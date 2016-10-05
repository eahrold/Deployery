<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();

            $table->string('from_commit');
            $table->string('to_commit');

            $table->boolean('success');
            $table->string('user_name')->unsigned();

            $table->integer('server_id')->unsigned();
            $table->integer('project_id')->unsigned();

            $table->foreign('server_id')->references('id')->on('servers')->onDelete('no action');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('histories');
    }
}
