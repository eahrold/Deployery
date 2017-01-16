<?php

namespace App\Models\Traits;

trait Slackable {

    /**
     * Route notifications for the Slack channel.
     *
     * @return string
     */
    public function routeNotificationForSlack()
    {
        if ($this->send_slack_messages) {
            return $this->slack_webhook_url;
        }
    }
}