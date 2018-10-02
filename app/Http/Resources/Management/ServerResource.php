<?php

namespace App\Http\Resources\Management;

use App\Http\Resources\Resource;

final class ServerResource extends Resource
{

    protected $whenLoadedIncludes = [ 'configs' ];

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $array =  parent::toArray($request);

        if ($request->user()->can('deploy', $this->project)) {
            $array["webhook"] = $this->webhook;
        }

        return $array;
    }

    public function includeConfigs()
    {
        logger("Checking Config", [$this->whenLoaded('configs')]);
        return ConfigResource::collection($this->whenLoaded('configs'));
    }
}
