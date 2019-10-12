<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class BaseController extends Controller
{
    public function index()
    {
        //
    }

    /**
     * 取得驗證碼.
     *
     * @return JsonResponse
     */
    public function captcha(): JsonResponse
    {
        $captcha = app('captcha')->build();

        $nonce = bin2hex(random_bytes(16));

        $captcha->getPhrase();

        Cache::put($nonce, $captcha->getPhrase(), 60 * 10);

        return response()->json([
            'data' => $captcha->inline(),
            'nonce' => $nonce,
        ]);
    }
}
