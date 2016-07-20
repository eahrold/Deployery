<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseRequest;
use App\Models\Project;

class ProjectsController extends Controller
{

    public function __construct(BaseRequest $request, Project $model){
        parent::__construct($request, $model);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $projects = $this->model->all();
        return view('pages.projects', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        return view('pages.projects_form', ['model' => $this->model]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(){
        $model = $this->model->initializeProject($this->request->all());
        if($this->request->get('exit')){
            return redirect()->route('projects.index');
        }
        return redirect()->route('projects.edit', $model->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $model = $this->model->with('servers', 'history')->find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        $model = $this->model->findOrFail($id);
        return view('pages.projects_form', compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id){
        $model = $this->model->findOrFail($id);
        $model->update($this->request->all());
        if($this->request->get('exit')){
            return redirect()->route('projects.index');
        }
        return redirect()->route('projects.edit', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        $model = $this->model->findOrFail($id);
        if($model->delete()){
            return redirect()->route('projects.index');
        }
        return redirect()->route('projects.edit', $id);
    }
}
