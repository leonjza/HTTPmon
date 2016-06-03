<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMetasTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('metas', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('url_id');

            $table->integer('status_code')->nullable();
            $table->string('protocol_version')->nullable();
            $table->string('reason_phrase')->nullable();

            $table->string('server')->nullable();

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

        Schema::drop('metas');
    }
}
