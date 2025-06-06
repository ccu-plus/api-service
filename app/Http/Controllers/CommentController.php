<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Validators\CommentValidator;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Professor;
use App\Transformers\CommentTransformer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CommentController extends Controller
{
    /**
     * 取得課程評論.
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
     * 評論統計.
     */
    public function statistics(): JsonResponse
    {
        $statistics = Comment::whereNull('comment_id')
            ->get(['created_at'])
            ->each(function (Comment $comment) {
                $comment->year = $comment->created_at->year;
            })
            ->groupBy('year')
            ->map(function (Collection $collection) {
                return $collection->count();
            })
            ->toArray();

        $total = 0;

        foreach ($statistics as &$statistic) {
            $statistic = $total = $statistic + $total;
        }

        return response()->json([
            'labels' => array_keys($statistics),
            'value' => array_values($statistics),
        ]);
    }

    /**
     * 最新幾則評論.
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

    /**
     * 新增評論.
     */
    public function store(Request $request, string $code): JsonResponse
    {
        if (! Str::startsWith($request->ip(), '140.123.')) {
            throw new AccessDeniedHttpException;
        }

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
            'user_id' => 0,
            'course_id' => is_null($commentId) ? $course->getKey() : null,
            'comment_id' => $commentId,
            'professor_id' => is_null($commentId) ? Professor::query()->where('name', '=', $input['professor'])->first()->getKey() : null,
            'content' => $input['content'],
            'recommended' => $commentId ? 0 : $input['recommended'],
            'informative' => $commentId ? 0 : $input['informative'],
            'challenging' => $commentId ? 0 : $input['challenging'],
            'overall' => $commentId ? 0 : $input['overall'],
            'anonymous' => true,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        Cache::forget('latest-comments');

        return fractal($comment->fresh('user', 'professor'))
            ->transformWith(new CommentTransformer)
            ->respond(201);
    }
}
