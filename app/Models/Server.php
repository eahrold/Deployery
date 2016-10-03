<?php

namespace App\Models;

use App\Presenters\PresentableTrait;
use App\Services\GitInfo;
use App\Services\SSHConnection as Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

final class Server extends Base
{
    use PresentableTrait;

    protected $presenter = 'App\Presenters\Server';

    protected $unique_validation_key = ['name'];
    protected $validation_rules = [
        'name' => 'required:max:255',
        'hostname' => 'required:active_url',
        'username' => 'required:max:255',
        'deployment_path' => 'required:max:255',
    ];

    protected $fillable = [
        'name',
        'protocol',
        'hostname',
        'port',
        'username',
        'password',
        'use_ssk_key',
        'deployment_path',
        'branch',
        'environment',
        'sub_directory',
        'autodeploy',
        'webhook'
    ];

    public function getConfigAttribute()
    {
        return [
            'host'      => $this->hostname,
            'username'  => $this->username,
            'password'  => $this->password,
            'key'       => $this->project->keyPath('id_rsa'),
            'keytext'   => '',
            'keyphrase' => '',
            'agent'     => '',
            'timeout'   => 10,
        ];
    }

    protected $casts = [
        'use_ssk_key' => 'boolean',
        'autodeploy' => 'boolean',
    ];

    protected $hidden = [
        'webhook',
        'pubkey'
    ];

    protected $appends = [
        'is_deploying',
    ];

    private function deploymentCacheKey()
    {
        return "{$this->project->deploymentCacheKey()}.server.{$this->id}";
    }

    public function getIsDeployingAttribute($value = false)
    {
        return Cache::has($this->deploymentCacheKey());
    }

    public function setIsDeployingAttribute($value = false)
    {
        $this->project->is_deploying = (bool)$value;
        if ((bool)$value) {
            Cache::put($this->deploymentCacheKey(), (bool)$value, 5);
        } else {
            Cache::forget($this->deploymentCacheKey());
        }
    }

    public function getWebhookAttribute($value = '')
    {
        if (!$value) {
            return url('/api/webhooks/'.str_random(32));
        }
        return $value;
    }

    public function getProtocolAttribute($value = '')
    {
        return $value ?: 'ssh';
    }

    public function getPortAttribute($value = null)
    {
        return $value ?: 22;
    }

    private function repoCacheKey()
    {
        return "{$this->project->id}_repo_commits";
    }

    public function getSlugAttribute($value)
    {
        return str_slug($this->name);
    }

    public function getChannelIdAttribute($value = '')
    {
        return $this->project->channel_id;
    }

    public function getConnectionDetailsAttribute($value = '')
    {
        return "{$this->username}@{$this->hostname}";
    }

    //----------------------------------------------------------
    // GitInfo
    //-------------------------------------------------------
    private static $_git_info;
    public function getGitInfoAttribute($value = '')
    {
        if ( ! isset(static::$_git_info)) {
            if ($branch = $this->branch ?: Input::get('branch') ?: $this->project->branch) {
                static::$_git_info = (new GitInfo($this->repo, $branch))->withPubKey($this->project->user->auth_key);
            }
        }
        return static::$_git_info;
    }

    //----------------------------------------------------------
    // Commits
    //-------------------------------------------------------
    public function getCommitsAttribute($value = '')
    {
        return $this->git_info->commits(10);
    }

    public function getNewestCommitAttribute($value = '')
    {
        return $this->git_info->newest_commit;
    }

    public function getInitialCommitAttribute($value = '')
    {
        return $this->git_info->initial_commit;
    }

    public function getLastDeployedCommitAttribute($value = '')
    {
        $history = $this->successful_deployments->first();
        if ($history) {
            return $history->to_commit;
        }
        $this->initial_commit;
    }

    //----------------------------------------------------------
    // Connection
    //-------------------------------------------------------
    private function getConnectionAuth()
    {
        if ($this->use_ssk_key) {
            return ['key' => $this->project->ssh_key,
                    'keyphrase' => ''];
        } else {
            return [
                'password' => $this->password ?: ""
            ];
        }
    }

