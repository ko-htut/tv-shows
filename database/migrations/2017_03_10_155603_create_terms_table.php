<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTermsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('terms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('term_type_id')->unsigned()->nullable(); //FK
            $table->string('slug', 60)->nullable();//en indetifier
            $table->integer('parent_id')->unsigned()->nullable(); //FK
            $table->integer('sort_i')->unsigned()->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('terms', function($table) {
            $table->foreign('term_type_id')->references('id')->on('terms_types');
            $table->foreign('parent_id')->references('id')->on('terms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('terms');
    }

}
