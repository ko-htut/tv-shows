<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTranslationsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('files_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('file_id')->unsigned()->nullable();
            $table->string('lang', 2)->nullable(); //FK
            $table->string('title')->nullable();
            $table->text('description');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('files_translations', function($table) {
            $table->foreign('file_id')->references('id')->on('files');
            $table->foreign('lang')->references('code')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('files_translations');
    }

}
