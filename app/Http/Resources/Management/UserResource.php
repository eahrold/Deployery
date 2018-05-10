<?php

namespace App\Http\Resources\Management;

use App\Http\Resources\Resource;

final class UserResource extends Resource
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

        if ($this->user()->can('manage', $this->user())) {
            $data['is_admin'] = $this->resource->is_admin;
            $data['can_manage_teams'] = $this->resource->can_manage_teams;
            $data['can_join_teams'] = $this->resource->can_join_teams;
        }

        return $array;
    }
}
