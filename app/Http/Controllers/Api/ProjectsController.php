<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\BaseRequest;
use App\Http\Resources\Management\ProjectResource;
use App\Jobs\RepositoryClone;
use App\Models\Project;

class ProjectsController extends APIController
{

    public function __construct(BaseRequest $request, Project $project)
    {
        parent::__construct($request, $project);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function index()
    {
        \DB::enableQueryLog();
        $query = $this->model->with([
            'latest_history' => function($query){
                $query->with(['server' => function($q){
                    $q->select(['name', 'id']);
                }]);
            },
            'servers' => function($query){
                $query->select(['id', 'name', 'project_id']);
            }
        ])->findUserModels()
          ->order([
            'order' => 'latest_history.created_at',
            'direction' => 1,
        ]);

        $projects = $query->get();
        return ProjectResource::collection($projects);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function store()
    {
        $rules = $this->model->getValidationRules();
        $data = $this->request->validate($rules);

        $model = $this->model->create($this->request->all());
        return new ProjectResource($model->load("servers", "configs", "scripts"));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Dingo\Api\Http\Response
     */
    public function show($id)
    {
        $model = $this->model->with([
            'servers',
            'configs' => function($query){
                $query->with(['servers' => function($query){
                    $query->select(['id', 'name']);
                }]);
            },
            'scripts'
        ])->findOrFail($id);

        $this->authorize($model);
        return new ProjectResource($model);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id) {
        $model = $this->model->getUserModel($id);
        $this->authorize($model);

        $rules = $model->getValidationRules($id);
        $data = $this->request->validate($rules);

        $model->update($this->request->all());

        return response()->json([
            "message" => "Successfully updated the project",
            "is_cloning" => $model->is_cloning,
            "status_code" => 200,
            "rules" => $rules
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Dingo\Api\Http\Response
     */
    public function destroy($id)
    {
        $model = $this->model->findOrFail($id);
        $this->authorize($model);

        abort_unless($model->delete(), 422, 'Could not delete the project');

        return response()->json([
            'message' => "Successfully deleted the project",
            "status_code" => 200
        ]);
    }

    /**
     * Get General info about the project
     *
     * @param  int $id
     * @return \Dingo\Api\Http\Response
     */
    public function info($id)
    {
        $model = $this->model->findOrFail($id);
        $this->authorize('view', $model);

        $exists = $model->repo_exists;
        $is_cloning = $model->is_cloning;

        $history = $model->history()->first();
        $last = $history ? $history->created_at->toIso8601String(): 'Never Deployed';
        $server = $history ? $history->server->name : "";

        return response()->json([
            'deployments' => [
                'last' => [
                    'date' => $last,
                    'server' => $server,
                ],
                'count' => $model->history()->count()
            ],
            'repo' => [
                'size' => $model->getRepoSizeAttribute(true),
                'exists' => $exists,
            ],
            'status' => [
                'is_cloning' => $is_cloning,
                'is_deploying' => $model->is_deploying,
                'clone_failed' => (!$is_cloning && !$exists)
            ]
        ]);
    }

    /**
     * Get the Public Key for deployed to servers.
     *
     * @param  int $id
     * @return \Dingo\Api\Http\Response
     */
    public function pubkey($id)
    {
        $model = $this->model->findOrFail($id);
        $this->authorize('view', $model);

        return response()->json([
            'key' => $model->pubkey
        ]);
    }

    /**
     * Trigger a RepositoryClone event
     *
     * @param  int $id
     * @return \Dingo\Api\Http\Response
     */
    public function cloneRepo($id)
    {
        $model = $this->model->getUserModel($id);
        $this->authorize('update', $model);

        if(!file_exists($model->repoPath())){
            $repo = $this->request->get('repo');

            if($repo && $repo !== $model->repo) {
                $model->update(compact('repo'));
            }

            $clone = (new RepositoryClone($model))->onQueue('clones');
            $this->dispatch($clone);
        } else {
            abort(400, 'The repository already exists. No need to reclone.');
        }

        return response()->json([
            'message'=>'Trying to reclone the repo.',
            'status_code'=>'200'
        ]);
    }

}
