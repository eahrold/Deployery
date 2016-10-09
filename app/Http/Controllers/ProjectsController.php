<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseRequest;
use App\Jobs\RepositoryClone;
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('pages.project', ['model' => $this->model]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store() {
        $model = $this->model->initializeProject($this->request->all());
        $clone = (new RepositoryClone($model))->onQueue('clones');
        $this->dispatch($clone);

        // Mark the model as "Cloning".  It's actually a
        // cache store so there is no need to save it.
        $model->is_cloning = true;

        return redirect()->route('projects.edit', $model->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($id){
        return redirect()->route('projects.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $with = ['servers', 'history', 'history.server', 'scripts', 'configs'];
        $model = $this->model->with($with)->getUserModel($id);

        return view('pages.project', compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id) {
        $model = $this->model->getUserModel($id);
        $model->update($this->request->all());

        if(!file_exists($model->repoPath())){
            $clone = (new RepositoryClone($model))->onQueue('clones');
            $this->dispatch($clone);
        }

        return redirect()->route('projects.edit', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id) {
        $model = $this->model->getUserModel($id);
        if ($model->delete()) {
            return redirect()->route('projects.index');
        }
        return redirect()->route('projects.edit', $id);
    }
}
