<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('username', 16)->charset('latin1')->collation('latin1_general_ci')->unique();
            $table->string('nickname', 24)->unique();
            $table->string('email', 48)->nullable()->unique();
            $table->char('token', 16)->charset('latin1')->collation('latin1_general_cs')->unique();
            $table->dateTime('verified_at')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
