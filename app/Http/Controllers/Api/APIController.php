<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BaseRequest as Request;
use App\Models\Base;
use App\Transformers\Transformer;
use Dingo\Api\Routing\Helpers;

abstract class APIController extends Controller
{
    use Helpers;

    protected $request;
    protected $model;
    protected $transformer;

    public function __construct(Request $request, Base $model, Transformer $transformer)
    {
        $this->request = $request;
        $this->model = $model;
        $this->transformer = $transformer;
    }

    public function options ($project_id=null)
    {
        return  $this->response->array([
            'options' => new \stdClass
        ]);
    }

    protected function apiValidate(Request $request, array $rules)
    {
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $class_name = class_basename($this->model);
            throw new \Dingo\Api\Exception\UpdateResourceFailedException(
                "Could not update the {$class_name}.",
                $validator->errors()
            );
        }
    }
}
