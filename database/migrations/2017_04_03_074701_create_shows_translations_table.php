<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShowsTranslationsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('shows_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('show_id')->unsigned()->nullable(); //FK
            $table->string('lang', 2)->nullable(); //FK
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->text('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->timestamps();
        });

        Schema::table('shows_translations', function($table) {
            $table->foreign('show_id')->references('id')->on('shows');
            $table->foreign('lang')->references('code')->on('languages');
            $table->unique(['lang', 'show_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('shows_translations');
    }

}
