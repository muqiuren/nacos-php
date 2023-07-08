<?php
namespace Hatch\Nacos\Service;

use GuzzleHttp\RequestOptions;
use Hatch\Nacos\Exception\AuthException;
use Hatch\Nacos\Exception\ValidateException;

class ServerDiscover extends BaseService
{
    /** @var string[] 允许参数 */
    private $all_allow_fields = [
        'namespace_id' => 'string',
        'weight' => 'float|int',
        'enabled' => 'boolean',
        'healthy' => 'boolean',
        'metadata' => 'string',
        'cluster_name' => 'string',
        'group_name' => 'string',
        'ephemeral' => 'boolean',
        'clusters' => 'string',
        'healthy_only' => 'boolean',
        'protect_threshold' => 'float|int',
        'selector' => 'string',
        'debug' => 'boolean',
    ];

    /**
     * 构建参数
     * @param array $params
     * @param array $options
     * @param array $allow_fields
     * @throws ValidateException
     */
    private function buildParams(array &$params, array $options, array $allow_fields = [])
    {
        $allow_fields = $allow_fields ?: array_keys($this->all_allow_fields);
        foreach ($options as $k => $v)
        {
            if (!isset($allow_fields[$k])) {
                continue;
            }

            $type = gettype($v);
            $typeArr = explode('|', $this->all_allow_fields[$k]);
            if (!in_array($type, $typeArr)) {
                throw new ValidateException(sprintf('The field [%s] type is incorrect, it must be of [%s] type', $k, $this->all_allow_fields[$k]));
            }

            $params[$k] = $v;
        }

        if (!isset($params['namespace_id']) && self::$namespace_id) {
            $params['namespace_id'] = self::$namespace_id;
        }
    }

    /**
     * 注册实例
     * @param string $service_name
     * @param string $ip
     * @param int $port
     * @param array $options
     * @return mixed|string
     * @throws AuthException
     * @throws ValidateException
     */
    public function registerInstance(string $service_name, string $ip, int $port, array $options = [])
    {
        $params = [
            'serviceName' => $service_name,
            'ip' => $ip,
            'port' => $port,
        ];

        $this->buildParams($params, $options, [
            'namespace_id', 'weight', 'enabled', 'healthy',
            'metadata', 'cluster_name', 'group_name', 'ephemeral',
        ]);
        $url = $this->buildRequestUrl('/ns/instance');
        $options = $this->buildRequestOptions([
            RequestOptions::FORM_PARAMS => $params
        ]);

        return $this->httpClient->request($url, 'POST', $options);
    }

    /**
     * 注销实例
     * @param string $service_name
     * @param string $ip
     * @param int $port
     * @param array $options
     * @return mixed|string
     * @throws AuthException
     * @throws ValidateException
     */
    public function destroyInstance(string $service_name, string $ip, int $port, array $options = [])
    {
        $params = [
            'serviceName' => $service_name,
            'ip' => $ip,
            'port' => $port,
        ];

        $this->buildParams($params, $options, [
            'namespace_id', 'cluster_name', 'group_name', 'ephemeral'
        ]);
        $url = $this->buildRequestUrl('/ns/instance');
        $options = $this->buildRequestOptions([
            RequestOptions::FORM_PARAMS => $params
        ]);

        return $this->httpClient->request($url, 'DELETE', $options);
    }

    /**
     * 修改实例
     * @param string $service_name
     * @param string $ip
     * @param int $port
     * @param array $options
     * @return mixed|string
     * @throws AuthException
     * @throws ValidateException
     */
    public function updateInstance(string $service_name, string $ip, int $port, array $options = [])
    {
        $params = [
            'serviceName' => $service_name,
            'ip' => $ip,
            'port' => $port,
        ];
        $this->buildParams($params, $options, [
            'namespace_id', 'weight', 'enabled', 'healthy',
            'metadata', 'cluster_name', 'group_name', 'ephemeral',
        ]);
        $url = $this->buildRequestUrl('/ns/instance');
        $options = $this->buildRequestOptions([
            RequestOptions::FORM_PARAMS => $params
        ]);

        return $this->httpClient->request($url, 'PUT', $options);
    }

