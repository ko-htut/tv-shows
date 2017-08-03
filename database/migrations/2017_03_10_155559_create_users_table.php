<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('users_role_id')->unsigned()->nullable(); //FK
            $table->string('lang', 2)->nullable(); //FK
            $table->string('username', 60)->nullable();
            $table->string('password', 255)->nullable();
            $table->string('first_name', 60)->nullable();
            $table->string('last_name', 60)->nullable();
            $table->string('email', 60)->nullable();
            $table->date('birthday')->nullable();
            $table->enum('gender', ['M', 'F'])->nullable();
            $table->text('about')->nullable();
            $table->string('facebook_id', 100)->nullable();
            $table->boolean('active')->default(true);
            $table->string('remember_token', 255)->nullable();
            $table->timestamps();
        });

        Schema::table('users', function($table) {
            $table->foreign('users_role_id')->references('id')->on('users_roles');
            $table->foreign('lang')->references('code')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('users');
    }

}
