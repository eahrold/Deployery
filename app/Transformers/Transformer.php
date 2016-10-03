<?php
namespace App\Transformers;

use App\Models\Base;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use League\Fractal;
use League\Fractal\Manager;

abstract class Transformer extends Fractal\TransformerAbstract
{

    /**
     * The model being transformed
     *
     * @var Base | Eloquent
     */
    protected $model;

    /**
     * Map the keys from one value to another
     * the structure  for each item is fromKey => toKey
     * For example if you want to convert this_is_great
     * to thisCouldBeBetter you specify
     *
     * ['this_is_great' => 'thisCouldBeBetter',
     *  'some_other_key' => 'someOtherKey'
     * ]
     *
     * @var associative array
     */
    protected $mappedKeys = [];


    /**
     * Transfrom the model
     *
     * @param   App\Models\Base  $model model object
     * @return  array            transformed model
     */
    public function transform(Base $model)
    {
        $this->model = $this->preProcess($model);

        $data = $this->mapData($model->toArray());
        $data = $this->guardData($data);

        return $this->postProcess($data);
    }

    /**
     * Perform any preprocessing to the model
     *
     * @param  Base   $model model object
     * @return Base          updated object
     */
    protected function preProcess(Base $model)
    {
        return $model;
    }

    /**
     * Perform any post processing tasks to the transformed array
     *
     * @param  array  $data array to process
     * @return array        processed array
     */
    protected function postProcess(array $data)
    {
        return $data;
    }

    /**
     * Map data based on the $mappedKeys()
     *
     * @param  array $item data in
     * @return array       data out
     */
    protected function mapData($item)
    {
        foreach ($this->mappedKeys as $fromKey => $toKey) {
            if (key_exists($fromKey, $item)) {
                $item[$toKey] = $item[$fromKey];
                unset($item[$fromKey]);
            }
        }
        return $item;
    }

   /**
     *  Remove guarded properties from the array
     *
     * @param  array $item data in
     * @return array       data out
     */
    protected function guardData($item)
    {
        if (!Auth::user()) {
            foreach ($this->guardedProperties as $key) {
                if (isset($item[$key])) {
                    unset($item[$key]);
                }
            }
        }
        return $item;
    }
}
