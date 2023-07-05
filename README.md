# Nacos的php客户端库

[![License](http://poser.pugx.org/muqiuren/nacos-php/license)](https://packagist.org/packages/muqiuren/nacos-php)
[![Total Downloads](http://poser.pugx.org/muqiuren/nacos-php/downloads)](https://packagist.org/packages/muqiuren/nacos-php)
[![Latest Stable Version](http://poser.pugx.org/muqiuren/nacos-php/v)](https://packagist.org/packages/muqiuren/nacos-php)
[![Latest Unstable Version](http://poser.pugx.org/muqiuren/nacos-php/v/unstable)](https://packagist.org/packages/muqiuren/nacos-php)

最近需要用到nacos，项目是php编写的，看了很多开源的轮子，要么不支持username&password模式，要么就是深度集成到框架中，要么就是各种乱七八糟的错误，所以打算自己接入[nacos](https://nacos.io/)。

[Nacos的open api](https://nacos.io/zh-cn/docs/open-api.html)

### 特点

1. 简单易懂
2. 支持username&password模式
3. 现已支持获取和监听配置
4. 后续积极开发支持

### 安装

```powershell
composer require muqiuren/nacos-php
```

### 使用

1. 获取配置

```php
use Hatch\Nacos\NacosClient;

$options = [
    // nacos服务端地址
    'host' => '127.0.0.1',
    // nacos服务端端口
    'port' => 8848,
    // 命名空间id
    'namespace_id' => '1e7b3de6-7edb-4329-9184-46582480063b',
    // 配置id
    'data_id' => 'php_env_config',
    // 配置组
    'group' => 'test',
    // nacos用户名
    'username' => 'admin',
    // nacos密码
    'password' => 'admin',
];

$client = new NacosClient($options);
// 获取配置
$conf = $client->configs->get();
var_dump(conf);
```

2. 监听配置

```php
use Hatch\Nacos\NacosClient;

$options = [
    // nacos服务端地址
    'host' => '127.0.0.1',
    // nacos服务端端口
    'port' => 8848,
    // 命名空间id
    'namespace_id' => '1e7b3de6-7edb-4329-9184-46582480063b',
    // 配置id
    'data_id' => 'php_env_config',
    // 配置组
    'group' => 'test',
    // nacos用户名
    'username' => 'admin',
    // nacos密码
    'password' => 'admin',
    // 自动保存的文件地址
    'save_config_path' => '.env',
];

$client = new NacosClient($options);
// 启动监听，会阻塞当前进程
$client->configs->listen(function($newMd5) {
    var_dump($newMd5);
});
```

### TODO

- [x] 增强异常处理与容错
- [x] 增强日志输出
- [ ] Open Api接口对接
- [ ] 接入单元测试
- [ ] 提供更多示例


### OpenApi对接进度

#### 配置中心

- [x] 获取配置
- [x] 监听配置
- [ ] 发布配置
- [ ] 删除配置
- [ ] 查询历史版本
- [ ] 查询历史版本详情
- [ ] 查询配置上一版本信息

#### 服务发现

- [ ] 注册实例
- [ ] 注销实例
- [ ] 修改实例
- [ ] 查询实例列表
- [ ] 查询实例详情
- [ ] 发送实例心跳
- [ ] 创建服务
- [ ] 删除服务
- [ ] 修改服务
- [ ] 查询服务
- [ ] 查询服务列表
- [ ] 查询系统开关
- [ ] 修改系统开关
- [ ] 查看系统当前数据指标
- [ ] 查看当前集群Server列表
- [ ] 查看当前集群leader
- [ ] 更新实例的健康状态

#### 命名空间

- [ ] 查询命名空间列表
- [ ] 创建命名空间
- [ ] 修改命名空间
- [ ] 删除命名空间


