<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseRequest;
use App\Models\Script;
use App\Models\Project;

final class ScriptsController extends ProjectChildController
{
    public function __construct(BaseRequest $request, Script $model)
    {
        parent::__construct($request, $model);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($project_id)
    {
        $project = $this->project->getUserModel($project_id);
        return view('pages.script_form', [
            'model' => $this->model,
            'project' => $project
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($project_id)
    {
        $this->validate(
            $this->request,
            $this->model->getValidationRules()
        );

        $project = $this->project->getUserModel($project_id);
        $data = $this->request->all();

        $model = $this->model->newInstance($data);
        $project->scripts()->save($model);

        $model->servers()->sync($this->request->get('server_ids') ?: []);

        if ($this->request->get('exit')) {
            return redirect()->route('projects.edit', $project_id);
        }
        return redirect()->route('scripts.edit', [$project_id, $model->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($project_id, $id)
    {
        $model = $this->project->findScript($project_id, $id);
        return view('pages.script_form', [
            'model' => $model,
            'project' => $model->project
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($project_id, $id)
    {
        $model = $this->project->findScript($project_id, $id);
        $this->validate(
            $this->request,
            $model->getValidationRules()
        );

        $model->fill($this->request->all());
        $dirty = $model->isDirty();
        $success = $model->save() || !$dirty;

        $model->servers()->sync($this->request->get('server_ids') ?: []);

        if ($this->request->get('exit') && $success) {
            return redirect()->route('projects.edit', $project_id);
        }
        return redirect()->route('scripts.edit', [$project_id, $model->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($project_id, $id)
    {
        $model = $this->project->findScript($project_id, $id);
        if ($model->delete()) {
            return redirect()->route('projects.edit', $project_id);
        }
        return redirect()->route('scripts.edit', [$project_id, $model->id]);
    }
}
