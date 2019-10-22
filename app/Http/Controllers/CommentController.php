<?php

namespace App\Http\Controllers;

use App\Http\Validators\CommentValidator;
use App\Transformers\CommentTransformer;
use Carbon\Carbon;
use CCUPLUS\EloquentORM\Comment;
use CCUPLUS\EloquentORM\Course;
use CCUPLUS\EloquentORM\Professor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
        $comments = Cache::remember('latest-comments', Carbon::now()->addMonth(), function () {
            return Comment::with('course', 'course.department', 'user', 'professor')
                ->whereNull('comment_id')
                ->take(8)
                ->latest('created_at')
                ->get();
        });

        return fractal()
            ->collection($comments)
            ->transformWith(new CommentTransformer)
            ->respond();
    }

    /**
     * 新增評論.
     *
     * @param Request $request
     * @param string $code
     *
     * @return JsonResponse
     */
    public function store(Request $request, string $code): JsonResponse
    {
        $input = CommentValidator::make($request);

        $commentId = null;

        if (isset($input['reply_to'])) {
            if (is_null($commentId = Cache::pull($input['reply_to']))) {
                throw new BadRequestHttpException;
            }
        }

        $course = Course::query()
            ->where('code', '=', $code)
            ->firstOrFail();

        $comment = Comment::query()->create([
            'user_id' => $request->user()->getKey(),
            'course_id' => is_null($commentId) ? $course->getKey() : null,
            'comment_id' => $commentId,
            'professor_id' => is_null($commentId) ? Professor::query()->where('name', '=', $input['professor'])->first()->getKey() : null,
            'content' => $input['content'],
            'anonymous' => $input['anonymous'],
        ]);

        Cache::forget('latest-comments');

        return fractal($comment->fresh('user', 'professor'))
            ->transformWith(new CommentTransformer)
            ->respond(201);
    }
}
