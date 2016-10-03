<?php

namespace App\Models;

use App\Services\SSHKeyer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Process\ProcessBuilder;

final class Project extends Base
{
    protected $unique_validation_key = ['name', 'repo'];
    public $validationRules = [
        'name' => 'required|string',
        'repo' => 'required|string',
    ];

    protected $fillable = [
        'name',
        'slug',
        'repo',
        'branch',
    ];

    protected $hidden = [
        'guid'
    ];

    public function initializeProject($data)
    {
        $data['slug'] = str_slug($data['name']);
        $project = $this->newInstance($data);
        $project->save();

        return $project;
    }

    //----------------------------------------------------------
    // Util
    //-------------------------------------------------------
    public function fileStore($path = '')
    {
        $path = ltrim($path, '/');
        return storage_path("projects/{$this->uid}/{$path}");
    }

    public function repoPath()
    {
        return $this->fileStore("/{$this->local_repo_name}");
    }

    public function backupDir($value = '')
    {
        return $this->fileStore('/backups/').str_slug($value);
    }

    public function keyPath($value = '')
    {
        return $this->fileStore('/keys/').$value;
    }

    //----------------------------------------------------------
    // Attrs
    //-------------------------------------------------------
    public function getPubkeyPathAttribute()
    {
        return $this->keyPath('id_rsa.pub');
    }

    public function getSshkeyAttribute()
    {
        return $this->keyPath('id_rsa');
    }

    public function getPubkeyAttribute()
    {
        $path = $this->keyPath('id_rsa.pub');
        if (file_exists($path)) {
            return file_get_contents($path);
        }
    }

    public function getRepoPathAttribute()
    {
        return $this->repoPath();
    }

    public function getBranchAttribute($value = null)
    {
        return $value ?: 'master';
    }

    public function getLocalRepoNameAttribute($value = '')
    {
        return 'repo';
    }

    public function getChannelIdAttribute($value = '')
    {
        return "project.{$this->id}";
    }

    public function getRepoSizeAttribute($value = '')
    {
        $path = $this->repoPath();
        $builder = new ProcessBuilder(['/usr/bin/du']);
        $builder->add('-ch')
                ->add("--exclude={$path}/.git")
                ->add($path);

        $proc = $builder->getProcess();
        $proc->run();
        $stdOut = $proc->getOutput();
        $lines = array_filter(explode(PHP_EOL, $stdOut));
        list($size, $repo) = explode("\t", end($lines), 2);

        return $size;
    }

    //----------------------------------------------------------
    // Relationships
    //-------------------------------------------------------
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function servers()
    {
        return $this->hasMany('App\Models\Server')->order();
    }

    public function configs()
    {
        return $this->hasMany('App\Models\Config')->order();
    }

    public function history()
    {
        return $this->hasMany('App\Models\History')->take(100)->order();
    }

    public function scripts()
    {
        return $this->hasMany('App\Models\Script')->order();
    }

    public function preinstall_scripts()
    {
        return $this->scripts()->where('run_pre_deploy', true);
    }

    public function postinstall_scripts()
    {
        return $this->scripts()->where('run_pre_deploy', false);
    }

    //----------------------------------------------------------
    // Deployment Status
    //-------------------------------------------------------
    public function cloningCacheKey()
    {
        return "cloning_{$this->id}";
    }

    public function getIsCloningAttribute($value = false)
    {
        return Cache::has($this->cloningCacheKey()) ? true : false;
    }

    public function setIsCloningAttribute($value = false)
    {
        if ($value) {
            Cache::put($this->cloningCacheKey(), $value, 5);
        } else {
            Cache::forget($this->cloningCacheKey());
        }
    }

    //----------------------------------------------------------
    // Deployment Status
    //-------------------------------------------------------
    public function deploymentCacheKey()
    {
        return "deployment.project.{$this->id}";
    }

    public function getIsDeployingAttribute($value = false)
    {
        return Cache::has($this->deploymentCacheKey()) ? true : false;
    }

    public function setIsDeployingAttribute($value = false)
    {
        if ($value) {
            Cache::put($this->deploymentCacheKey(), $value, 1);
        } else {
            Cache::forget($this->deploymentCacheKey());
        }
    }

    //----------------------------------------------------------
    // Scopes & Lookups
    //-------------------------------------------------------
    public function scopeGetUserModel($query, $id)
    {
        $uid = Auth::user() ? Auth::user()->id : -1;

        return $query->where('user_id', $uid)
            ->where('id', $id)
            ->firstOrFail();
    }

    public function scopeFindServer($query, $id, $model_id)
    {
        return $query->getUserModel($id)->servers()->findOrFail($model_id);
    }

    public function scopeFindConfig($query, $id, $model_id)
    {
        return $query->getUserModel($id)->configs()->findOrFail($model_id);
    }

    public function scopeFindScript($query, $id, $model_id)
    {
        return $query->getUserModel($id)->scripts()->findOrFail($model_id);
    }

    public function scopeOrder($query)
    {
        return $query->orderBy('name');
    }

    //----------------------------------------------------------
    // Booting
    //-------------------------------------------------------
    protected static function boot()
    {
        parent::boot();

        // Register for events
        static::creating(function($model){
            $model->uid = uniqid();
            $model->user_id = Auth::user()->id;
        });

        // Register for events
        static::created(function($model){
            $keyer = new SSHKeyer();
            $res = $keyer->generate($model->keyPath(), true);
        });

        static::deleting(
            function ($model) {
                if($model->uid){
                    \File::deleteDirectory($model->fileStore());
                }
                // Clears any cached keys
                $model->is_cloning = false;
                $model->is_deploying = false;
            }
        );
    }
}
