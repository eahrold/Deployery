<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\BaseRequest;
use App\Models\Project;
use App\Transformers\HistoryTransformer;

class HistoryController extends APIController
{

    private $project;

    public function __construct(BaseRequest $request, Project $project, HistoryTransformer $transformer)
    {
        $this->project = $project;
        parent::__construct($request, $project->history()->getModel(), $transformer);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($project_id)
    {
        $project = $this->project->findOrFail($project_id);
        $this->authorize('listChildren', $project);

        return $this->response->collection($project->history, $this->transformer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($project_id, $id)
    {
        $project = $this->project->findOrFail($project_id);
        $this->authorize($project);

        $history = $project->history()->findOrFail($id);

        $this->transformer->makeVisible(['details']);
        return $this->response->item($history, $this->transformer);
    }
}
