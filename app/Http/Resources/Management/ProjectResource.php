<?php

namespace App\Http\Resources\Management;

use App\Http\Resources\Resource;

final class ProjectResource extends Resource
{

    protected $whenLoadedIncludes = [ 'configs', 'servers', 'scripts' ];

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $array =  parent::toArray($request);

        return $array;
    }

    public function includeLatestHistory()
    {
        return new HistoryResource($this->latest_history);
    }

    public function includeConfigs()
    {
        return ConfigResource::collection($this->configs);
    }

    public function includeServers()
    {
        return ServerResource::collection($this->servers);
    }

    public function includeScripts()
    {
        return ScriptResource::collection($this->scripts);
    }
}
