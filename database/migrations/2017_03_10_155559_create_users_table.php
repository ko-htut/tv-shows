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
            $table->string('first_name', 60)->nullable();
            $table->string('last_name', 60)->nullable();
            $table->string('email', 60)->nullable();
            $table->string('telephone', 30)->nullable();
            $table->date('birthday')->nullable();
            $table->enum('gender', ['M', 'F'])->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('users', function($table) {
            $table->foreign('users_role_id')->references('id')->on('users_roles');
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
