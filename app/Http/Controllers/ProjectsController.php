<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseRequest;
use App\Models\Project;

final class ProjectsController extends Controller
{

    public function __construct(BaseRequest $request, Project $model) {
        parent::__construct($request, $model);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function view($id=null) {
        return view('pages.dashboard');
    }
}
