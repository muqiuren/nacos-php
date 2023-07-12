<?php
namespace Hatch\Nacos;

use Hatch\Nacos\Service\BaseService;
use Hatch\Nacos\Service\ConfigCenter;
use Hatch\Nacos\Service\NamespaceCenter;
use Hatch\Nacos\Service\ServerDiscover;

class NacosClient
{
    /** @var array 默认配置 */
    protected $options = [
        'host' => '127.0.0.1',
        'port' => 8848,
        'namespace_id' => '',
        'data_id' => '',
        'group' => '',
        'username' => '',
        'password' => '',
        'timeout' => 3
    ];

    /** @var ConfigCenter 配置中心 */
    public $configs;
    /** @var NamespaceCenter 命名空间 */
    public $namespaces;
    /** @var ServerDiscover 服务发现 */
    public $servers;

    /**
     * construct func
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->options = array_merge($this->options, $config);

        foreach ($this->options as $k => $v)
        {
            if (!property_exists(BaseService::class, $k)) {
                continue;
            }

            $method = 'set' . str_replace('_', '', ucwords($k, '_'));
            if (method_exists(BaseService::class, $method)) {
                BaseService::$method($v);
            } else {
                BaseService::$$k = $v;
            }
        }

        $this->configs = new ConfigCenter();
        $this->namespaces = new NamespaceCenter();
        $this->servers = new ServerDiscover();
    }
}


