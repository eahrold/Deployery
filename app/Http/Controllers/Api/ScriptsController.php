<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\BaseRequest;
use App\Models\Project;
use App\Transformers\ScriptTransformer;

class ScriptsController extends APIController
{
    private $projects;

    public function __construct(BaseRequest $request, Project $project, ScriptTransformer $transformer)
    {
        $this->projects = $project;
        parent::__construct($request, $project->scripts()->getModel(), $transformer);
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

        $project = $this->projects->findOrFail($project_id);
        $data = $this->request->all();

        $model = $this->model->newInstance($data);
        $project->scripts()->save($model);

        $model->servers()->sync($this->request->get('server_ids') ?: []);
        return $this->response->item($model, $this->transformer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $project_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($project_id, $id)
    {
        $this->validate(
            $this->request,
            $this->model->getValidationRules($id)
        );
        $model = $this->projects->findScript($project_id, $id);
        $model->update($this->request->all());
        $model->servers()->sync($this->request->get('server_ids') ?: []);

        return $this->response->item($model, $this->transformer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $project_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($project_id, $id)
    {
        $model = $this->model->findOrFail($id);
        if (!$model->delete()) {
            $this->response->error('Could not detete the model.', 422);
        }
        return $this->response->array([
            'message'=>'Successfully deleted the install script.',
            'status_code'=>'200'
        ]);
    }
}
