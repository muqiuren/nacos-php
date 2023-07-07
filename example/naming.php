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
$testNamespaceId = 'f0816beb-0b5d-489f-b5f9-ec6dee048ecf';
$testNamespaceName = 'php_namespace';
$testNamespaceDesc = 'this is example';
$client = new NacosClient($options);

// 查询命名空间列表
printLog('查询命名空间列表 START');
$list = $client->namespaces->getList();
print_r($list);
printLog('查询命名空间列表 END');

// 创建命名空间
printLog('创建命名空间 START');
$result = $client->namespaces->create($testNamespaceId, $testNamespaceName, $testNamespaceDesc);
print_r($result);
printLog('创建命名空间 END');

// 修改命名空间
printLog('修改命名空间 START');
$result = $client->namespaces->update($testNamespaceId, $testNamespaceName, $testNamespaceDesc);
print_r($result);
printLog('修改命名空间 END');

// 删除命名空间
printLog('删除命名空间 START');
$result = $client->namespaces->destroy($testNamespaceId);
print_r($result);
printLog('删除命名空间 END');
