<?php

namespace App\Repositories;

use App\Models\Tenant;
use App\Repositories\Interfaces\TenantRepositoryInterface;
use Illuminate\Http\Request;

final class TenantRepository extends BaseRepository implements TenantRepositoryInterface
{
    public function __construct(Request $request, Tenant $model)
    {
        parent::__construct($request);
        $this->model = $model;
    }

    /**
     * Get all tenants with optional relationships and ordering.
     *
     * @param  array  $relation  Relationships to eager load
     * @param  array  $orderBy  [column => direction]
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(array $relation = [], array $orderBy = ['id' => 'desc'])
    {
        $query = $this->model->with($relation);

        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query->get();
    }

    public function totalCount(): int
    {
        return $this->model->count();
    }
}
