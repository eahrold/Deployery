<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\BaseRequest;
use App\Http\Resources\Management\HistoryResource;
use App\Models\Project;

class HistoryController extends APIController
{

    /**
     * Project
     * @var \App\Models\Project
     */
    private $project;

    public function __construct(BaseRequest $request, Project $project)
    {
        $this->project = $project;
        parent::__construct($request, $project->history()->getModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return HistoryResource
     */
    public function index($project_id)
    {
        $project = $this->project->findOrFail($project_id);
        $this->authorize('listChildren', $project);

        $limit = $this->request->get('limit');
        return HistoryResource::collection($project->history()->with([
            "server"
        ])->paginate($limit));
    }

    /**
     * Display the specified resource.
     *
     * @param  integer  $project_id
     * @param  integer  $id
     *
     * @return HistoryResource
     */
    public function show($project_id, $id)
    {
        $project = $this->project->findOrFail($project_id);
        $this->authorize($project);

        $history = $project->history()->findOrFail($id);
        $history->makeVisible(["details"]);
        return new HistoryResource($history);
    }
}
