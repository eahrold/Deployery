<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ServerRequest;
use App\Jobs\ServerDeploy;
use App\Models\Project;
use App\Models\Server;
use App\Services\Git\GitInfo;
use App\Transformers\ServerTransformer;
use Illuminate\Support\Facades\Auth;

class ServersController extends APIController
{

    /**
     * Project
     * @var \App\Models\Project
     */
    private $projects;

    public function __construct(ServerRequest $request, Project $project, ServerTransformer $transformer)
    {
        $this->projects = $project;
        parent::__construct($request, $project->servers()->getModel(), $transformer);
    }

    //----------------------------------------------------------
    // Resource Endpoints
    //-------------------------------------------------------

    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $project_id
     * @return \Dingo\Api\Http\Response
     */
    public function store($project_id)
    {
        $this->apiValidate(
            $this->request,
            $this->model->getValidationRules()
        );

        $project = $this->projects->getUserModel($project_id);
        $this->authorize($project);

        $data = $this->request->all();
        $data["project_id"] = $project_id;

        $model = $this->model->newInstance($data);

        \DB::transaction(function() use ($project, $model) {
            $project->servers()->save($model);
            if($script_ids = $this->request->script_ids) {
                $model->scripts()->sync($script_ids);
            }
        });

        return $this->response->item($model, new $this->transformer);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $project_id
     * @param  int  $id
     * @return \Dingo\Api\Http\Response
     */
    public function show($project_id, $id)
    {
        $model = $this->projects->findServer($project_id, $id);
        $this->authorize($model->project);
        $model->updateGitInfo();

        return $this->response->item($model, new $this->transformer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $project_id
     * @param  int  $id
     * @return \Dingo\Api\Http\Response|null
     */
    public function update($project_id, $id)
    {
        $model = $this->projects->findServer($project_id, $id);
        $this->authorize($model->project);

        $rules = $model->getValidationRules($id);
        $this->apiValidate($this->request, $rules);

        \DB::transaction(function() use ($model) {
            $model->update($this->request->all());
            if($script_ids = $this->request->script_ids) {
                $model->scripts()->sync($script_ids);
            }
        });

        return $this->response->item($model, new $this->transformer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $project_id
     * @param  int  $id
     * @return Dingo\Api\Http\Response
     */
    public function destroy($project_id, $id)
    {
        $model = $this->projects->findServer($project_id, $id);
        $this->authorize($model->project);

        if (!$model->delete()) {
            $this->response->error('Could not detete the server.', 422);
        }

        return $this->response->array([
            'message'=>'Successfully deleted the server.',
            'status_code'=>'200'
        ]);
    }

    public function options ($project_id=null)
    {
        $protocols = [
            [ 'text' => 'ssh', 'value' => 'ssh'],
            [ 'text' => 'sftp', 'value' => 'sftp']
        ];

        $branches = [];
        if ($project = $this->projects->find($project_id)) {
            $branches = $project->branches;
        }

        return $this->response
            ->array(['options' => compact('protocols', 'branches')]);
    }


    public function test($project_id, $id)
    {
        $server = $this->projects->findServer($project_id, $id);
        $this->authorize('deploy', $server->project);

        if ($server->validateConnection()) {
            return $this->response->array([
                'success'=> true,
                'message'=> $server->present()->connection_status_message
            ]);
        }
        abort(412, $server->present()->connection_status_message);
    }

    public function pubkey($id)
    {
        $project = $this->projects->findOrFail($id);
        $this->authorize('deploy', $project);

        return $this->response->array([
            'key' => $project->pubkey
        ]);
    }


}
