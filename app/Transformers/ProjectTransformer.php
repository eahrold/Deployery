<?php
namespace App\Transformers;

use App\Transformers\ServerTransformer;

class ProjectTransformer extends Transformer
{
    protected $defaultIncludes = [];
    protected $mappedKeys = [];
    protected $guardedProperties =[];

    public function includeServers($model)
    {
        if ($data = $model->servers) {
            return $this->collection($data, new ServerTransformer, false);
        }
    }
}
