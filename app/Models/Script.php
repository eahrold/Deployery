<?php

namespace App\Models;

use App\Models\Server;
use Illuminate\Support\Facades\Log;

final class Script extends Base
{
    const RUN_ON_ALL_DEPLOYMENTS = 0;
    const RUN_ON_FIRST_DEPLOYMENT = 1;
    const RUN_ON_ALL_BUT_FIRST_DEPLOYMENT = 2;

    protected $validation_rules = [
        'description' => 'required:max:255',
        'script' => 'required'
    ];

    protected $fillable = [
        // Human descripton of the file
        'description',

        // The bash-like script to be run on the server
        'script',

        // Should the script should be run before deployment?
        'run_pre_deploy',

        // Shold the deployment stop on script failure?
        'stop_on_failure',

        // Which deployments should the server be run (SEE RUN_ON consts)
        'on_deployment',

        // Should the script be available to all servers?
        'available_to_all_servers',
    ];

    protected $casts = [
        'run_pre_deploy' => 'bool',
        'stop_on_failure' => 'bool',
        'available_to_all_servers' => 'bool',
    ];

    private $_parsable = [
        '%deployment_path%' => "Server's Deployment Path",
        '%username%' => 'Username used to log into the server',
        '%password%' => 'Password used to log into the server',
        '%environment%' => 'Server environment (development, production etc.)',
        '%branch%' => 'The branch being deployed',
    ];

    public function getParsableAttribute()
    {
        return $this->_parsable;
    }

    public function parse(Server $server)
    {
        $script = $this->script;
        foreach ($this->_parsable as $key => $message) {
            $serverKey = str_replace('%', '', $key);
            if ($swap = $server->{$serverKey}) {
                $script = str_replace($key, $swap, $script);
            }
        }
        return $script;
    }

    public function project()
    {
        return $this->belongsTo('App\Models\Project')->order();
    }

    public function servers()
    {
        return $this->belongsToMany('App\Models\Server')->order();
    }

    /**
     * Select/Options key/value pair
     * @return string[] usable description value pair
     */
    public function getDeploymentOptsAttribute()
    {
        return [
            self::RUN_ON_FIRST_DEPLOYMENT => 'Run on first deployment',
            self::RUN_ON_ALL_DEPLOYMENTS => 'Run on all deployments',
            self::RUN_ON_ALL_BUT_FIRST_DEPLOYMENT => 'Run on all but first deployment',
        ];
    }
}
