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
     * Any properties to purge on unauthenticated requests
     *
     * @var array
     */
    protected $guardedProperties = [];

    /**
     * Any properties to expose
     *
     * @var array
     */
    protected $exposedProperties = [];

    /**
     * Transfrom the model
     *
     * @param   App\Models\Base  $model model object
     * @return  array            transformed model
     */
    public function transform(Base $model)
    {
        $model = $this->guardData(
            $this->exposeProperties($model)
        );
        $model = $this->model = $this->preProcess($model);

        $data = $this->mapData($model->toArray());

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

    protected function authUser()
    {
        return app('Dingo\Api\Auth\Auth')->user() ?: Auth::user();
    }

    /**
     * Make the given, typically hidden, attributes visible.
     *
     * @param  array|string  $attributes
     * @return $this
     */
    public function makeVisible($attributes)
    {
        $this->exposedProperties = (array) $attributes;
        return $this;
    }

    /**
     * Handle the internals of exposing the attributes.
     *
     */
    protected function exposeProperties($model)
    {
        $model->makeVisible($this->exposedProperties);
        return $model;
    }


    /**
     *  Hide properties if the user isn't authenticated
     *
     * @param  Base   $model model in
     * @return Base          model out
     */
    protected function guardData($model)
    {
        if (!$this->authUser()) {
            $model->makeHidden($this->guardedProperties);
        }
        return $model;
    }
}
