<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

abstract class Base extends Model
{

    protected $validation_rules = [];
    protected $unique_validation_keys = [];

    /**
     * @param integer $id
     */
    public function getValidationRules($id = null, $append=[])
    {
        $unique = $this->validation_rules;
        foreach ($this->unique_validation_keys as $key) {
            $rule = $this->validation_rules[$key];
            if ($id ?: $this->id) {
                $unique[$key] = "{$rule}|unique:{$this->getTable()},{$key},{$this->id}";
            } else {
                $unique[$key] = "{$rule}|unique:{$this->getTable()}";
            }
        }
        return array_merge($unique, $append);
    }

    public function scopeOrder($query)
    {
        return $query->orderBy('created_at');
    }
}
