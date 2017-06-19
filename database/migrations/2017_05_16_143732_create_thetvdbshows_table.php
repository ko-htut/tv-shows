<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTheTvDbShowsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    
    //Terms = genres
    //Actors
    //Files - banner
    public function up() {
        Schema::create('thetvdbshows', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',255)->nullable();
            $table->integer('thetvdb_id')->unsigned()->unique();
            $table->integer('rating_count')->unsigned()->nullable();
            $table->boolean('fanart')->default(false);
            $table->boolean('poster')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('thetvdbshows');
    }

}
