<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEpisodesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    //Files - thumbs
    public function up() {
        Schema::create('episodes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('show_id')->unsigned();
            $table->integer('thetvdb_id')->unsigned()->unique();
            $table->string('imdb_id')->nullable();
            $table->date('first_aired')->nullable();
            $table->integer('season_number')->unsigned()->nullable();
            $table->integer('episode_number')->unsigned()->nullable();
            $table->float('rating')->nullable();
            $table->integer('rating_count')->unsigned()->nullable();
            $table->integer('last_updated')->unsigned()->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('episodes', function($table) {
            $table->foreign('show_id')->references('id')->on('shows');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('episodes');
    }

}
