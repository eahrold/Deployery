<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\BaseRequest;
use App\Jobs\RepositoryClone;
use App\Models\Project;
use App\Transformers\ProjectTransformer;

class ProjectsController extends APIController
{
    public function __construct(BaseRequest $request, Project $project, ProjectTransformer $transformer)
    {
        parent::__construct($request, $project, $transformer);
    }

    public function cloneRepo($id)
    {
        $project = $this->model->getUserModel($id);
        if(!file_exists($project->repoPath())){
            $clone = (new RepositoryClone($project))->onQueue('clones');
            $this->dispatch($clone);
        } else {
            abort(400, 'The repository already exists. No need to reclone.');
        }

        return $this->response->array([
            'message'=>'Trying to reclone the repo.',
            'status_code'=>'200'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
