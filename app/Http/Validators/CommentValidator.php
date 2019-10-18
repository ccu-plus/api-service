<?php

namespace App\Http\Validators;

use App\Http\Validators\Rules\Captcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as BaseValidator;

final class CommentValidator
{
    /**
     * 評論表單驗證器.
     *
     * @param Request $request
     *
     * @return array
     */
    public static function make(Request $request): array
    {
        $v = BaseValidator::make($request->all(), [
            'captcha' => ['required', new Captcha],
            'content' => 'required|min:10|max:10000',
            'anonymous' => 'required|boolean',
            'professor' => 'bail|required_without:reply_to|string|exists:professors,name',
            'reply_to' => 'required_without:professor|string|size:12',
        ]);

        return  $v->validated();
    }
}
