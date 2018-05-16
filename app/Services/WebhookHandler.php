<?php

namespace App\Services;

use App\Models\Server;
use Symfony\Component\HttpFoundation\Request;

/**
 * Get Git Info for a repo and branch.
 */
class WebhookInfo {
    public $user;
    public $source;
    public $from_commit;
    public $to_commit;
    public $branch;
}

class WebhookHandler {

    protected $request;
    protected $server;

    public function __construct(Request $request, Server $server) {
        $this->server = $server;
        $this->request = $request;
    }

    public function info() {
        // TODO: info about the webhook request
    }

    private function getDeploymentService() {
        // TODO: service calling the webhook
    }

}