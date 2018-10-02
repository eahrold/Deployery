<?php

namespace App\Models\Observers;



class ServerObserver {

    // Register for events
    public function creating($model) {
        $model->resetWebhook();
    }
}