<?php

declare(strict_types=1);

namespace App\Http\Validators\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Cache;

final class Captcha implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('驗證碼錯誤');
        }

        if (preg_match('/[0-9a-f]{32}\.\d{5}/', $value) !== 1) {
            $fail('驗證碼錯誤');
        }

        [$key, $secret] = explode('.', $value);

        if (Cache::pull($key) !== $secret) {
            $fail('驗證碼錯誤');
        }
    }
}
