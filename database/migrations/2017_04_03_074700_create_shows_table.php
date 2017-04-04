<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShowsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    
    //Terms = genres
    //Actors
    //Files - banner
    public function up() {
        Schema::create('shows', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('thetvdb_id')->unsigned()->unique();
            $table->string('imdb_id')->nullable();
            $table->date('first_aired')->nullable();
            $table->date('finale_aired')->nullable();
            $table->integer('air_day')->unsigned()->nullable();
            $table->string('air_time', 20)->nullable();
            $table->float('rating')->nullable();
            $table->integer('rating_count')->unsigned()->nullable();
            $table->integer('runtime')->unsigned()->nullable();
            $table->integer('last_updated')->unsigned()->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('shows');
    }

}
