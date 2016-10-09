<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    protected $model;
    protected $request;

    public function __construct(BaseRequest $request, Model $model)
    {
        $this->model = $model;
        $this->request = $request;
    }

    /**
     * Checkboxes will not return the data for off state,
     * so we'll ensure the boolean fields are set here
     *
     * @param  Model  $model the model
     * @param  array  $keys  expected boolean keys
     *
     * @return void
     */
    protected function setBooleansForModel(Model $model, array $keys)
    {
        foreach ($keys as $key) {
            $model->{$key} = $this->request->get($key) ? true : false;
        }
    }
}
