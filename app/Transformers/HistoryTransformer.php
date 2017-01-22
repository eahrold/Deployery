<?php
namespace App\Transformers;

use App\Transformers\ServerTransformer;

class HistoryTransformer extends Transformer
{
    protected $defaultIncludes = ['server'];
    protected $mappedKeys = [];
    protected $guardedProperties = [];

    public function includeServer($model)
    {
        if ($data = $model->server) {
            return $this->item($data, new ServerTransformer, false);
        }
    }
}
