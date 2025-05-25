<?php

declare(strict_types=1);

namespace App\Transformers;

use App\Models\Comment;
use Illuminate\Support\Facades\Cache;
use League\Fractal\TransformerAbstract;
use Throwable;

class CommentTransformer extends TransformerAbstract
{
    /**
     * Comment transformer.
     */
    public function transform(Comment $comment): array
    {
        return [
            'user' => null,
            'professor' => $comment->professor?->name,
            'content' => $comment->trashed() ? null : $comment->content,
            'recommended' => $comment->trashed() ? 0 : $comment->recommended,
            'informative' => $comment->trashed() ? 0 : $comment->informative,
            'challenging' => $comment->trashed() ? 0 : $comment->challenging,
            'overall' => $comment->trashed() ? 0 : $comment->overall,
            'commented_at' => $comment->created_at->toDateTimeString(),
            'deleted' => $comment->trashed(),
            'comments' => $comment->trashed() ? [] : fractal()->collection($comment->comments)->transformWith(new self)->toArray()['data'],
            'course' => ! $comment->relationLoaded('course') ? null : [
                'code' => $comment->course->code,
                'name' => $comment->course->name,
                'department' => $comment->course->department->name,
            ],
            'token' => $this->token($comment->id),
        ];
    }

    /**
     * Random token.
     */
    protected function token(int $key): string
    {
        try {
            $nonce = bin2hex(random_bytes(6));
        } catch (Throwable) {
            [$micro, $time] = explode(' ', microtime());

            $dec = (int) ($micro * (10 ** 18)) + (int) $time;

            $nonce = substr(str_shuffle(dechex($dec)), 0, 12);
        }

        Cache::put($nonce, $key, 60 * 60 * 6);

        return $nonce;
    }
}
