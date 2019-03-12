<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\BaseRequest;
use App\Http\Resources\Management\ConfigResource;
use App\Models\Project;

class ConfigsController extends APIController
{
    /**
     * Project
     * @var \App\Models\Project
     */
    private $project;

    public function __construct(BaseRequest $request, Project $project)
    {
        $this->project = $project;
        parent::__construct($request, $project->configs()->getModel());
    }

    /**
     * Get the resource
     *
     * @param  integer $project_id
     * @param  integer $id
     *
     * @return ConfigResource
     */
    public function show($project_id, $id)
    {
        $model = $this->project->findConfig($project_id, $id);
        $this->authorize($model->project);

        $model->server_ids = $model->servers()->get()->pluck('id');

        return new ConfigResource($model);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return ConfigResource
     */
    public function store($project_id)
    {
        $data = $this->request->validate(
            $this->model->getValidationRules()
        );

        $project = $this->project->getUserModel($project_id);
        $this->authorize($project);

        $data = $this->request->all();
        $model = $this->model->newInstance($data);

        $model = \DB::transaction(function() use ($project, $model) {
            $project->configs()->save($model);
            $server_ids = $this->request->get('server_ids');
            if( !! $server_ids ) {
                $model->servers()->sync($server_ids);
            }
            $model->server_ids = $server_ids;
            return $model;
        });

        return new ConfigResource($model);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  integer  $project_id
     * @param  integer  $id
     *
     * @return ConfigResource
     */
    public function update($project_id, $id)
    {
        $data = $this->request->validate(
            $this->model->getValidationRules($id)
        );

        $model = $this->project->findConfig($project_id, $id);
        $this->authorize($model->project);

        $model = \DB::transaction(function() use ($model, $data) {
            $model->update($this->request->all());

            $server_ids = data_get($data, 'server_ids');
            if(is_array($server_ids)) {
                logger("Setting NO Server IDS", compact('data', 'server_ids'));
                $model->servers()->sync($server_ids);
            }

            $model->server_ids = $server_ids;
            return $model;
        });

        return new ConfigResource($model);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  integer  $project_id
     * @param  integer  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($project_id, $id)
    {
        $model = $this->project->findConfig($project_id, $id);
        $this->authorize($model->project);

        abort_unless($model->delete(), 422, 'Could not delete the configuration file.');

        return response()->json([
            'message'=>'Successfully deleted the config file.',
            'status_code'=>'200'
        ]);
    }

    /**
     * Get the relational options for the form
     *
     * @param  integer|null $project_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function options ($project_id=null)
    {
        $project = $this->project->findOrFail($project_id);
        $this->authorize('update', $project);

        $servers = $project->servers->pluck('name', 'id');
        return response()->json([
            'options' => compact('servers'),
        ]);
    }
}
