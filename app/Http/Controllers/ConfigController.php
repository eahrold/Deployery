<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseRequest;
use App\Models\Config;
use App\Models\Project;

class ConfigController extends ProjectChildController
{
    public function __construct(BaseRequest $request, Config $model)
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
        return view('pages.config_form', [
            'model' => $this->model,
            'project' => $project
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
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
        $project->configs()->save($model);

        $model->servers()->sync($this->request->get('server_ids') ?: []);

        if ($this->request->get('exit')) {
            return redirect()->route('projects.edit', $project_id);
        }
        return redirect()->route('configs.edit', [$project_id, $model->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($project_id, $id)
    {
        $model = $this->project->findConfig($project_id, $id);
        $project = $model->project;
        return view('pages.config_form', compact('model', 'project'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($project_id, $id)
    {
        $model = $this->project->findConfig($project_id, $id);
        $this->validate(
            $this->request,
            $model->getValidationRules()
        );

        $model->fill($this->request->all());
        $dirty = $model->isDirty();
        $success = $model->save() || !$dirty;

        $model->servers()->sync($this->request->get('server_ids') ?: []);

        if ($success && $this->request->get('exit')) {
            return redirect()->route('projects.edit', $project_id);
        }
        return redirect()->route('configs.edit', [$project_id, $model->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($project_id, $id)
    {
        $model = $this->project->findConfig($project_id, $id);
        if ($model->delete()) {
            return redirect()->route('projects.edit', $project_id);
        }
        return redirect()->route('configs.edit', [$project_id, $model->id]);
    }
}
