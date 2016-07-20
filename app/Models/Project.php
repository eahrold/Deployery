<?php

namespace App\Models;

use App\Services\SSHKeyer;

class Project extends Base
{

    public $validationRules = [
        'name'=>'required|string',
        'repo'=>'required|string',
    ];

    protected $fillable = [
        'name',
        'slug',
        'repo',
        'branch'
    ];

    protected $appends = [
        'pubkey',
    ];

    public function initializeProject($data){
        $data['slug'] = str_slug($data['name']);
        $model = $this->create($data);

        $keyer = new SSHKeyer();
        $res = $keyer->generate($model->keyPath(), true);

        return $model;
    }

    //----------------------------------------------------------
    // Util
    //-------------------------------------------------------
    public function projectStore($path=''){
        return storage_path("projects/{$this->id}").$path;
    }

    public function repoPath(){
        return $this->projectStore($this->local_repo_name);
    }

    public function backupDir($value=''){
        return $this->projectStore('/backups/').str_slug($value);
    }

    public function keyPath($value=''){
        return $this->projectStore('/keys/').$value;
    }

    //----------------------------------------------------------
    // Attrs
    //-------------------------------------------------------
    public function getPubkeyAttribute(){
        return file_get_contents($this->keyPath('id_rsa.pub'));
    }

    public function getLocalRepoNameAttribute($value=''){
        return 'repo';
    }

    //----------------------------------------------------------
    // Relationships
    //-------------------------------------------------------
    public function servers(){
        return $this->hasMany('App\Models\Server');
    }

    public function configs(){
        return $this->hasMany('App\Models\Config');
    }

    public function history(){
        return $this->hasMany('App\Models\History');
    }
}
