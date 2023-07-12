# Nacos的php客户端库

[![PHP Version Require](http://poser.pugx.org/muqiuren/nacos-php/require/php)](https://packagist.org/packages/muqiuren/nacos-php)
[![License](http://poser.pugx.org/muqiuren/nacos-php/license)](https://packagist.org/packages/muqiuren/nacos-php)
[![Total Downloads](http://poser.pugx.org/muqiuren/nacos-php/downloads)](https://packagist.org/packages/muqiuren/nacos-php)
[![Latest Stable Version](http://poser.pugx.org/muqiuren/nacos-php/v)](https://packagist.org/packages/muqiuren/nacos-php)

最近需要用到nacos，项目是php编写的，看了很多开源的轮子，要么不支持username&password模式，要么就是深度集成到框架中，要么就是各种乱七八糟的错误，所以打算自己接入[nacos](https://nacos.io/)。

什么是[Nacos](https://nacos.io/zh-cn/docs/what-is-nacos.html)

Nacos的[open api](https://nacos.io/zh-cn/docs/open-api.html)

### 特点

1. 简单易用
2. 支持username&password鉴权模式
3. 支持调用所有Nacos Open Api接口
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
更多示例请看example目录提供的调用示例

### TODO

- [x] 增强异常处理与容错
- [x] 增强日志输出
- [x] Open Api接口对接
- [x] 提供更多示例
- [ ] 接入单元测试


### OpenApi接口支持列表

#### 配置中心

- [x] 获取配置
- [x] 监听配置
- [x] 发布配置
- [x] 删除配置
- [x] 查询历史版本
- [x] 查询历史版本详情
- [x] 查询配置上一版本信息

#### 服务发现

- [x] 注册实例
- [x] 注销实例
- [x] 修改实例
- [x] 查询实例列表
- [x] 查询实例详情
- [x] 发送实例心跳
- [x] 创建服务
- [x] 删除服务
- [x] 修改服务
- [x] 查询服务
- [x] 查询服务列表
- [x] 查询系统开关
- [x] 修改系统开关
- [x] 查看系统当前数据指标
- [x] 查看当前集群Server列表
- [x] 查看当前集群leader
- [x] 更新实例的健康状态

#### 命名空间

- [x] 查询命名空间列表
- [x] 创建命名空间
- [x] 修改命名空间
- [x] 删除命名空间


