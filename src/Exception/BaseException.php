<?php
declare(strict_types=1);

namespace Hatch\Nacos\Exception;

use Hatch\Nacos\Service\Logger;
use Throwable;

class BaseException extends \Exception
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct($previous->getMessage(), $previous->getCode(), $previous);
        (Logger::getInstance())->exception($previous);
    }
}
