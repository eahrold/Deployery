<?php

namespace App\Http\Resources;

use App\Http\Resources\Exceptions\ResourceException;
use App\Http\Resources\ResourceCollection;
use Illuminate\Http\Resources\Json\Resource as ConcreteResource;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Str;

class Resource extends ConcreteResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $this->preProcess();
        $this->setVisibility($this->resource);

        return $this->postProcess(
            $this->include($request, parent::toArray($request))
        );
    }

    protected function include($request, array $data)
    {
        if(is_array($this->whenLoadedIncludes)) {
            foreach ($this->whenLoadedIncludes as $include) {
                if(! $this->whenLoaded($include) instanceof MissingValue) {
                    if (method_exists($this, $fnc = 'include'.Str::studly($include))) {
                        $data[$include] = $this->{$fnc}();
                    } else {
                        $class = static::class;
                        throw new ResourceException("Method {$fnc} does not exist on {$class}");
                    }
                }
            }
        }
        return $data;
    }

    protected function guard()
    {
        return \Auth::guard();
    }

    protected function user() {
        return $this->guard()->user();
    }

    protected function preProcess()
    {
        //
    }

    protected function postProcess(array $data)
    {
        return $data;
    }

    /**
     * Set Visibility on a model.
     * @param [type] $model [description]
     */
    protected function setVisibility($model)
    {
        if ($this->visible) {
            $model->makeVisible($this->visible);
        }

        else if(($user = $this->user()) && method_exists($user, 'visibleFieldsForModel')) {
            $this->visible = $user->visibleFieldsForModel($model);
            $model->makeVisible($this->visible);
        }

        return $this;
    }

    /**
     * Proxy To Eager Load Resource Relations
     *
     * @param  array  $relations related items
     * @return $this
     */
    public function load($relations) {
        $relations = is_string($relations) ? func_get_args() : $relations;
        $this->resource->loadMissing($relations);

        return $this;
    }

    /**
     * Create new anonymous resource collection.
     *
     * @param  mixed  $resource
     * @return mixed
     */
    public static function collection($resource, $with=null)
    {
        if(is_array($with)) {
            $resource->load($with);
        }
        return new ResourceCollection($resource, static::class);
    }
}
