<?php

declare(strict_types=1);

namespace App\Http\Validators;

use App\Http\Validators\Rules\Captcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class CommentValidator
{
    /**
     * 評論表單驗證器.
     *
     * @throws ValidationException
     */
    public static function make(Request $request): array
    {
        $v = Validator::make($request->all(), [
            'captcha' => ['required', new Captcha],
            'content' => 'required|min:10|max:10000',
            'recommended' => 'required|integer|between:1,5',
            'informative' => 'required|integer|between:1,5',
            'challenging' => 'required|integer|between:1,5',
            'overall' => 'required|integer|between:1,5',
            'professor' => 'bail|required_without:reply_to|string|exists:professors,name',
            'reply_to' => 'required_without:professor|string|size:12',
        ]);

        return $v->validated();
    }
}
