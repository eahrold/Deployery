<?php
namespace App\Transformers;

class ConfigTransformer extends Transformer
{

    protected $defaultIncludes = [];
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
}
