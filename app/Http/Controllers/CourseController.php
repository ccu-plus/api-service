<?php

namespace App\Http\Controllers;

use App\Transformers\CourseTransformer;
use CCUPLUS\EloquentORM\Course;
use CCUPLUS\EloquentORM\Semester;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CourseController extends Controller
{
    /**
     * 搜尋課程.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $keyword = '';

        foreach (['college', 'department', 'dimension', 'keyword'] as $filter) {
            if (!empty($input = $request->input($filter))) {
                $keyword = trim(sprintf('%s %s', $keyword, $input));
            }
        }

        $key = sprintf('search-%s', base64_encode($keyword));

        $courses = Cache::remember($key, 60 * 60, function () use ($keyword) {
            return Course::search($keyword)
                ->take(300)
                ->get()
                ->load('department', 'dimension', 'professors')
                ->loadCount(['semesters', 'semesters as newest_semesters_count' => function (Builder $query) {
                    $query->where('semesters.id', '=', Semester::newest()->getKey());
                }]);
        });

        return fractal()
            ->collection($courses->loadCount('comments'))
            ->transformWith(new CourseTransformer)
            ->respond();
    }

    public function waterfall()
    {
        //
    }

    /**
     * 取得課程資訊.
     *
     * @param string $code
     *
     * @return JsonResponse
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
