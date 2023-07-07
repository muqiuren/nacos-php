<?php

namespace Hatch\Nacos\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Exception;
use Hatch\Nacos\Exception\BadRequestException;
use Hatch\Nacos\Exception\RequestException;

class Request extends Singleton
{
    /**
     * request config
     * @var array
     */
    protected static $config = [];

    /**
     * guzzle http client
     * @var Client
     */
    protected static $client;

    /**
     * 初始化
     */
    protected static function _initialize()
    {
        self::$config = [
            RequestOptions::TIMEOUT => 20.0,
            RequestOptions::CONNECT_TIMEOUT => 10.0,
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::HEADERS => ['Content-Type' => 'application/json; charset=utf-8']
        ];

        self::$client = new Client(self::$config);
    }

    /**
     * rsync request
     * @param string $url
     * @param string $method
     * @param array $options
     * @return mixed
     */
    public function request(string $url, string $method = 'GET', array $options = [])
    {
        try {
            $response = self::$client->request($method, $url, $options);
            $statusCode = $response->getStatusCode();

            if ($statusCode != Constant::HTTP_OK) {
                throw new BadRequestException('[Get Config]:The http status code is ' . $statusCode);
            }
            return $response->getBody()->getContents();
        } catch (GuzzleException | Exception $e) {
            new RequestException($e);
            return '';
        }
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return false|mixed|void
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists(self::$client, $name)) {
            return call_user_func([self::$client, $name], ...$arguments);
        }
    }
}