    /**
     * 查询实例列表
     * @param string $service_name
     * @param array $options
     * @return mixed|string
     * @throws AuthException
     * @throws ValidateException
     */
    public function getInstanceList(string $service_name, array $options = [])
    {
        $query = [
            'serviceName' => $service_name,
        ];
        $this->buildParams($query, $options, [
            'group_name', 'namespace_id', 'clusters', 'healthy_only'
        ]);
        $url = $this->buildRequestUrl('/ns/instance/list', $query);
        $options = $this->buildRequestOptions();

        return $this->httpClient->request($url, 'GET', $options);
    }

    /**
     * 查询实例详情
     * @param string $service_name
     * @param string $ip
     * @param int $port
     * @param array $options
     * @return mixed|string
     * @throws AuthException
     * @throws ValidateException
     */
    public function getInstance(string $service_name, string $ip, int $port, array $options = [])
    {
        $query = [
            'serviceName' => $service_name,
            'ip' => $ip,
            'port' => $port,
        ];
        $this->buildParams($query, $options, [
            'group_name', 'namespace_id', 'cluster', 'healthy_only', 'ephemeral'
        ]);
        $url = $this->buildRequestUrl('/ns/instance', $query);
        $options = $this->buildRequestOptions();

        return $this->httpClient->request($url, 'GET', $options);
    }

    /**
     * 发送实例心跳
     * @param string $service_name
     * @param string $ip
     * @param int $port
     * @param string $beat
     * @param array $options
     * @return mixed|string
     * @throws AuthException
     * @throws ValidateException
     */
    public function beat(string $service_name, string $ip, int $port, string $beat, array $options = [])
    {
        $params = [
            'serviceName' => $service_name,
            'ip' => $ip,
            'port' => $port,
            'beat' => $beat,
        ];
        $this->buildParams($params, $options, [
            'group_name', 'namespace_id', 'ephemeral'
        ]);
        $url = $this->buildRequestUrl('/ns/instance/beat');
        $options = $this->buildRequestOptions([
            RequestOptions::FORM_PARAMS => $params
        ]);

        return $this->httpClient->request($url, 'PUT', $options);
    }

    /**
     * 创建服务
     * @param string $service_name
     * @param array $options
     * @return mixed|string
     * @throws AuthException
     * @throws ValidateException
     */
    public function createService(string $service_name, array $options = [])
    {
        $params = [
            'serviceName' => $service_name,
        ];
        $this->buildParams($params, $options, [
            'group_name', 'namespace_id', 'protect_threshold', 'metadata', 'selector'
        ]);
        $url = $this->buildRequestUrl('/ns/service');
        $options = $this->buildRequestOptions([
            RequestOptions::FORM_PARAMS => $params
        ]);

        return $this->httpClient->request($url, 'POST', $options);
    }

    /**
     * 删除服务
     * @param string $service_name
     * @param array $options
     * @return mixed|string
     * @throws AuthException
     * @throws ValidateException
     */
    public function destroyService(string $service_name, array $options = [])
    {
        $params = [
            'serviceName' => $service_name,
        ];
        $this->buildParams($params, $options, [
            'group_name', 'namespace_id',
        ]);
        $url = $this->buildRequestUrl('/ns/service');
        $options = $this->buildRequestOptions([
            RequestOptions::FORM_PARAMS => $params
        ]);

        return $this->httpClient->request($url, 'DELETE', $options);
    }

    /**
     * 修改服务
     * @param string $service_name
     * @param array $options
     * @return mixed|string
     * @throws AuthException
     * @throws ValidateException
     */
    public function updateService(string $service_name, array $options = [])
    {
        $params = [
            'serviceName' => $service_name,
        ];
        $this->buildParams($params, $options, [
            'group_name', 'namespace_id', 'protect_threshold', 'metadata', 'selector',
        ]);
        $url = $this->buildRequestUrl('/ns/service');
        $options = $this->buildRequestOptions([
            RequestOptions::FORM_PARAMS => $params
        ]);

        return $this->httpClient->request($url, 'PUT', $options);
    }

