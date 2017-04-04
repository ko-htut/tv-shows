<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTranslationsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pages_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('page_id')->unsigned()->nullable();
            $table->string('lang', 2)->nullable(); //FK
            $table->string('title')->nullable();
            $table->text('content');
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('pages_translations', function($table) {
            $table->foreign('page_id')->references('id')->on('pages');
            $table->foreign('lang')->references('code')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pages_translations');
    }

}
