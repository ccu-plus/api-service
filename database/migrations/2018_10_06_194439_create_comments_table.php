<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
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
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
