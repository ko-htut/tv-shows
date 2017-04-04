<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOptionsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('options', function (Blueprint $table) {



            $table->increments('id');
            $table->integer('select_id')->unsigned()->nullable(); //FK
            $table->integer('parent_id')->unsigned()->nullable(); //FK
            $table->string('slug');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('options', function($table) {
            $table->foreign('select_id')->references('id')->on('selects');
            $table->foreign('parent_id')->references('id')->on('options');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('options');
    }

}
