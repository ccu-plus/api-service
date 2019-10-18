<?php

namespace App\Http\Controllers;

use App\Transformers\CommentTransformer;
use CCUPLUS\EloquentORM\Comment;
use CCUPLUS\EloquentORM\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * 取得課程評論.
     *
     * @param string $code
     *
     * @return JsonResponse
     */
    public function index(string $code): JsonResponse
    {
        /** @var Course $course */

        $course = Course::query()
            ->where('code', '=', $code)
            ->firstOrFail();

        $comments = $course->comments()
            ->with('user', 'professor', 'comments')
            ->withTrashed()
            ->latest()
            ->get();

        return fractal()
            ->collection($comments)
            ->transformWith(new CommentTransformer)
            ->respond();
    }

    /**
     * 最新幾則評論.
     *
     * @return JsonResponse
     */
    public function latest(): JsonResponse
    {
        $comments = Comment::with('course', 'course.department', 'user', 'professor')
            ->whereNull('comment_id')
            ->take(8)
            ->latest('created_at')
            ->get();

        return fractal()
            ->collection($comments)
            ->transformWith(new CommentTransformer)
            ->respond();
    }
}
