<?php

namespace App\Http\Controllers;

use App\Transformers\CommentTransformer;
use CCUPLUS\EloquentORM\Course;
use Illuminate\Http\JsonResponse;

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
            ->with('professor', 'comments')
            ->withTrashed()
            ->latest()
            ->get();

        return fractal()
            ->collection($comments)
            ->transformWith(new CommentTransformer)
            ->respond();
    }

    public function store(string $code)
    {
        //
    }
}
