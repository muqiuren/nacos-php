<?php
namespace Hatch\Nacos\Exception;

use Hatch\Nacos\Service\Logger;
use Throwable;

class RequestException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        (Logger::getInstance())->record($message);
    }
}
