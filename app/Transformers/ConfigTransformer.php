<?php
namespace App\Transformers;

use League\Fractal\Resource\Collection;

class ConfigTransformer extends Transformer
{

    protected $defaultIncludes = ['servers'];
    protected $mappedKeys = [];
    protected $guardedProperties = [];

    /**
     * Perform any post processing tasks to the transformed array
     *
     * @param  array  $data array to process
     * @return array        processed array
     */
    protected function postProcess(array $data)
    {
        if (in_array('server_ids', $this->exposedProperties)) {
            $data['server_ids'] = $this->model->servers->pluck('id');
        }
        return $data;
    }

    protected function includeServers($config)
    {
        return new Collection($config->servers, function($item){
            return [
                'id' => $item->id,
                'name' => $item->name,
            ];
        }, false);
    }
}
