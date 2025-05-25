<?php

declare(strict_types=1);

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class AuthTransformer extends TransformerAbstract
{
    /**
     * Auth transformer.
     */
    public function transform(?User $user = null): array
    {
        if ($user->exists) {
            $hmac = hash_hmac('md5', $user->token, env('APP_KEY'), true);

            $token = sprintf('%s.%s', $user->token, base64_encode($hmac));
        }

        return [
            'signedUp' => $user->exists,
            'token' => $token ?? encrypt(['username' => $user->username, 'timestamp' => time()]),
        ];
    }
}
