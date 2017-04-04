<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('attributes', function (Blueprint $table) {

            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            $table->increments('id');
            $table->string('column_type')->nullable();
            $table->integer('parent_id')->unsigned()->nullable(); //FK
            $table->morphs('model'); //model_id, model_type
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('attributes', function($table) {
            $table->foreign('parent_id')->references('id')->on('attributes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('attributes');
    }

}
