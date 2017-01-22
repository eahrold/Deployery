<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterServerTableFixSshKeyProp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('servers', function ($table){
            $table->renameColumn('use_ssk_key', 'use_ssh_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('servers', function ($table){
            $table->renameColumn('use_ssh_key', 'use_ssk_key');
        });
    }
}
