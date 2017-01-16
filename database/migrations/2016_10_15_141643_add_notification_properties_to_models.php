<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNotificationPropertiesToModels extends Migration
{

    private $slackable = [
        'teams',
        'servers',
        'projects'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        foreach ($this->slackable as $model) {
            Schema::table($model, function ($table){
                $table->boolean('send_slack_messages')->default(false);
                $table->text('slack_webhook_url');
            });
        }

        Schema::table('users', function ($table){
            $table->boolean('send_mail_messages')->default(false);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->slackable as $model) {
            Schema::table($model, function ($table){
                $table->dropColumn('send_slack_messages');
                $table->dropColumn('slack_webhook_url');
            });
        }

        Schema::table('users', function ($table){
            $table->dropColumn('send_mail_messages');
        });
    }
}
