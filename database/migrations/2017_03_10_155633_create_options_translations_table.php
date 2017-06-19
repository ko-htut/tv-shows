<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOptionsTranslationsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('options_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('option_id')->unsigned()->nullable();
            $table->string('value')->nullable();
            $table->string('lang', 2)->nullable(); //FK
            $table->timestamps();
        });

        Schema::table('options_translations', function($table) {
            $table->foreign('option_id')->references('id')->on('options');
            $table->foreign('lang')->references('code')->on('languages');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('options_translations');
    }

}
