<?php

namespace App\Models;

use App\Models\Server;
use Illuminate\Support\Facades\Log;

final class Script extends Base
{
    const RUN_ON_ALL_DEPLOYMENTS = 0;
    const RUN_ON_FIRST_DEPLOYMENT = 1;
    const RUN_ON_ALL_BUT_FIRST_DEPLOYMENT = 2;

    protected $fillable = [
        // bool indicating whether the script
        // should be run before deployment
        'run_pre_deploy',

        'description',
        'stop_on_failure',
        'on_deployment',
        'available_to_all_servers',
        // The bash-like script to
        // be run on the server
        'script',
    ];

    protected $casts = [
        'run_pre_deploy' => 'bool',
        'stop_on_failure' => 'bool'
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
                $script = str_replace($key, $server->{$serverKey}, $script);
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

    public function getServersAttirbute()
    {
        if ($ids = $this->server_ids) {
            $whereIns = explode($ids);
            return $this->projects()->servers()->whereIn('id', $whereIns);
        }
        return collect([]);
    }


    /**
     * Select/Options key/value pair
     * @return array usable description value pair
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
