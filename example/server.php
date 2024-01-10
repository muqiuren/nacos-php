<?php
require __DIR__ . '/../vendor/autoload.php';

use Hatch\Nacos\NacosClient;

function printLog(string $message)
{
    print(PHP_EOL . str_repeat('=', 30) . " {$message} " . str_repeat('=', 30) . PHP_EOL);
}

$options = [
    'host' => '127.0.0.1',
    'port' => 8848,
    'namespace_id' => '1e7b3de6-7edb-4329-9184-46582480063b',
    'data_id' => 'php_env_config',
    'group' => 'test',
    'username' => 'admin',
    'password' => 'admin',
    'save_config_path' => '.env',
];
$testServiceName = 'test_service_name';
$testServiceIp = '127.0.0.1';
$testServicePort = 8080;
$testSysSwitchEntry = 'test_sys_entry';
$testSysSwitchValue = 'test_sys_value';
$client = new NacosClient($options);

// 注册实例
printLog('注册实例 START');
$result = $client->servers->registerInstance($testServiceName, $testServiceIp, $testServicePort, [
    'weight' => 1.0,
    'enabled' => true,
    'healthy' => true
]);
print_r($result);
printLog('注册实例 END');

// 注销实例
printLog('注销实例 START');
$result = $client->servers->destroyInstance($testServiceName, $testServiceIp, $testServicePort, [
    'weight' => 1.0,
    'enabled' => true,
    'healthy' => true
]);
print_r($result);
printLog('注销实例 END');

// 修改实例
printLog('修改实例 START');
$result = $client->servers->updateInstance($testServiceName, $testServiceIp, $testServicePort, [
    'weight' => 1.0,
    'enabled' => false,
    'healthy' => true
]);
print_r($result);
printLog('修改实例 END');

// 查询实例列表
printLog('查询实例列表 START');
$list = $client->servers->getInstanceList($testServiceName);
print_r($list);
printLog('查询实例列表 END');

// 查询实例详情
printLog('查询实例详情 START');
$list = $client->servers->getInstance($testServiceName, $testServiceIp, $testServicePort);
print_r($list);
printLog('查询实例详情 END');

// 发送实例心跳
printLog('发送实例心跳 START');
// beat example: {"cluster":"DEFAULT","ip":"xxx","metadata":{},"port":xxx,"scheduled":true,"serviceName":"nacos-php","weight":1}
$result = $client->servers->beat($testServiceName, json_encode(['ip' => $testServiceIp, 'port' => $testServicePort, 'serviceName' => $testServiceName]));
print_r($result);
printLog('发送实例心跳 END');

// 创建服务
printLog('创建服务 START');
$result = $client->servers->createService($testServiceName);
print_r($result);
printLog('创建服务 END');

// 删除服务
printLog('删除服务 START');
$result = $client->servers->destroyService($testServiceName);
print_r($result);
printLog('删除服务 END');

// 修改服务
printLog('修改服务 START');
$result = $client->servers->updateService($testServiceName, [
    'metadata' => 'test service',
]);
print_r($result);
printLog('修改服务 END');

// 查询服务
printLog('查询服务 START');
$result = $client->servers->getService($testServiceName);
print_r($result);
printLog('查询服务 END');

// 查询服务列表
printLog('查询服务列表 START');
$list = $client->servers->getServiceList(1, 20);
print_r($list);
printLog('查询服务列表 END');

// 查询系统开关
printLog('查询系统开关 START');
$result = $client->servers->getSysSwitch();
print_r($result);
printLog('查询系统开关 END');

// 修改系统开关
printLog('修改系统开关 START');
$result = $client->servers->updateSysSwitch($testSysSwitchEntry, $testSysSwitchValue);
print_r($result);
printLog('修改系统开关 END');

// 查看系统当前数据指标
printLog('查看系统当前数据指标 START');
$result = $client->servers->getSysMetrics();
print_r($result);
printLog('查看系统当前数据指标 END');

// 查看当前集群Server列表
printLog('查看当前集群Server列表 START');
$list = $client->servers->getClusterServices();
print_r($list);
printLog('查看当前集群Server列表 END');

// 查看当前集群leader
printLog('查看当前集群leader START');
$result = $client->servers->getClusterLeader();
print_r($result);
printLog('查看当前集群leader END');

// 更新实例的健康状态
printLog('更新实例的健康状态 START');
$result = $client->servers->updateInstanceHealth($testServiceName, $testServiceIp, $testServicePort, true);
print_r($result);
printLog('更新实例的健康状态 END');
