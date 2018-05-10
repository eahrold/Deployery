<?php

namespace App\Models;

use App\Models\Config;
use App\Models\History;
use App\Models\Project;
use App\Models\Script;
use App\Models\Traits\GitInfoTrait;
use App\Models\Traits\PasswordEncrypter;
use App\Models\Traits\SSHAble;
use App\Models\Traits\Slackable;
use App\Presenters\PresentableTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;

/**
* @property boolean    $autodeploy
* @property Array      $available_branches
* @property string     $branch
* @property integer    $channel_id
* @property Array      $connection_details
* @property string     $available_branches
* @property string     $deployment_path
* @property string     $environment
* @property History    $failed_deployments
* @property string     $hostname
* @property boolean    $is_deploying
* @property Array      $last_deployed_commit
* @property Array      $last_deployed_commit_details
* @property string     $name
* @property string     $password
* @property Collection $post_install_scripts
* @property Collection $pre_install_scripts
* @property string     $protocol
* @property string     $port
* @property string     $username
* @property boolean    $use_ssh_key
* @property boolean    $send_slack_messages
* @property string     $slack_webhook_url
* @property string     $sub_directory
* @property History    $successful_deployments
* @property string     $webhook
*/
final class Server extends Base
{
    use PresentableTrait;
    use PasswordEncrypter;
    use Slackable;
    use Notifiable;
    use SSHAble;
    use GitInfoTrait;

    protected $presenter = 'App\Presenters\Server';

    /**
     * @param integer $id
     */
    public function getValidationRules($id = null, $append=[])
    {
        $id = $id ?: $this->id;
        $name = "unique:servers";
        $name .= $id ? (",name,{$this->id},id,project_id,{$this->project_id}") : "";

        $rules = [
            'name' => $name,
            'hostname' => 'required|string',
            'username' => 'required|max:255',
            'deployment_path' => 'required|max:255',
            'slack_webhook_url' => 'active_url',
        ];

        if($this->id) {
            $rules['branch'] = "git_branch:{$this->project->repo_path}";
        }

        return $rules;
    }

    protected $fillable = [
        'name',
        'protocol',
        'hostname',
        'port',
        'username',
        'password',
        'use_ssh_key',
        'deployment_path',
        'branch',
        'environment',
        'sub_directory',
        'autodeploy',
        'webhook',
        'slack_webhook_url',
        'send_slack_messages',
    ];

    protected $casts = [
        'use_ssh_key' => 'boolean',
        'autodeploy' => 'boolean',
    ];

    protected $hidden = [
        'webhook',
        'pubkey',
        'project'
    ];

    protected $appends = [
        'is_deploying',
    ];

    /**
     * Get the cache key used to check for deployment status
     *
     * @return string|boolean cache key
     */
    private function deploymentCacheKey()
    {
        if($this->project) {
            return "{$this->project->deploymentCacheKey()}.server.{$this->id}";
        }
        return false;
    }

    /**
     * Getter for the is_deploying attribute
     *
     * @param  boolean $value
     * @return boolean        true if repo is deploying false otherwise
     */
    public function getIsDeployingAttribute($value = false)
    {
        if($key = $this->deploymentCacheKey()) {
            return Cache::has($key);
        }
        return false;
    }

    /**
     * Setter for the is_deploying attribute
     *
     * @param boolean $value
     */
    public function setIsDeployingAttribute($value = false)
    {
        $this->project->is_deploying = (bool)$value;
        if ((bool)$value) {
            Cache::put($this->deploymentCacheKey(), (bool)$value, 5);
        } else {
            Cache::forget($this->deploymentCacheKey());
        }
    }

    /**
     * Getter for the webhook attribute
     *
     * @param  string $value webhook attribute or random string
     * @return string        deployment webhook url
     */
    public function getWebhookAttribute($value = '')
    {
        if (!$value) {
            return url('/api/webhooks/'.str_random(32));
        }
        return $value;
    }

    /**
     * Getter for the protocol attribute
     *
     * @param  string $value
     * @return string        protocol, defaults to ssh
     */
    public function getProtocolAttribute($value = '')
    {
        return $value ?: 'ssh';
    }

    /**
     * Getter for the port attribute
     *
     * @param  string $value
     * @return string        port, defaults to 22 (ssh)
     */
    public function getPortAttribute($value = null)
    {
        return $value ?: 22;
    }

     /**
     * Getter for the slug attribute
     *
     * @param  string $value
     * @return string       slug string
     */
    public function getSlugAttribute($value)
    {
        return str_slug($this->name);
    }

    /**
     * Getter for the channel_id attribute
     *
     * @param  string $value
     * @return string        pass through to the project.channel_id
     */
    public function getChannelIdAttribute($value = '')
    {
        return $this->project->channel_id;
    }

    /**
     * Getter for the connection_details attribute
     *
     * @param  string $value
     * @return string        connection details string
     */
    public function getConnectionDetailsAttribute($value = '')
    {
        return "{$this->username}@{$this->hostname}";
    }

    /**
     * Getter for the slack_webhook_url attribute
     *
     * @param  mixed $value
     * @return string        the webhook url, or the projects webhook url
     */
    public function getSlackWebhookUrlAttribute($value = null)
    {
        return $value ?: ($this->project ? $this->project->slack_webhook_url : $value);
    }

    /**
     * Getter for the last_deployed_commit attribute
     *
     * @return associative array
     */
    public function getLastDeployedCommitAttribute($value = '')
    {
        $history = $this->successful_deployments->first();
        if ($history) {
            return $history->to_commit;
        }
        $this->initial_commit;
    }

    /**
     * Getter for the last_deployed_commit attribute
     *
     * @return associative array
     */
    public function getLastDeployedCommitDetailsAttribute($value = '')
    {
        if($hash = $this->last_deployed_commit) {
            return $this->findCommit($hash);
        }
    }

    /**
     * Proxy for project->branches
     *
     * @return associative array
     */
    public function getAvailableBranchesAttribute($value = '')
    {
        return $this->project->branches;
    }

    //----------------------------------------------------------
    // Relations
    //-------------------------------------------------------

    public function project()
    {
        return $this->belongsTo(Project::class)->order();
    }

    public function configs()
    {
        return $this->belongsToMany(Config::class)->order();
    }

    public function scripts()
    {
        return $this->belongsToMany(Script::class)->order();
    }

    public function oneOffScripts()
    {
        return $this->project->scripts()->where('available_for_one_off', true);
    }

    public function history()
    {
        return $this->hasMany(History::class)->orderBy('created_at', 'DESC');
    }

    //----------------------------------------------------------
    // Script extras
    //-------------------------------------------------------

    public function getPreInstallScriptsAttribute()
    {
        return $this->filteredInstallScripts($predeploy = true);
    }

    public function getPostInstallScriptsAttribute()
    {
        return $this->filteredInstallScripts($predeploy = false);
    }

    /**
     * filter scripts
     *
     * @param  boolean $predeploy
     * @return Collection          conditional scripts
     */
    private function filteredInstallScripts($predeploy)
    {
        $count = $this->successful_deployments->count();
        return $this->scripts()
                    ->where('run_pre_deploy', $predeploy)
                    ->get()
                    ->filter(function($script) use ($count) {
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

}
