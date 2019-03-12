<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ServerRequest;
use App\Http\Resources\Management\ServerResource;
use App\Jobs\ServerDeploy;
use App\Models\Project;
use App\Models\Server;
use App\Services\Git\GitInfo;
use Illuminate\Support\Facades\Auth;

class ServersController extends APIController
{

    /**
     * Project
     * @var \App\Models\Project
     */
    private $projects;

    public function __construct(ServerRequest $request, Project $project)
    {
        $this->projects = $project;
        parent::__construct($request, $project->servers()->getModel());
    }

    //----------------------------------------------------------
    // Resource Endpoints
    //-------------------------------------------------------

    /**
     * Store a newly created resource in storage.
     *
     * @param integer $project_id
     * @return ServerResource
     */
    public function store($project_id)
    {
        $this->request->validate(
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

        return new ServerResource($model);
    }

    /**
     * Display the specified resource.
     *
     * @param integer $project_id
     * @param integer $id
     * @return ServerResource
     */
    public function show($project_id, $id)
    {
        $model = $this->projects->findServer($project_id, $id);
        $this->authorize($model->project);

        $model->updateGitInfo();
        return new ServerResource($model);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param integer $project_id
     * @param integer $id
     * @return ServerResource
     */
    public function update($project_id, $id)
    {
        $model = $this->projects->findServer($project_id, $id);
        $this->authorize($model->project);

        $this->request->validate(
            $model->getValidationRules($id)
        );

        \DB::transaction(function() use ($model) {
            $model->update($this->request->all());
            if($script_ids = $this->request->script_ids) {
                $model->scripts()->sync($script_ids);
            }
        });

        return new ServerResource($model);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param integer $project_id
     * @param integer $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($project_id, $id)
    {
        $model = $this->projects->findServer($project_id, $id);
        $this->authorize($model->project);

        abort_unless($model->delete(), 422, 'Could not detete the server.');

        return response()->json([
            'message'=>'Successfully deleted the server.',
            'status_code'=>'200'
        ]);
    }

    /**
     * Options For Project
     *
     * @param integer $project_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
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

        return response()->json(
            ['options' => compact('protocols', 'branches')
        ]);
    }

    /**
     * Test Server Connection
     *
     * @param integer $project_id
     * @param integer $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function test($project_id, $id)
    {
        $server = $this->projects->findServer($project_id, $id);
        $this->authorize('deploy', $server->project);

        if ($server->validateConnection()) {
            return response()->json([
                'success'=> true,
                'message'=> $server->present()->connection_status_message
            ]);
        }
        abort(412, $server->present()->connection_status_message);
    }

    /**
     * [webhookReset description]
     * @param integer$project_id [description]
     * @param integer$id         [description]
     *
     * @return ServerResource
     */
    public function webhookReset($project_id, $id)
    {
        $model = $this->projects->findServer($project_id, $id);
        $this->authorize('deploy', $model->project);

        \DB::transaction(function() use ($model) {
            $model->resetWebhook(true);
        });

        return new ServerResource($model);
    }

    /**
     * Get the pubkey for the server
     *
     * @param  integer $id serverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function pubkey($id=0)
    {
        $project = $this->projects->findOrFail($id);
        $this->authorize('deploy', $project);

        return response()->json([
            'key' => $project->pubkey
        ]);
    }


}
