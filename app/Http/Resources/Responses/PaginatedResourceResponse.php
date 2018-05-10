<?php

namespace App\Http\Resources\Responses;

use Illuminate\Http\Resources\Json\PaginatedResourceResponse as ConcretePaginatedResourceResponse;
use Illuminate\Support\Arr;

class PaginatedResourceResponse extends ConcretePaginatedResourceResponse
{
    /**
     * Add the pagination information to the response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function paginationInformation($request)
    {
        $paginated = $this->resource->resource->toArray();
        $pagination = $this->meta($paginated);

        return [
            'meta' => [
                'pagination' => $pagination
            ],
        ];
    }

    /**
     * Get the pagination links for the response.
     *
     * @param  array  $paginated
     * @return array
     */
    protected function paginationLinks($paginated)
    {
        return [
            'first' => $paginated['first_page_url'] ?? null,
            'last' => $paginated['last_page_url'] ?? null,
            'prev' => $paginated['prev_page_url'] ?? null,
            'next' => $paginated['next_page_url'] ?? null,
        ];
    }

    /**
     * Gather the meta data for the response.
     *
     * @param  array  $paginated
     * @return array
     */
    protected function meta($paginated)
    {
        return array_merge(Arr::except($paginated, [
            'data',
            'first_page_url',
            'last_page_url',
            'prev_page_url',
            'next_page_url',
        ]),[
            'count' => count($paginated['data']),
            'per_page' => (integer)$paginated['per_page'],
            'total_pages' => ceil($paginated['total'] / $paginated['per_page']),
            'links' => $this->paginationLinks($paginated)
        ]);
    }
}
