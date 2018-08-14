<?php

namespace App\Services\WebHooks;

use App\Models\Server;
use App\Services\WebHooks\Parsers\BitBucketParser;
use Symfony\Component\HttpFoundation\Request;

class WebhookHandler
{
    protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function info() {
        if( $parser = $this->getParser()) {
            $info = $parser->parse($this->request);
            return $info;
        }
        // TODO: info about the webhook request
    }

    private function getParser() {
        $agent = str_slug($this->request->header('User-Agent'));

        switch ($agent){
            case 'bitbucket-webhooks20':
                return new BitBucketParser;
                break;
            default:
                # code...
                break;
        }
    }

}