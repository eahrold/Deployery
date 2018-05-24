<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\BaseRequest;
use App\Models\Project;
use App\Transformers\HistoryTransformer;

class HistoryController extends APIController
{

    /**
     * Project
     * @var \App\Models\Project
     */
    private $project;

    public function __construct(BaseRequest $request, Project $project, HistoryTransformer $transformer)
    {
        $this->project = $project;
        parent::__construct($request, $project->history()->getModel(), $transformer);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function index($project_id)
    {
        $project = $this->project->findOrFail($project_id);
        $this->authorize('listChildren', $project);

        $limit = $this->request->get('limit');
        return $this->response->paginator($project->history()->paginate($limit), $this->transformer);
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
        $project = $this->project->findOrFail($project_id);
        $this->authorize($project);

        $history = $project->history()->findOrFail($id);

        $this->transformer->makeVisible(['details']);
        return $this->response->item($history, $this->transformer);
    }
}
