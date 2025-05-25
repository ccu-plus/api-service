<?php

namespace App\Authentication\Validators;

abstract class Validator
{
    /**
     * 驗證帳號格式.
     *
     * @param string $username
     *
     * @return bool
     */
    abstract public function valid(string $username): bool;
}
