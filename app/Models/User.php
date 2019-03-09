<?php

namespace App\Models;

use App\Models\Traits\BcryptsPassword;
use App\Presenters\PresentableTrait;
use App\Services\SSH\SSHKeyer;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Mpociot\Teamwork\Traits\UserHasTeams;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use UserHasTeams;
    use PresentableTrait;
    use Notifiable;
    use BcryptsPassword;

    protected $presenter = 'App\Presenters\User';

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
        'can_join_teams',
        'can_manage_teams',
        'is_admin',
        'uid'
    ];

    public static function getValidationRules($user_id = null)
    {
        if ($user_id) {
            return [
                'username' => "required|max:255|unique:users,username,{$user_id}",
                'email' =>    "required|email|max:255|unique:users,email,{$user_id}",
                'first_name' => 'sometimes|string',
                'last_name' => 'sometimes|string',
                'password' => 'sometimes|min:6|confirmed|nullable'
            ];
        }

        return [
            'username' => 'required|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'first_name' => 'sometimes|string',
            'last_name' => 'sometimes|string',
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
    // Teams
    //-------------------------------------------------------
    /**
     * Wrapper method for "isOwner"
     *
     * @return bool
     */
    public function isTeamMember($team)
    {
        return $this->teams->contains('id', is_numeric($team) ? $team : $team->id);
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
        if ($this->id === data_get(\Auth::user(), 'id')) {
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
            return trim(file_get_contents($path));
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

            if($token = \Session::pull('invite_token')){
                $invite = \Teamwork::getInviteFromAcceptToken($token);
                $model->attachTeam($invite->team);
                $invite->delete();
            }
        });

        static::deleted(function ($model) {
            // Make sure the project is not completley lost
            // This way at least some member of the team
            // will still have access to the project.
            $model->projects->each(function($project) use ($model) {
                $owner = $project->team->owner;
                if($owner->id !== $model->id){
                    $project->user_id = $owner->id;
                } else {
                    $user = $project->team->users()->first();
                    $project->user_id = $user ? $user->id : 1;
                }
                $project->save();
            });

            if ($model->uid) {
                \File::deleteDirectory($model->fileStore());
            }
        });
    }
}
