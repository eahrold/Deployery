<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

/**
 * Bcrypts passwords from the request on save.
 */
trait OrderableTrait {

    public function scopeDefaultOrder($query)
    {
        $query->orderBy('created_at', 'DESC');
    }

    private function guessForeingKey($relation, $table){
        if (method_exists($relation, 'getQualifiedForeignKeyName')) {
            return $relation->getQualifiedForeignKeyName();
        }
        return "{$table}.{$relation->getQualifiedRelatedPivotKeyName()}";
    }

    private function getKeysForRelation($relation) {

        $related_table = $relation->getRelated()->getTable();

        switch (class_basename($relation)) {
            case 'BelongsTo':
                $parent_key = $relation->getQualifiedOwnerKeyName();
                $foreing_key = $this->guessForeingKey($relation, $this->getTable());
                break;
            case 'MorphToLatest':
                $parent_key = $relation->getQualifiedParentKeyName();
                $foreing_key = $this->guessForeingKey($relation, $related_table);
                break;
            default:
                $parent_key = $relation->getQualifiedParentKeyName();
                $foreing_key = $this->guessForeingKey($relation, $related_table);
                break;
        }
        return [$related_table, $parent_key, $foreing_key];
    }

    public function scopeRelatedOrder($query, $relation_name, $column, $order = 'DESC') {

        if(!method_exists($this, $relation_name)) {
            return $query;
        }

        $relation = $this->$relation_name();

        list($related_table, $parent_key, $foreing_key) = $this->getKeysForRelation($relation);

        if (empty($query->columns)) {
            $query->select($this->getTable().".*");
        }

        $rord = "{$related_table}_{$column}";
        $query->addSelect(\DB::raw(
            "(select max(`{$column}`) from {$related_table} where {$parent_key} = {$foreing_key}) AS `{$rord}`")
        );

        return $query->orderBy($rord, $order);
    }


    public function scopeRelatedCount($query, $count_relation, $order = 'DESC') {
        $relation_name = str_replace('_count', '', $count_relation);
        if(!method_exists($this, $relation_name)) {
            return $query;
        }
        return $query->withCount($relation_name)->orderBy($count_relation, $order);
    }

    public function scopeOrder($query, $options=[])
    {
        $order = isset($options['order']) ? $options['order'] : null;
        $direction = isset($options['direction']) ? $options['direction'] : 0;
        $dir = $direction > 0 ? "DESC" : "ASC";

        if (!is_string($order) || !is_numeric($direction)) {
            return $query->defaultOrder();
        }

        if (\Schema::hasColumn($this->getTable(), $order)) {
            $query->orderBy($order, $dir);
        }

        if (Str::contains($order, '.')) {
            $list = explode('.', $order);
            logger("Relation", $list);

            if(count($list) == 2) {
                list($relation, $column) = $list;
                $query->relatedOrder($relation, $column, $dir);
            }
        }
        if (Str::contains($order, '_count')) {
            $query->relatedCount($order, $dir);
        }

        return $query;
    }
}