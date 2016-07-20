<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseRequest;
use App\Jobs\ServerDeploy;
use App\Models\Server;

class ServersController extends Controller
{

    public function __construct(BaseRequest $request, Server $model){
        parent::__construct($request, $model);
    }

    /**
     * Trigger Deployment from frontend
     *
     * @return JSON
     */
    public function deploy($project_id, $id){
        $server = $this->model->findOrFail($id);
        return $this->ququeDeployment($server);
    }

    /**
     * Trigger Deployment from frontend
     *
     * @return JSON
     */
    public function webhook($webhook){
        $server = $this->model
                       ->where('webhook', $this->request->url())
                       ->firstOrFail();
        return $this->ququeDeployment($server);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($project_id){
        return view('pages.server_form', [
            'model' => $this->model,
            'project' => $project_id
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($project_id){
        $project = $this->model
                        ->project()
                        ->getModel()
                        ->findOrFail($project_id);
        $model = new $this->model($this->request->all());

        $project->servers()->save($model);

        if($this->request->get('exit')){
            return redirect()->route('projects.edit', $project_id);
        }
        return redirect()->route('projects.{projects}.servers.edit', [$project_id, $model->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($project_id, $id){
        $model = $this->model->with('servers', 'history')->find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($project_id, $id){
        $model = $this->model->findOrFail($id);
        return view('pages.server_form', [
            'model' => $model,
            'project' => $project_id
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($project_id, $id){
        $model = $this->model->findOrFail($id);

        if($model->update($this->request->all()) && $this->request->get('exit')){
            return redirect()->route('projects.edit', $project_id);
        }
        return redirect()->route('projects.{projects}.servers.edit', [$project_id, $model->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($project_id, $id){
        $model = $this->model->findOrFail($id);
        $project_id = $model->project->id;
        if($model->delete()){
            return redirect()->route('projects.edit', $project_id);
        }
        return redirect()->route('projects.{projects}.servers.edit', [$project_id, $model->id]);
    }

    //----------------------------------------------------------
    // Private
    //-------------------------------------------------------
    private function ququeDeployment(Server $server){
        $deployment = new ServerDeploy($server);
        $this->dispatch($deployment);
        return [
            'message'=>'started deployment',
            'socket_id'=>$deployment->socket()
        ];
    }
}
