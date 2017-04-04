<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributesTranslationsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('attributes_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('attribute_id')->unsigned()->nullable(); //FK
            $table->string('lang', 2)->nullable(); //FK
            $table->string('title')->nullable();
            $table->text('value')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('attributes_translations', function($table) {
            $table->foreign('attribute_id')->references('id')->on('attributes');
            $table->foreign('lang')->references('code')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('attributes_translations');
    }

}
