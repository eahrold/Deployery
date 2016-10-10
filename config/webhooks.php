<?php

/**
 * This file is part of Teamwork
 *
 * @license MIT
 * @package Teamwork
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Sources
    |--------------------------------------------------------------------------
    |
    | A list of sources to test against
    |
    */
    'sources' => [
        'GitHub' => [
            'user_agent_prefix' => 'GitHub-Hookshot',
            'whitelist' => [
                '192.30.252.0/22',
            ],
        ],
        'BitBucket' => [
            'user_agent_prefix' => 'Bitbucket-Webhooks',
            'whitelist' => [
                '104.192.143.0/24',
            ],
        ],
        'Homestead' => [
            'user_agent_prefix' => '*',
            'whitelist' => [
                '192.168.10.1',
            ],
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Should the webhook be validated?
    |--------------------------------------------------------------------------
    |
    | If you are using a source that either cannot be analyzed
    | or is using a load balancer that masks the ip address
    |
    */
    'should_validate' => env('VALIDATE_WEBHOOK', true),

];
