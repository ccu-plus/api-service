<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->char('code', 7)->unique();
            $table->string('name', 64);
            $table->tinyInteger('department_id')->unsigned();
            $table->tinyInteger('dimension_id')->unsigned()->nullable();
            $table->timestamps();});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
