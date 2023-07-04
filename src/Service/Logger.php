<?php

namespace Hatch\Nacos\Service;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as LoggerService;
use Throwable;

class Logger extends Singleton
{
    /** @var LoggerService */
    private static $logger;

    /**
     * 初始化
     */
    protected static function _initialize()
    {
        self::$logger = new LoggerService('app');
        $dateFormat = 'Y-m-d H:i:s.u';
        $output = "[%datetime%] %channel% %level_name% %message% %context%\n";
        $formatter = new LineFormatter($output, $dateFormat);
        $handler = new StreamHandler('log/runtime.log', LoggerService::DEBUG);
        $handler->setFormatter($formatter);
        self::$logger->pushHandler($handler);
    }

    /**
     * 异常记录
     * @param Throwable $e
     * @param string $level
     */
    public function exception(Throwable $e, string $level = 'error')
    {
        $trace = $e->getTrace();
        $file = $e->getFile();
        $line = $e->getLine();
        $message = $e->getMessage();

        $this->$level(sprintf("%s(%s):%s", $file, $line, $message), compact('trace'));
    }

    /**
     * 日志记录
     * @param string $info
     * @param string $level
     * @param array $context
     */
    public function record(string $info, string $level = 'info', array $context = [])
    {
        $this->$level($info, $context);
    }

    /**
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        if (method_exists(self::$logger, $name)) {
            call_user_func_array([self::$logger, $name], $arguments);
        }
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        if (self::$logger instanceof LoggerService) {
            self::$logger->close();
        }
    }
}
