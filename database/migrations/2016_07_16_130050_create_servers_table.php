<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();

            $table->string('name');
            $table->string('protocol');
            $table->string('hostname');
            $table->integer('port');
            $table->string('username');
            $table->string('password');
            $table->boolean('use_ssk_key');
            $table->string('deployment_path');
            $table->string('branch');
            $table->string('environment');
            $table->string('sub_directory');
            $table->boolean('autodeploy');
            $table->string('webhook');

            $table->integer('project_id')->unsigned();
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
        Schema::drop('servers');
    }
}
