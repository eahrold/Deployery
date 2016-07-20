<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Base extends Model
{

    public function scopeOrder(Builder $query){
        return $query->orderBy('created_at');
    }
}
