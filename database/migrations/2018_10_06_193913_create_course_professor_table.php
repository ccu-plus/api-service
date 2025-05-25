<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseProfessorTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_professor', function (Blueprint $table): void {
            $table->smallInteger('course_id')->unsigned();
            $table->smallInteger('professor_id')->unsigned();
            $table->tinyInteger('semester_id')->unsigned();

            $table->primary(['course_id', 'professor_id', 'semester_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_professor');
    }
}
