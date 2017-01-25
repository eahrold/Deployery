<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

abstract class Base extends Model
{

    /**
     * An array of validaion rules
     *
     * @var array
     */
    protected $validation_rules = [];

    /**
     * Unique Keys in the list of validation rules
     *
     * @var array
     */
    protected $unique_validation_keys = [];

    /**
     * Get the validation rules
     *
     * @param  int $id        id of model to check for unique against
     * @param  array  $append any additional rule to appen
     * @return array          validation rules
     */
    public function getValidationRules($id = null, $append=[])
    {
        $unique = $this->validation_rules;
        foreach ($this->unique_validation_keys as $key) {
            $rule = $this->validation_rules[$key];
            if ($id = $id ?: $this->id) {
                $unique[$key] = "{$rule}|unique:{$this->getTable()},{$key},{$id}";
            } else {
                $unique[$key] = "{$rule}|unique:{$this->getTable()}";
            }
        }
        return array_merge($unique, $append);
    }

    /**
     * Order the models
     * @param  Builder $query Query builder
     * @return Builder        Query builder
     */
    public function scopeOrder($query)
    {
        return $query->orderBy('created_at');
    }
}
