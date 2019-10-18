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
            $table->smallIncrements('id');
            $table->char('code', 7)->charset('latin1')->collation('latin1_general_ci')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('name_pinyin');
            $table->tinyInteger('credit')->unsigned();
            $table->tinyInteger('department_id')->unsigned();
            $table->tinyInteger('dimension_id')->unsigned()->nullable();
        });
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
