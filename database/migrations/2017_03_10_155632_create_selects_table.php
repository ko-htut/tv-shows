<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSelectsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('selects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100)->nullable(); //
            $table->enum('type', ['select', 'multiselect', 'range', 'radio'])->nullable();
            $table->integer('parent_id')->unsigned()->nullable(); //FK
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('selects', function($table) {
            $table->foreign('parent_id')->references('id')->on('selects');
            $table->unique(['title']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('selects');
    }

}
