<?php

namespace App\Http\Validators\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Cache;

final class Captcha implements Rule
{

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!is_string($value)) {
            return false;
        } else if (preg_match('/[0-9a-f]{32}\.\d{5}/', $value) !== 1) {
            return false;
        }

        [$key, $secret] = explode('.', $value);

        return Cache::pull($key) === $secret;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return '驗證碼錯誤';
    }
}
