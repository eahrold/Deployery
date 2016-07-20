<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Base
{

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

    public function getConfigAttribute(){
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

    public function getWebhookAttribute($value=''){
        if(!$value){
            return url('/webhooks/'.str_random(32));
        }
        return $value;
    }

    public function getProtocolAttribute($value=''){
        return $value ?: 'ssh';
    }

    public function getPortAttribute($value=null)
    {
        return $value ?: 22;
    }

    public function getSlugAttribute($value){
        return str_slug($this->name);
    }

    public function project(){
        return $this->belongsTo('App\Models\Project');
    }

    public function configs(){
        return $this->hasManyThrough('App\Models\Config', 'App\Models\Project');
    }

    public function history(){
        return $this->hasManyThrough('App\Models\History', 'App\Models\Project');
    }
}
