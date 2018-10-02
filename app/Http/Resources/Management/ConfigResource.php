<?php

namespace App\Http\Resources\Management;

use App\Http\Resources\Resource;

final class ConfigResource extends Resource
{

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
}
