<?php

namespace App\Http\Resources\Management;

use App\Http\Resources\Resource;

final class HistoryResource extends Resource
{

    protected $whenLoadedIncludes = [ 'server' ];

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

    public function includeServer()
    {
        return new ServerResource($this->server);
    }
}
