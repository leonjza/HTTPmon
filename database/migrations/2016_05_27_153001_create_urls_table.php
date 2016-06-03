<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUrlsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('urls', function (Blueprint $table) {

            $table->increments('id');

            $table->string('url', 2083);
            $table->dateTime('last_check')->nullable();
            $table->json('features')->nullable();

            $table->index(['url']);
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

        Schema::drop('urls');
    }
}
