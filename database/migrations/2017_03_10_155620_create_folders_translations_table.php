<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFoldersTranslationsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('folders_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('folder_id')->unsigned()->nullable();
            $table->string('lang', 2)->nullable(); //FK
            $table->string('title')->nullable();
            $table->text('description');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('folders_translations', function($table) {
            $table->foreign('folder_id')->references('id')->on('folders');
            $table->foreign('lang')->references('code')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('folders_translations');
    }

}
