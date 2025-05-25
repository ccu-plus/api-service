<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Semester;
use App\Transformers\CourseTransformer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CourseController extends Controller
{
    /**
     * 搜尋課程.
     */
    public function search(Request $request): JsonResponse
    {
        $keyword = '';

        foreach (['college', 'department', 'dimension'] as $filter) {
            if (! empty($input = $request->input($filter))) {
                $keyword = trim(sprintf('%s %s', $keyword, $input));
            }
        }

        if (! empty($input = $request->input('keyword'))) {
            $keyword = empty($keyword)
                ? to_ascii($input)
                : sprintf('%s %s', $keyword, $input);
        }

        $key = sprintf('search-%s', base64_encode($keyword));

        $courses = Cache::remember($key, Carbon::now()->addDay(), function () use ($keyword) {
            return Course::search($keyword)
                ->take(300)
                ->get()
                ->load('department', 'dimension', 'professors')
                ->loadCount(['semesters', 'semesters as newest_semesters_count' => function (Builder $query): void {
                    $query->where('semesters.id', '=', Semester::newest()->getKey());
                }]);
        });

        return fractal()
            ->collection($courses->loadCount('comments'))
            ->transformWith(new CourseTransformer)
            ->respond();
    }

    /**
     * 取得課程資訊.
     */
    public function show(string $code): JsonResponse
    {
        $course = Course::with('department', 'dimension', 'semesters', 'professors')
            ->where('code', '=', $code)
            ->firstOrFail();

        return fractal($course)
            ->transformWith(new CourseTransformer)
            ->respond();
    }
}
