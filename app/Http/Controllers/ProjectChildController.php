<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseRequest;
use App\Models\Base;
use App\Models\Project;

/**
 * Abstract Class for Resource Routes with
 * models which are owned by a proejct.
 */
abstract class ProjectChildController extends Controller
{
    protected $project;

    public function __construct(BaseRequest $request, Base $model)
    {
        $this->project = new Project();
        parent::__construct($request, $model);
    }
}
