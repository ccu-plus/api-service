<?php

declare(strict_types=1);

use CCUPLUS\EloquentORM\Course;
use CCUPLUS\EloquentORM\Professor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ImportCommentsData extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $path1 = storage_path('app/comments.json');

        $path2 = storage_path('app/professors.json');

        $path3 = storage_path('app/comment_professor.json');

        if (! is_file($path1) || ! is_file($path2) || ! is_file($path3)) {
            return;
        }

        $comments = json_decode(file_get_contents($path1), true);

        $professors = json_decode(file_get_contents($path2), true);

        $professors = array_combine(array_column($professors, 'id'), array_column($professors, 'name'));

        $cToP = [];

        $exists = Professor::all()->pluck('id', 'name')->toArray();

        foreach (json_decode(file_get_contents($path3), true) as $mapping) {
            $cToP[$mapping['comment_id']][] = $exists[$professors[$mapping['professor_id']]];
        }

        $courses = Course::all(['id', 'code'])->pluck('id', 'code')->toArray();

        foreach ($comments as $comment) {
            $values = Arr::only($comment, [
                'id', 'user_id', 'comment_id', 'content', 'anonymous',
                'created_at', 'updated_at', 'deleted_at',
            ]);

            $values['course_id'] = $courses[$comment['commentable_id']] ?? null;

            $values['professor_id'] = Arr::first($cToP[$comment['id']] ?? []);

            DB::table('comments')->insert($values);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('comments')->truncate();
    }
}
