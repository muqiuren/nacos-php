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
$testDataId = "test_publish_data_id";
$testGroup = "test_publish_group";
$client = new NacosClient($options);

// 获取配置
printLog('获取配置 START');
$conf = $client->configs->get();
print_r($conf);
printLog('获取配置 END');

// 发布配置
printLog('发布配置 START');
$result = $client->configs->publish($conf, $testDataId, $testGroup);
print_r($result);
printLog('发布配置 END');

// 删除配置
printLog('删除配置 START');
$result = $client->configs->destroy($testDataId, $testGroup);
print_r($result);
printLog('删除配置 END');

// 查询历史版本
printLog('查询历史版本 START');
$list = $client->configs->history(1, 10);
print_r($list);
printLog('查询历史版本 END');

// 查询历史版本详情
printLog('查询历史版本详情 START');
$result = $client->configs->historyInfo(1);
print_r($result);
printLog('查询历史版本详情 END');

// 监听配置
printLog('监听配置 START');
$client->configs->listen(function($newMd5) {
    print_r("new config:" . $newMd5 . PHP_EOL);
});
printLog('监听配置 END');
