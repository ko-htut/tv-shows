<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTermsTranslationsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('terms_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('term_id')->unsigned()->nullable(); //FK
            $table->string('lang', 2)->nullable(); //FK
            $table->string('title', 60)->nullable();
            $table->string('slug', 60)->nullable();
            $table->text('description')->nullable();
            $table->string('meta_title', 60)->nullable();
            $table->string('meta_description', 60)->nullable();
            $table->timestamps();
        });

        Schema::table('terms_translations', function($table) {
            $table->foreign('term_id')->references('id')->on('terms');
            $table->foreign('lang')->references('code')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('terms_translations');
    }

}
