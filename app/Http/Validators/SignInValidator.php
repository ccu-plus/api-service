<?php

declare(strict_types=1);

namespace App\Http\Validators;

use App\Http\Validators\Rules\Captcha;
use App\Http\Validators\Rules\IdentifyCardNumber;
use App\Http\Validators\Rules\StudentId;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as BaseValidator;
use Illuminate\Support\Fluent;

final class SignInValidator
{
    /**
     * 登入表單驗證器.
     *
     *
     */
    public static function make(Request $request): array
    {
        $v = BaseValidator::make($request->all(), [
            'captcha' => ['required', new Captcha],
            'password' => 'required|string',
            'type' => 'required|in:alumni,portal',
        ]);

        $v->sometimes('username', ['required', new StudentId], function (Fluent $input): bool {
            return $input->get('type') === 'portal';
        });

        $v->sometimes('username', ['required', new IdentifyCardNumber], function (Fluent $input): bool {
            return $input->get('type') === 'alumni';
        });

        return $v->validated();
    }
}
