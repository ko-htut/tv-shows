<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFoldersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('folders', function (Blueprint $table) {

            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            $table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable(); //FK
            $table->string('type');
            $table->morphs('model'); //model_id, model_type
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('folders', function($table) {
            $table->foreign('parent_id')->references('id')->on('folders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('folders');
    }

}
