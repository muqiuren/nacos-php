<?php
declare(strict_types=1);

namespace Hatch\Nacos\Exception;

class AuthException extends BaseException
{
    public function __construct(string $message)
    {
        $this->message = $message;
        parent::__construct($this);
    }
}
