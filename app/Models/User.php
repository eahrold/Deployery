<?php

namespace App\Models;

use App\Services\SSHKeyer;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Mpociot\Teamwork\Traits\UserHasTeams;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use UserHasTeams;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'uid'
    ];


    public function getValidationRules($user_id = null)
    {
        if ($user_id) {
            return [
                'username' => "required|max:255|unique:users,username,{$user_id}",
                'email' =>    "required|email|max:255|unique:users,email,{$user_id}",
                'password' => 'min:6|confirmed'
            ];
        }
        return [
            'username' => 'required|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed'
        ];
    }


    public function getFullNameAttribute($value = "")
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function projects()
    {
        return $this->hasMany('App\Models\Project')->order();
    }

    public function fileStore($path = '')
    {
        $path = ltrim($path, '/');
        return storage_path("users/{$this->uid}/{$path}");
    }

    //----------------------------------------------------------
    // Api Auth
    //-------------------------------------------------------
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getApiTokenAttribute($value = '')
    {
        if ($this === \Auth::user()) {
            return \JWTAuth::fromUser($this);
        }
    }

    //----------------------------------------------------------
    // SSH
    //-------------------------------------------------------
    public function keyPath($value = '')
    {
        return $this->fileStore("/keys/{$value}");
    }

    public function getPubkeyPathAttribute()
    {
        return $this->keyPath('id_rsa.pub');
    }

    public function getAuthKeyAttribute()
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

    //----------------------------------------------------------
    // Booting
    //-------------------------------------------------------
    protected static function boot()
    {
        parent::boot();

        // Register for events
        static::creating(function ($model) {
            $model->uid = uniqid();
        });

        static::created(function ($model) {
            $keyer = new SSHKeyer();
            $keyer->generate($model->keyPath(), true);
        });

        static::deleting(
            function ($model) {
                if ($model->uid) {
                    \File::deleteDirectory($model->fileStore());
                }
            }
        );
    }
}
