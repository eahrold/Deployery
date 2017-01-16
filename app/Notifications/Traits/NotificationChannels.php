<?php

namespace App\Notifications\Traits;

trait NotificationChannels
{
    protected $via_channels = [
        'mail',
        'slack'
    ];

    protected function enabledChannels($notifiable, $condition=false) {
        $channels = [];
        foreach ($this->via_channels as $channel) {
            $fn = "send_{$channel}_messages";
            if ($condition || $notifiable->{$fn}) {
                $channels[] = $channel;
            }
        }
        return $channels;
    }
}