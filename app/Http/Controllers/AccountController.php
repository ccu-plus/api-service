<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Transformers\ProfileTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Account profile.
     *
     *
     */
    public function profile(Request $request): JsonResponse
    {
        return fractal($request->user())
            ->transformWith(new ProfileTransformer)
            ->respond();
    }
}
