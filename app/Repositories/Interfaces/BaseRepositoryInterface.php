<?php

namespace App\Repositories\Interfaces;

use App\Models\BaseModel;
use App\Models\Tenant;
use App\Models\Tenant\TenantUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    public function get(): BaseModel|User|Tenant|TenantUser;

    public function create(mixed $params = null, array $relation = []): self;

    public function update(int $id, mixed $params = null): self;

    public function updateOrCreate(array $attributes, array $values = [], array $relation = []): self;

    public function delete(int $id): bool;

    public function getById(int $id, array $relation = [], array $select = []): BaseModel|User|Tenant|TenantUser;

    public function getByOne(array $relation = [], array $params = [], array $select = []): BaseModel|User|Tenant|TenantUser|null;

    public function getByOneWithTrash(array $params = []): BaseModel|User|Tenant|TenantUser|null;

    public function all(array $relation = [], array $conditions = [], array $select = [], array $order = []): Collection;

    public function allWithPaginate(array $relation = [], array $conditions = [], array $select = [], array $order = []): LengthAwarePaginator;

    public function bulkCreate(array $data): bool;

    public function bulkUpsert(array $data, array $unique_keys): bool;

    public function getClass();
}
