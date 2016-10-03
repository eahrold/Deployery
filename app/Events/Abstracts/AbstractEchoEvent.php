<?php

namespace App\Events\Abstracts;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

abstract class AbstractEchoEvent extends Event implements ShouldBroadcast
{
    use SerializesModels, InteractsWithSockets;
}
