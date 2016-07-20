<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseRequest;
use App\Models\Base;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    protected $model;
    protected $request;

    public function __construct(BaseRequest $request, Base $model){
        $this->model = $model;
        $this->request = $request;
    }

}
