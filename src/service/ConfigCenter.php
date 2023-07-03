<?php
namespace Hatch\Nacos\Service;

use GuzzleHttp\RequestOptions;
use Hatch\Nacos\Exception\AuthException;
use Hatch\Nacos\Exception\RequestException;

class ConfigCenter extends BaseService
{
    const LINE_SEPARATOR = "\x01";
    const WORD_SEPARATOR = "\x02";
    const KEY_SEPARATOR  = "\x03";
    const LISTENER_CONFIG = 'Listening-Configs';
    /** @var string 获取配置 */
    const API_GET_CONFIG =  '/cs/configs';
    /** @var string 监听配置 */
    const API_LISTENER_CONFIG =  '/cs/configs/listener';
    /** @var string 发布配置 */
    const API_POST_CONFIG =  '/cs/configs';
    /** @var string 删除配置 */
    const API_DELETE_CONFIG =  '/cs/configs';
    /** @var string 查询历史版本 */
    const API_GET_HISTORY =  '/cs/history?search=accurate';
    /** @var string 查询历史版本详情 */
    const API_GET_HISTORY_INFO =  '/cs/history';
    /** @var string 查询配置上一版本信息 */
    const API_GET_HISTORY_PREVIOUS =  '/cs/history/previous';
    /** @var array 监听者map */
    private $listenerMap = [];

    /**
     * 获取配置
     * @param string $data_id
     * @param string $group
     * @param string $tenant
     * @return mixed
     * @throws AuthException
     * @throws RequestException
     */
    public function get(string $data_id = '', string $group = '', string $tenant = '')
    {
        $query = [
            'dataId' => $data_id ?: self::$data_id,
            'group' => $group ?: self::$group,
        ];

        if ($tenant || self::$namespace_id) {
            $query['tenant'] = $tenant ?: self::$namespace_id;
        }

        $url = $this->buildRequestUrl('/cs/configs', $query);
        return $this->httpClient->request($url);
    }

    /**
     * 监听配置
     * @param string $data_id
     * @param string $group
     * @param string $content_md5
     * @param string $tenant
     */
    public function listen(string $data_id = '', string $group = '', string $content_md5 = '', string $tenant = '')
    {
        $timeout = 30;
        $params = [
            'data_id' => $data_id ?: self::$data_id,
            'group' => $group ?: self::$group,
        ];

        if ($content_md5) {
            $params['md5'] = $content_md5;
        } else {
            $content = $this->get($data_id, $group, $tenant);
            $params['md5'] = md5($content);
        }

        $tenant = $tenant ?: self::$namespace_id;
        if ($tenant) {
            $params['tenant'] = $tenant;
            $formParam['tenant'] = $tenant;
        }
        $listenerKey = $this->getListenerKey($params);

        $configStr = implode(self::WORD_SEPARATOR, array_values($params)) . self::LINE_SEPARATOR;
        $formParam[self::LISTENER_CONFIG] = $configStr;
        $this->listenerMap[$listenerKey] = $params + $formParam;
        $url = $this->buildRequestUrl('/cs/configs/listener');
        $options = [
            RequestOptions::TIMEOUT => self::$timeout + $timeout,
            RequestOptions::FORM_PARAMS => $formParam,
            RequestOptions::HEADERS => [
                'Long-Pulling-Timeout' => $timeout * 1000
            ],
        ];
        do {
            $this->log->record('long pull...');
            if (isset($this->listenerMap[$listenerKey])) {
                $options[RequestOptions::FORM_PARAMS][self::LISTENER_CONFIG] = $this->listenerMap[$listenerKey][self::LISTENER_CONFIG];
            }

            $response = $this->httpClient->request($url, 'POST', $options);
            if ($response) {
                $this->syncConfig($listenerKey);
            }
        } while(true);
    }

    /**
     * 同步配置
     * @param string $listenerKey
     * @throws AuthException
     * @throws RequestException
     */
    private function syncConfig(string $listenerKey)
    {
        $params = $this->listenerMap[$listenerKey];
        $content = $this->get($params['data_id'], $params['group'], $params['tenant']);
        if (!$content) {
            sleep(2);
            return;
        }
        $contentMd5 = md5($content);
        if (!empty(self::$save_config_path)) {
            $this->log->record('write config:' . $contentMd5);
            file_put_contents(self::$save_config_path, $content);
        }
        $this->listenerMap[$listenerKey]['md5'] = $contentMd5;
        $this->listenerMap[$listenerKey][self::LISTENER_CONFIG] = sprintf(
            '%s%s%s%s',
            $params['data_id'] . self::WORD_SEPARATOR,
            $params['group'] . self::WORD_SEPARATOR,
            $contentMd5 . self::WORD_SEPARATOR,
            $params['tenant'] . self::LINE_SEPARATOR
        );
    }

    /**
     * 获取listener key
     * @param array $params
     * @return string
     */
    private function getListenerKey(array $params): string
    {
        return implode(self::KEY_SEPARATOR, $params);
    }
}
