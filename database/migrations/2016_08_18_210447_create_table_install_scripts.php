<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInstallScripts extends Migration
{
    public function up()
    {
        Schema::create('scripts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();

            $table->text('script');
            $table->string('description');

            $table->boolean('run_pre_deploy');
            $table->boolean('stop_on_failure');
            $table->integer('on_deployment');
            $table->integer('available_to_all_servers');

            $table->integer('project_id')->unsigned();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });

        Schema::create('script_server', function (Blueprint $table) {
            $table->integer('server_id')->unsigned()->index();
            $table->foreign('server_id')->references('id')->on('servers')->onDelete('cascade');

            $table->integer('script_id')->unsigned()->index();
            $table->foreign('script_id')->references('id')->on('scripts')->onDelete('cascade');

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
        Schema::drop('scripts_server');
        Schema::drop('scripts');
    }
}