    /**
     * 查询服务
     * @param string $service_name
     * @param array $options
     * @return mixed|string
     * @throws AuthException
     * @throws ValidateException
     */
    public function getService(string $service_name, array $options = [])
    {
        $query = [
            'serviceName' => $service_name,
        ];
        $this->buildParams($query, $options, [
            'group_name', 'namespace_id',
        ]);
        $url = $this->buildRequestUrl('/ns/service', $query);
        $options = $this->buildRequestOptions();

        return $this->httpClient->request($url, 'GET', $options);
    }

    /**
     * 查询服务列表
     * @param int $page
     * @param int $page_size
     * @param array $options
     * @return mixed|string
     * @throws AuthException
     * @throws ValidateException
     */
    public function getServiceList(int $page = 1, int $page_size = 10, array $options = [])
    {
        $query = [
            'pageNo' => $page >= 1 ? $page : 1,
            'pageSize' => ($page_size >= 1 && $page_size <= 500) ? $page_size : 10,
        ];
        $this->buildParams($query, $options, [
            'group_name', 'namespace_id',
        ]);
        $url = $this->buildRequestUrl('/ns/service/list', $query);
        $options = $this->buildRequestOptions();

        return $this->httpClient->request($url, 'GET', $options);
    }

    /**
     * 查询系统开关
     * @return mixed|string
     * @throws AuthException
     */
    public function getSysSwitch()
    {
        $url = $this->buildRequestUrl('/ns/operator/switches');
        $options = $this->buildRequestOptions();

        return $this->httpClient->request($url, 'GET', $options);
    }

    /**
     * 修改系统开关
     * @param string $entry
     * @param string $value
     * @param array $options
     * @return mixed|string
     * @throws AuthException
     * @throws ValidateException
     */
    public function updateSysSwitch(string $entry, string $value, array $options = [])
    {
        $params = [
            'entry' => $entry,
            'value' => $value,
        ];
        $this->buildParams($params, $options, [
            'debug',
        ]);
        $url = $this->buildRequestUrl('/ns/operator/switches');
        $options = $this->buildRequestOptions([
            RequestOptions::FORM_PARAMS => $params,
        ]);

        return $this->httpClient->request($url, 'PUT', $options);
    }

    /**
     * 查看系统当前数据指标
     * @return mixed|string
     * @throws AuthException
     */
    public function getSysMetrics()
    {
        $url = $this->buildRequestUrl('/ns/operator/metrics');
        $options = $this->buildRequestOptions();

        return $this->httpClient->request($url, 'GET', $options);
    }

    /**
     * 查看当前集群Server列表
     * @param array $options
     * @return mixed|string
     * @throws AuthException
     * @throws ValidateException
     */
    public function getClusterServices(array $options = [])
    {
        $query = [];
        $this->buildParams($query, $options, [
            'healthy',
        ]);
        $url = $this->buildRequestUrl('/ns/operator/servers', $query);
        $options = $this->buildRequestOptions();

        return $this->httpClient->request($url, 'GET', $options);
    }

    /**
     * 查看当前集群leader
     * @return mixed|string
     * @throws AuthException
     */
    public function getClusterLeader()
    {
        $url = $this->buildRequestUrl('/ns/raft/leader');
        $options = $this->buildRequestOptions();

        return $this->httpClient->request($url, 'GET', $options);
    }

    /**
     * 更新实例的健康状态
     * @param string $service_name
     * @param string $ip
     * @param int $port
     * @param bool $healthy
     * @param array $options
     * @return mixed|string
     * @throws AuthException
     * @throws ValidateException
     */
    public function updateInstanceHealth(string $service_name, string $ip, int $port, bool $healthy, array $options = [])
    {
        $params = [
            'serviceName' => $service_name,
            'ip' => $ip,
            'port' => $port,
            'healthy' => $healthy,
        ];
        $this->buildParams($params, $options, [
            'namespace_id', 'group_name', 'cluster_name'
        ]);
        $url = $this->buildRequestUrl('/ns/health/instance');
        $options = $this->buildRequestOptions([
            RequestOptions::FORM_PARAMS => $params
        ]);

        return $this->httpClient->request($url, 'PUT', $options);
    }
}
