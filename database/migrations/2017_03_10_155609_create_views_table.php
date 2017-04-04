<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('views', function (Blueprint $table) {

            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            $table->increments('id');
            //$table->string('lang', 2)->nullable(); //FK
            $table->string('period');
            $table->string('period_type');
            $table->integer('count')->unsigned()->nullable();
            $table->morphs('model'); //model_id, model_type
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('views', function($table) {
            //$table->foreign('lang')->references('code')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('views');
    }

}
