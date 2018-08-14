<?php

namespace App\Services\WebHooks\Parsers;

use App\Services\WebHooks\WebhookInfo;
use Illuminate\Http\Request;

final class BitBucketParser implements ParsesWebhooks
{
    public function parse(Request $request) : WebhookInfo
    {
        $data = $request->all();

        $source = $request->header('User-Agent') ?: "BitBucket Webhook (Fallback)";
        $user = data_get($data,'actor.display_name');
        $from_commit = data_get($data,'push.changes.0.old.target.hash');
        $to_commit = data_get($data,'push.changes.0.new.target.hash');
        $branch = data_get($data,'push.changes.0.new.name');

        return new WebhookInfo($user, $source, $from_commit, $to_commit, $branch);
    }
}