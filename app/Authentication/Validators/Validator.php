<?php

declare(strict_types=1);

namespace App\Authentication\Validators;

abstract class Validator
{
    /**
     * 驗證帳號格式.
     */
    abstract public function valid(string $username): bool;
}
