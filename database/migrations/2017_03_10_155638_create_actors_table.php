<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActorsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('actors', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('gender', ['M', 'F'])->nullable();
            $table->date('birthday')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('actors', function($table) {
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('actors');
    }

}
