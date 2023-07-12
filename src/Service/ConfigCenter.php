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
    /** @var array 监听者map */
    private $listenerMap = [];

    /**
     * 获取配置
     * @param string $data_id
     * @param string $group
     * @param string $tenant
     * @return mixed
     * @throws AuthException
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
     * @param callable|null $callback
     * @param string $data_id
     * @param string $group
     * @param string $content_md5
     * @param string $tenant
     */
    public function listen(callable $callback = null, string $data_id = '', string $group = '', string $content_md5 = '', string $tenant = '')
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
        $options = $this->buildRequestOptions([
            RequestOptions::TIMEOUT => self::$timeout + $timeout,
            RequestOptions::FORM_PARAMS => $formParam,
            RequestOptions::HEADERS => [
                'Long-Pulling-Timeout' => $timeout * 1000
            ],
        ]);
        do {
            $this->log->record('long pull...');
            $url = $this->buildRequestUrl('/cs/configs/listener');
            if (isset($this->listenerMap[$listenerKey])) {
                $options[RequestOptions::FORM_PARAMS][self::LISTENER_CONFIG] = $this->listenerMap[$listenerKey][self::LISTENER_CONFIG];
            }

            $result = $this->httpClient->request($url, 'POST', $options);
            if ($result) {
                $newMd5 = $this->syncConfig($listenerKey);
                is_callable($callback) && $callback($newMd5);
            }
        } while(true);
    }

    /**
     * 同步配置
     * @param string $listenerKey
     * @return string
     * @throws AuthException
     * @throws RequestException
     */
    private function syncConfig(string $listenerKey): string
    {
        $params = $this->listenerMap[$listenerKey];
        $content = $this->get($params['data_id'], $params['group'], $params['tenant']);
        if (!$content) {
            sleep(2);
            return $params['md5'];
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

        return $contentMd5;
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

    /**
     * 发布配置
     * @param string $content
     * @param string $data_id
     * @param string $group
     * @param string $tenant
     * @return mixed
     * @throws AuthException
     */
    public function publish(string $content, string $data_id = '', string $group = '', string $tenant = '')
    {
        $params = [
            'content' => $content,
            'dataId' => $data_id ?: self::$data_id,
            'group' => $group ?: self::$group,
        ];

        if ($tenant || self::$namespace_id) {
            $params['tenant'] = $tenant ?: self::$namespace_id;
        }

        $url = $this->buildRequestUrl('/cs/configs');
        $options = $this->buildRequestOptions([
            RequestOptions::FORM_PARAMS => $params
        ]);

        return $this->httpClient->request($url, 'POST', $options);
    }

    /**
     * 删除配置
     * @param string $data_id
     * @param string $group
     * @param string $tenant
     * @return mixed
     * @throws AuthException
     */
    public function destroy(string $data_id = '', string $group = '', string $tenant = '')
    {
        $params = [
            'dataId' => $data_id ?: self::$data_id,
            'group' => $group ?: self::$group,
        ];

        if ($tenant || self::$namespace_id) {
            $params['tenant'] = $tenant ?: self::$namespace_id;
        }

        $url = $this->buildRequestUrl('/cs/configs');
        $options = $this->buildRequestOptions([
            RequestOptions::FORM_PARAMS => $params
        ]);

        return $this->httpClient->request($url, 'DELETE', $options);
    }

    /**
     * 查询历史版本
     * @param int $page
     * @param int $page_size
     * @param string $data_id
     * @param string $group
     * @param string $tenant
     * @return mixed|string
     * @throws AuthException
     */
    public function history(int $page = 1, int $page_size = 100, string $data_id = '', string $group = '', string $tenant = '')
    {
        $query = [
            'search' => 'accurate',
            'dataId' => $data_id ?: self::$data_id,
            'group' => $group ?: self::$group,
            'pageNo' => $page >= 1 ? $page : 1,
            'pageSize' => ($page_size >= 1 && $page_size <= 500) ? $page_size : 100,
        ];

        if ($tenant || self::$namespace_id) {
            $query['tenant'] = $tenant ?: self::$namespace_id;
        }

        $url = $this->buildRequestUrl('/cs/history', $query);
        $options = $this->buildRequestOptions();
        return $this->httpClient->request($url, 'GET', $options);
    }

    /**
     * 查询历史版本详情
     * @param int $nid
     * @param string $data_id
     * @param string $group
     * @param string $tenant
     * @return mixed|string
     * @throws AuthException
     */
    public function historyInfo(int $nid, string $data_id = '', string $group = '', string $tenant = '')
    {
        $query = [
            'nid' => $nid,
            'dataId' => $data_id ?: self::$data_id,
            'group' => $group ?: self::$group,
        ];

        if ($tenant || self::$namespace_id) {
            $query['tenant'] = $tenant ?: self::$namespace_id;
        }

        $url = $this->buildRequestUrl('/cs/history', $query);
        $options = $this->buildRequestOptions();
        return $this->httpClient->request($url, 'GET', $options);
    }

    /**
     * 查询配置上一版本信息
     * @param int $id
     * @param string $data_id
     * @param string $group
     * @param string $tenant
     * @return mixed|string
     * @throws AuthException
     */
    public function historyPrevInfo(int $id, string $data_id = '', string $group = '', string $tenant = '')
    {
        $query = [
            'id' => $id,
            'dataId' => $data_id ?: self::$data_id,
            'group' => $group ?: self::$group,
        ];

        if ($tenant || self::$namespace_id) {
            $query['tenant'] = $tenant ?: self::$namespace_id;
        }

        $url = $this->buildRequestUrl('/cs/history/previous', $query);
        $options = $this->buildRequestOptions();
        return $this->httpClient->request($url, 'GET', $options);
    }
}
