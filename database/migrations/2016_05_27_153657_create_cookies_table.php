<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCookiesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('cookies', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('url_id');

            $table->string('name')->nullable();
            $table->longText('value')->nullable();
            $table->string('domain')->nullable();
            $table->string('path')->nullable();
            $table->integer('max_age')->nullable();
            $table->integer('expires')->nullable();
            $table->boolean('secure');
            $table->boolean('discard');
            $table->boolean('httponly');

            $table->index('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::drop('cookies');
    }
}
