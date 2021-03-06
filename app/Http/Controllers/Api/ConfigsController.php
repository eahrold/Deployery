<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\BaseRequest;
use App\Models\Project;
use App\Transformers\ConfigTransformer;

class ConfigsController extends APIController
{

    private $project;

    public function __construct(BaseRequest $request, Project $project, ConfigTransformer $transformer)
    {
        $this->project = $project;
        parent::__construct($request, $project->configs()->getModel(), $transformer);
    }

    /**
     * Store a newly created resource in storage.
     *
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
        $project->configs()->save($model);

        $model->servers()->sync($this->request->get('server_ids') ?: []);

        return $this->response->item($model, $this->transformer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($project_id, $id)
    {
        $this->validate(
            $this->request,
            $this->model->getValidationRules($id)
        );

        $model = $this->project->findConfig($project_id, $id);
        $model->update($this->request->all());
        $model->servers()->sync($this->request->get('server_ids') ?: []);

        return $this->response->item($model, $this->transformer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($project_id, $id)
    {
        $model = $this->project->findConfig($project_id, $id);
        if (!$model->delete()) {
            $this->response->error('Could not delete the configuration file.', 422);
        }
        return $this->response->array([
            'message'=>'Successfully deleted the config file.',
            'status_code'=>'200'
        ]);
    }
}
