<?php

namespace App\Transformers;

use CCUPLUS\EloquentORM\User;
use League\Fractal\TransformerAbstract;

class SignInTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param User|null $user
     *
     * @return array
     */
    public function transform(User $user = null)
    {
        if (!is_null($user)) {
            $hmac = hash_hmac('md5', $user->token, env('APP_KEY'), true);

            $token = sprintf('%s.%s', $user->token, base64_encode($hmac));
        }

        return [
            'signedUp' => !is_null($user),
            'token' => $token ?? null,
        ];
    }
}
