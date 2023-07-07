<?php

namespace Hatch\Nacos\Service;

use GuzzleHttp\RequestOptions;
use Hatch\Nacos\Exception\AuthException;

abstract class BaseService
{
    /** @var string nacos目录 */
    public static $base_dir = '/nacos';
    /** @var string 接口版本 */
    public static $version = 'v1';
    /** @var bool 是否需要鉴权 */
    public static $need_auth = true;
    /** @var int 接口超时时间，单位秒 */
    public static $timeout = 6;
    /** @var string 保存配置文件地址 */
    public static $save_config_path = '';
    /** @var string host */
    public static $host;
    /** @var int 端口 */
    public static $port;
    /** @var string 命令空间 */
    public static $namespace_id;
    /** @var string 配置id */
    public static $data_id;
    /** @var string 配置组 */
    public static $group = 'DEFAULT_GROUP';
    /** @var string 用户名 */
    public static $username;
    /** @var string 密码 */
    protected static $password;
    /** @var array 令牌信息 */
    private $tokenInfo;
    /** @var Logger 日志实例 */
    protected $log;
    /** @var Request http客户端 */
    protected $httpClient;

    /**
     * construct func
     */
    public function __construct()
    {
        $this->log = Logger::getInstance();
        $this->httpClient = Request::getInstance();
    }

    /**
     * 设置登录密码
     * @param string $password
     */
    public static function setPassword(string $password)
    {
        self::$password = $password;
    }

    /**
     * 获取请求链接
     * @param string $endpoint
     * @return string
     */
    protected function getRequestUrl(string $endpoint): string
    {
        return sprintf('http://%s:%s', self::$host, self::$port . self::$base_dir . '/' . self::$version . $endpoint);
    }

    /**
     * 构建请求链接
     * @param string $endpoint
     * @param array $query
     * @return string
     * @throws AuthException
     */
    protected function buildRequestUrl(string $endpoint, array &$query = []): string
    {
        $url = $this->getRequestUrl($endpoint);
        if (self::$need_auth) {
            $accessToken = $this->getAccessToken();
            if ($accessToken) {
                $query['accessToken'] = $accessToken;
            }
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }

    /**
     * 构建请求配置
     * @param array $options
     * @return array
     */
    protected function buildRequestOptions(array $options = []): array
    {
        return array_merge([
            RequestOptions::TIMEOUT => self::$timeout,
        ], $options);
    }

    /**
     * 获取访问令牌
     * @return mixed
     * @throws AuthException
     */
    protected function getAccessToken()
    {
        if (empty($this->tokenInfo) || $this->tokenInfo['ttl'] <= time()) {
            $this->login();
        }

        return $this->tokenInfo['access_token'];
    }

    /**
     * 登录
     * @throws AuthException
     */
    private function login()
    {
        $options = [
            RequestOptions::FORM_PARAMS => [
                'username' => self::$username,
                'password' => self::$password,
            ]
        ];
        $response = $this->httpClient->request($this->getRequestUrl('/auth/login'), 'POST', $options);
        $result = json_decode($response, true);
        if (!isset($result['accessToken']) || !isset($result['tokenTtl'])) {
            throw new AuthException('[Login Error]:' . $response);
        }

        $this->tokenInfo = [
            'access_token' => $result['accessToken'] ?? '',
            'ttl' => time() + ($result['tokenTtl'] ?? 0) - 5
        ];
    }
}
