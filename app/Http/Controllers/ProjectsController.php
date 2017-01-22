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
    public function index() {
        $projects = $this->model->findUserModels()->get();
        return view('pages.dashboard', compact('projects'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($id){
        $model = $this->model->getUserModel($id);
        return view('pages.project', compact('model'));
    }
}
