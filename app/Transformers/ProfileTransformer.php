<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class ProfileTransformer extends TransformerAbstract
{
    /**
     * Profile transformer.
     *
     * @param User $user
     *
     * @return array
     */
    public function transform(User $user): array
    {
        return [
            'nickname' => $user->nickname,
            'email' => $user->email,
            'verified' => !is_null($user->verified_at),
        ];
    }
}
