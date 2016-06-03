<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCertificatesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('certificates', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('url_id');

            $table->string('name')->nullable();
            $table->string('cn')->nullable();
            $table->json('subject')->nullable();
            $table->string('hash')->nullable();
            $table->json('issuer')->nullable();
            $table->integer('version')->nullable();
            $table->string('serial_number')->nullable();
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_to')->nullable();
            $table->json('purposes')->nullable();
            $table->json('extentions')->nullable();
            $table->integer('key_bits')->nullable();
            $table->string('public_key')->nullable();
            $table->dateTime('ssl_labs_last_update')->nullable();
            $table->string('ssl_labs_rating')->nullable();

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

        Schema::drop('certificates');
    }
}
