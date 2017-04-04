<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesToFoldersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('files_to_folders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('folder_id')->unsigned()->nullable();
            $table->integer('file_id')->unsigned()->nullable(); //FK
            $table->timestamps();
        });

        Schema::table('files_to_folders', function($table) {
            $table->foreign('folder_id')->references('id')->on('folders');
            $table->foreign('file_id')->references('id')->on('files');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('files_to_folders');
    }

}
