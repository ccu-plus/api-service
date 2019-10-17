<?php

namespace App\Http\Validators;

use App\Http\Validators\Rules\Decryptable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as BaseValidator;

final class SignUpValidator
{
    /**
     * 註冊表單驗證器.
     *
     * @param Request $request
     *
     * @return array
     */
    public static function make(Request $request): array
    {
        $v = BaseValidator::make($request->all(), [
            'nickname' => 'bail|required|string|between:3,12|unique:users',
            'email' => 'bail|required|email|max:48|unique:users',
            'token' => ['required', 'string', new Decryptable],
        ]);

        return  $v->validated();
    }
}
