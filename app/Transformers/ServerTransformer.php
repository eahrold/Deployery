<?php
namespace App\Transformers;

use App\Transformers\HistoryTransformer;

class ServerTransformer extends Transformer
{

    protected $defaultIncludes = [];
    protected $availableIncludes = ['history', 'project', 'configs'];

    protected $mappedKeys = [];
    protected $guardedProperties =[];

    public function includeHistory($model)
    {
        if ($data = $model->history) {
            return $this->collection($data, new HistoryTransformer, false);
        }
    }

    public function includeProject($model)
    {
        return $this->item($model->project, new ProjectTransformer, false);
    }

    public function includeConfigs($model)
    {
        return $this->collection($model->configs, new ConfigTransformer, false);
    }
}
