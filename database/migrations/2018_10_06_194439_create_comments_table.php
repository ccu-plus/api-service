<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table): void {
            $table->smallIncrements('id');
            $table->smallInteger('user_id')->unsigned();
            $table->smallInteger('course_id')->unsigned()->nullable();
            $table->smallInteger('comment_id')->unsigned()->nullable();
            $table->smallInteger('professor_id')->unsigned()->nullable();
            $table->text('content');
            $table->boolean('anonymous');
            $table->dateTime('created_at')->index();
            $table->dateTime('updated_at');
            $table->dateTime('deleted_at')->nullable();

            $table->index(['course_id', 'created_at']);
            $table->index(['comment_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
}
