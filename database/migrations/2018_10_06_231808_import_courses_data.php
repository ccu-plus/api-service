<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ImportCoursesData extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /**
         * @var array $departments
         * @var array $dimensions
         * @var array $semesters
         * @var array $professors
         */
        foreach (['departments', 'dimensions', 'semesters', 'professors'] as $variable) {
            ${$variable} = collect(DB::table($variable)->get(['id', 'name']))
                ->pluck('id', 'name')
                ->toArray();
        }

        $courses = json_decode(file_get_contents(storage_path('legacy/courses.json')), true);

        foreach ($courses as $course) {
            // 課程資料
            $courseId = DB::table('courses')->insertGetId([
                'code' => $course['code'],
                'name' => $course['name'],
                'name_pinyin' => to_ascii($course['name']),
                'credit' => Arr::first(Arr::first($course['professors']))['credit'],
                'department_id' => $departments[$course['department']],
                'dimension_id' => $dimensions[$course['dimension']] ?? null,
            ]);

            // 課程學期資料
            $values = [];

            foreach ($course['semesters'] as $semester) {
                $values[] = [
                    'course_id' => $courseId,
                    'semester_id' => $semesters[$semester],
                ];
            }

            DB::table('course_semester')->insert($values);

            // 課程教授資料
            $values = [];

            foreach ($course['professors'] as $semester => $professor) {
                foreach ($professor as $user) {
                    if ($user['name'] === '未公佈') {
                        $user['name'] = '教師未定';
                    }

                    $values[] = [
                        'course_id' => $courseId,
                        'professor_id' => $professors[$user['name']],
                        'semester_id' => $semesters[$semester],
                    ];
                }
            }

            DB::table('course_professor')->insert($values);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['courses', 'course_professor', 'course_semester'];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
    }
}
