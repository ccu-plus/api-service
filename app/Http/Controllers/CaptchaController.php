<?php

namespace App\Http\Controllers;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;

class CaptchaController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $phrase = new PhraseBuilder(5, '0123456789');

        $captcha = (new CaptchaBuilder(null, $phrase))
            ->setIgnoreAllEffects(true)
            ->setMaxAngle(35)
            ->build();

        $nonce = bin2hex(random_bytes(16));

        Cache::put(
            $nonce,
            $captcha->getPhrase(),
            Date::now()->addMinutes(10),
        );

        return response()->json([
            'data' => $captcha->inline(),
            'nonce' => $nonce,
        ]);
    }
}
