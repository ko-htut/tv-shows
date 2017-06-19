<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('files', function (Blueprint $table) {

            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->string('patch')->nullable();
            $table->string('external_patch')->nullable();
            $table->longText('base64')->nullable();//
            $table->string('type')->nullable();
            $table->string('extension')->nullable();
            $table->integer('file_size')->unsigned()->nullable();
            $table->integer('width')->unsigned()->nullable();
            $table->integer('height')->unsigned()->nullable();
            $table->morphs('model'); //model_id, model_type
            $table->integer('sort')->unsigned()->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        
        Schema::table('files', function($table) {
            $table->foreign('parent_id')->references('id')->on('files');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('files');
    }

}
