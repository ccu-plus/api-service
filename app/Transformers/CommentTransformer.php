<?php

namespace App\Transformers;

use CCUPLUS\EloquentORM\Comment;
use Illuminate\Support\Facades\Cache;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract
{
    /**
     * Comment transformer.
     *
     * @param Comment $comment
     *
     * @return array
     */
    public function transform(Comment $comment): array
    {
        return [
            'user' => ($comment->anonymous || $comment->trashed()) ? null : $comment->user->nickname,
            'professor' => optional($comment->professor)->name,
            'content' => $comment->trashed() ? null : $comment->content,
            'commented_at' => $comment->created_at->toDateTimeString(),
            'deleted' => $comment->trashed(),
            'comments' => $comment->trashed() ? [] : fractal()->collection($comment->comments)->transformWith(new CommentTransformer)->toArray()['data'],
            'course' => !$comment->relationLoaded('course') ? null : [
                'code' => $comment->course->code,
                'name' => $comment->course->name,
                'department' => $comment->course->department->name,
            ],
            'token' => $this->token($comment->id),
        ];
    }

    /**
     * Random token.
     *
     * @param int $key
     *
     * @return string
     */
    protected function token(int $key): string
    {
        try {
            $nonce = bin2hex(random_bytes(6));
        } catch (\Exception $e) {
            [$micro, $time] = explode(' ', microtime());

            $dec = intval($micro * (10 ** 18)) + intval($time);

            $nonce = substr(str_shuffle(dechex($dec)), 0, 12);
        }

        Cache::put($nonce, $key, 60 * 60 * 6);

        return $nonce;
    }
}
