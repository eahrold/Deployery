<?php

namespace App\Services\WebHooks\Parsers;

use App\Services\WebHooks\WebhookInfo;
use Illuminate\Http\Request;

interface ParsesWebhooks
{
    public function parse(Request $request) : WebhookInfo;
}