<?php

namespace App\Transformers;

use CCUPLUS\EloquentORM\Comment;
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
        ];
    }
}
