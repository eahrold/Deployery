<?php
namespace App\Transformers;

class ProjectTransformer extends Transformer
{
    protected $defaultIncludes = [];
    protected $mappedKeys = [];
    protected $guardedProperties =[];

    public function includeServers($model)
    {
        if ($data = $model->blocks) {
            return $this->collection($data, new BlockTransformer, false);
        }
    }
}
