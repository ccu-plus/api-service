<?php

declare(strict_types=1);

namespace App\Http\Validators\Rules;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Validation\Rule;

final class Decryptable implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (! is_string($value)) {
            return false;
        }

        try {
            decrypt($value);
        } catch (DecryptException $decryptException) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return 'Invalid payload.';
    }
}
