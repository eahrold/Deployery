<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BaseRequest as Request;
use App\Models\Base;
use App\Transformers\Transformer;
use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class APIController extends Controller
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
}
