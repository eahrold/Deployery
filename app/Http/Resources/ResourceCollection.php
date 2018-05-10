<?php

namespace App\Http\Resources;

use App\Http\Resources\Responses\PaginatedResourceResponse;
use Illuminate\Http\Resources\Json\ResourceCollection as ConcreteResourceCollection;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Str;

class ResourceCollection extends ConcreteResourceCollection
{

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource, $collects)
    {
        $this->collects = $collects;
        parent::__construct($resource);
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return $this->resource instanceof AbstractPaginator
                    ? (new PaginatedResourceResponse($this))->toResponse($request)
                    : parent::toResponse($request);
    }
}
