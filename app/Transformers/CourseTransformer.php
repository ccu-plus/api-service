<?php

declare(strict_types=1);

namespace App\Transformers;

use App\Models\Course;
use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;

class CourseTransformer extends TransformerAbstract
{
    /**
     * Course transformer.
     */
    public function transform(Course $course): array
    {
        return [
            'code' => $course->code,
            'name' => $course->name,
            'credit' => $course->credit,
            'department' => $course->department->name,
            'dimension' => $course->dimension?->name,
            'semesters' => $course->relationLoaded('semesters') ? $this->semesters($course) : [],
            'professors' => $course->professors->pluck('name')->unique()->values(),
            'recently' => ($course->semesters_count ?? 0) === ($course->newest_semesters_count ?? 0),
            'comments' => $course->comments_count ?? 0,
        ];
    }

    /**
     * 課程各學期資料.
     */
    protected function semesters(Course $course): Collection
    {
        return $course->semesters->reverse()->values()->map(function ($semester) use ($course): array {
            $professors = $course->professors->where('pivot.semester_id', $semester->id);

            return [
                'name' => $semester->name,
                'professors' => $professors->pluck('name'),
            ];
        });
    }
}
