<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BaseRequest as Request;
use App\Models\Base;

abstract class APIController extends Controller
{
    /**
     * @var \App\Http\Requests\BaseRequest
     */
    protected $request;

    /**
     * @var \App\Models\Base
     */
    protected $model;

    public function __construct(Request $request, Base $model)
    {
        $this->request = $request;
        $this->model = $model;
    }

    /**
     * Get Options For Project
     *
     * @param  integer $project_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function options ($project_id=null)
    {
        return  response()->json([
            'options' => new \stdClass
        ]);
    }

    /**
     * Validate request with api error response
     *
     * @param  Request $request request object
     * @param  array   $rules   validation rules
     * @throws UpdateResourceFailedException on validation failure
     * @return void
     */
    protected function apiValidate(Request $request, array $rules)
    {
        $validator = \Validator::make($request->all(), $rules);
        $validator->validate();
    }
}
