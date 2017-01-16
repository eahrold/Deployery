<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseRequest;
use App\Models\Server;
use Illuminate\Support\Facades\Log;

final class ServersController extends ProjectChildController
{

    public function __construct(BaseRequest $request, Server $model)
    {
        parent::__construct($request, $model);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param number $project_id Project Id
     *
     * @return \Illuminate\Http\Response
     */
    public function create($project_id)
    {
        $project = $this->project->getUserModel($project_id);
        return view('pages.server_form', [
            'model' => $this->model,
            'project' => $project
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param number $project_id Project Id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($project_id)
    {
        $project = $this->project->getUserModel($project_id);
        $path = $project->repo_path;
        $key = $project->user->auth_key;

        $this->validate(
            $this->request,
            $this->model->getValidationRules(null, ['branch'=>"gitBranch:{$path},{$key}"])
        );

        $data = $this->request->all();
        $data["project_id"] = $project_id;

        $model = $this->model->newInstance($data);
        $project->servers()->save($model);

        $model->scripts()->sync($this->request->get('script_ids') ?: []);

        if ($this->request->get('exit')) {
            return redirect()->route('projects.edit', $project_id);
        }
        return redirect()->route('servers.edit', [$project_id, $model->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $project_id Project Id
     * @param int $id         Server Id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($project_id, $id)
    {
        $model = $this->project->findServer($project_id, $id);
        $project = $model->project;
        return view('pages.server_form', compact('model', 'project'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $project_id Project Id
     * @param int $id         Server Id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($project_id, $id)
    {
        $model = $this->project->findServer($project_id, $id);
        $path = $model->repo;
        $key = $model->project->user->auth_key;

        $rules = $model->getValidationRules($id, ['branch'=>"git_branch:{$path},{$key}"]);
        $this->validate(
            $this->request,
            $rules
        );

        $model->fill($this->request->all());

        $this->setBooleansForModel($model, [
            'autodeploy',
            'use_ssk_key',
            'send_slack_messages'
        ]);


        $dirty = $model->isDirty();
        $success = $model->save() || !$dirty;

        $model->scripts()->sync($this->request->get('script_ids') ?: []);
        if ($success && $this->request->get('exit')) {
            Log::info("Redirecting to project route");
            return redirect()->route('projects.edit', $project_id);
        }
        return redirect()->route('servers.edit', [$project_id, $model->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $project_id Project Id
     * @param int $id         Server Id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($project_id, $id)
    {
        $model = $this->project->findServer($project_id, $id);
        if ($model->delete()) {
            return redirect()->route('projects.edit', $project_id);
        }
        return redirect()->route('servers.edit', [$project_id, $model->id]);
    }

    /**
     * Show the deployment page for the server.
     *
     * @param int $project_id Project Id
     * @param int $id         Server Id
     *
     * @return \Illuminate\Http\Response
     */
    public function deploy($project_id, $id)
    {
        $model = $this->project->findServer($project_id, $id);
        return view('pages.server_deploy', [
            'model' => $model,
            'project' => $project_id
        ]);
    }
}