    private $ssh_connection;
    public function getConnectionAttribute()
    {
        if (!$this->ssh_connection) {
            $this->ssh_connection = new Connection(
                $this->slug,
                $this->hostname,
                $this->username,
                $this->getConnectionAuth(),
                null,
                $timeout = 10
            );
        }
        return $this->ssh_connection;
    }

    public function validateConnection()
    {
        $status = Connection::CONNECTION_STATUS_SUCCESS;
        $connection = $this->getConnectionAttribute();

        if (!$connection->exists($this->deployment_path)) {
            $status = Connection::CONNECTION_STATUS_INVALID_PATH;
        } else {
            $fauxf = strtoupper(md5(uniqid(rand(), true)));
            $fauxPath = "{$this->deployment_path}/{$fauxf}";
            $connection->putString($fauxPath, "hello world");
            if (!$connection->exists($fauxPath)) {
                $status = Connection::CONNECTION_STATUS_CANNOT_WRITE_TO_PATH;
            } else {
                $connection->delete($fauxPath);
            }
        }

        // If the connection status wasn't caught by any of the
        // above conditons, but there was a failure...
        if ($status == 0 && $connection->status() != 0) {
            $status = Connection::CONNECTION_STATUS_FAILURE;
        }

        $this->successfully_connected = $status;
        $this->save();
        return ($status === Connection::CONNECTION_STATUS_SUCCESS);
    }

    //----------------------------------------------------------
    // Repo
    //-------------------------------------------------------
    public function getRepoAttribute($value = '')
    {
        if ($this->project) {
            return $this->project->repo_path;
        }
    }

    public function getBranchAttribute($value = "master")
    {
        return $value;
    }

    public function setBranchAttribute($value)
    {
        if ($this->project) {
            $this->git_info->branch($value);
            $this->attributes['branch'] = $value;
        }
    }

    //----------------------------------------------------------
    // Morph classes
    //-------------------------------------------------------
    public function project()
    {
        return $this->belongsTo('App\Models\Project')->order();
    }

    public function configs()
    {
        return $this->belongsToMany('App\Models\Config')->order();
    }

    public function scripts()
    {
        return $this->belongsToMany('App\Models\Script')->order();
    }

    public function history()
    {
        return $this->hasMany('App\Models\History')->orderBy('created_at', 'DESC');
    }

    //----------------------------------------------------------
    // Hook extras
    //-------------------------------------------------------
    public function getPreInstallScriptsAttribute()
    {
        return $this->filteredInstallScripts($predeploy = true);
    }

    public function getPostInstallScriptsAttribute()
    {
        return $this->filteredInstallScripts($predeploy = false);
    }

    private function filteredInstallScripts($predeploy)
    {
        $count = $this->successful_deployments->count();
        return $this->scripts()
                    ->where('run_pre_deploy', $predeploy)
                    ->get()
                    ->filter(function ($script) use ($count) {
                switch ($script->on_deployment) {
                    case $script::RUN_ON_ALL_DEPLOYMENTS:
                        return true;
                        break;
                    case $script::RUN_ON_FIRST_DEPLOYMENT:
                        return ($count == 0);
                        break;
                    case $script::RUN_ON_ALL_BUT_FIRST_DEPLOYMENT:
                        return ($count > 0);
                        break;
                    default:
                        break;
                }
                return false;
            });
    }

    //----------------------------------------------------------
    // Histories
    //-------------------------------------------------------
    public function getFailedDeploymentsAttribute()
    {
        return $this->history()
                    ->where('success', false)
                    ->get();
    }

    public function getSuccessfulDeploymentsAttribute()
    {
        return $this->history()
                    ->where('success', true)
                    ->get();
    }

    //----------------------------------------------------------
    // Booting
    //-------------------------------------------------------
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            // A list of properties that if changed, require revalidating
            // the server connection.
            $check = ['hostname', 'username', 'password', 'port', 'deployment_path'];
            if ($model->isDirty($check)) {
                $model->successfully_connected = self::CONNECTION_STATUS_UNKNOWN;
            }
        });
    }
}
