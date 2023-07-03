<?php
require __DIR__ . '/../vendor/autoload.php';

use Hatch\Nacos\NacosClient;

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

$client = new NacosClient($options);
$client->configs->listen();
