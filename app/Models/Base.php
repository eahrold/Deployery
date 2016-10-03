<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

abstract class Base extends Model
{

    protected $validation_rules = [];
    protected $unique_validation_rules = [];

    public function getValidationRules($id = null, $append=[])
    {
        $unique = [];
        foreach ($this->unique_validation_rules as $key) {
            if ($id ?: $this->id) {
                $unique[] = [$key=>"required|unique:{$this->getTable()},{$key},{$this->id}"];
            } else {
                $unique[] = [$key => "required|unique:{$this->getTable()}"];
            }
        }
        return array_merge($this->validation_rules, $unique, $append);
    }

    public function scopeFindUserModels($query)
    {
        if (Schema::hasColumn($this->getTable(), 'user_id')) {
            $uid = Auth::user() ? Auth::user()->id : -1;
            $query->where('user_id', $uid);
        };
        return $query;
    }

    public function scopeOrder($query)
    {
        return $query->orderBy('created_at');
    }
}
