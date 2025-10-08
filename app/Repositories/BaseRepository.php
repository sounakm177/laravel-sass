<?php

namespace App\Repositories;

use App\Models\BaseModel;
use App\Models\Tenant;
use App\Models\Tenant\TenantUser;
use App\Models\User;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected array $fillable = [];

    protected BaseModel|User|Tenant|TenantUser $model;

    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function get(): BaseModel|User|Tenant|TenantUser
    {
        return $this->model;
    }

    public function create(mixed $params = null, array $relation = []): self
    {
        $params = $params ?? $this->request->only($this->fillable);
        $this->model = $this->model->create($params);
        $this->model = $this->model->with($relation)->findOrFail($this->model->id);

        return $this;
    }

    public function bulkCreate(array $data): bool
    {
        try {
            $this->model->insert($data);

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function bulkUpsert(array $data, array $unique_keys): bool
    {
        try {
            $this->model->upsert($data, $unique_keys);

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function update(int $id, mixed $params = null): self
    {
        $this->model = $this->model->findOrFail($id);
        $params = $params ?? $this->request->only($this->fillable);
        $this->model->update($params);

        return $this;
    }

    public function updateOrCreate(array $attributes, array $values = [], array $relation = []): self
    {
        $this->model = $this->model->updateOrCreate($attributes, $values);
        $this->model = $this->model->with($relation)->findOrFail($this->model->id);

        return $this;
    }

    public function delete(int|string|array $id): bool
    {
        if (is_array($id)) {
            return $this->model->destroy($id) > 0;
        }

        return $this->model->destroy((int) $id) > 0;
    }

    public function getById(int $id, array $relation = [], array $select = []): BaseModel|User|TenantUser|Tenant
    {
        return count($relation) > 0 ? (count($select) > 0 ? $this->model->with($relation)->select($select)->findOrFail($id) : $this->model->with($relation)->findOrFail($id)) : (count($select) > 0 ? $this->model->select($select)->findOrFail($id) : $this->model->findOrFail($id));
    }

    public function getByOne(array $relation = [], array $params = [], array $select = []): BaseModel|User|TenantUser|null
    {
        return count($relation) > 0 ? (count($select) > 0 ? $this->model->with($relation)->where($params)->select($select)->first() : $this->model->with($relation)->where($params)->first()) : (count($select) > 0 ? $this->model->where($params)->select($select)->first() : $this->model->where($params)->first());
    }

    public function getByOneWithTrash(array $params = []): BaseModel|User|null
    {
        return $this->model->withTrashed()->where($params)->first();
    }

    public function getQueryForAll($conditions)
    {
        $params = $this->fillable;
        $exclude = ['is_active', 'is_default', 'is_primary', 'seen'];
        $filteredParams = array_merge(array_diff($params, $exclude), ['id', 'updated_at', 'created_at']);
        $where = [];
        foreach ($filteredParams as $filterBy) {
            $this->request->$filterBy ? $where[$filterBy] = $this->request->$filterBy : '';
        }
        foreach ($exclude as $filterBy) {
            if (isset($this->request->$filterBy)) {
                $where[$filterBy] = $this->request->$filterBy;
            }
        }
        $query = $this->model->newQuery();
        foreach ($conditions as $condition) {
            if (count($condition) === 3) {
                [$column, $operator, $value] = $condition;
                if ($operator === 'like') {
                    $query->whereLike($column, $value);
                } elseif ($operator === 'or') {
                    $query->orWhere($column, $value);
                } elseif ($operator === 'orlike') {
                    $query->orWhere($column, 'like', '%'.$value.'%');
                } elseif ($operator === 'In') {
                    $query->whereIn($column, $value);
                } elseif ($operator === 'NotIn') {
                    $query->whereNotIn($column, $value);
                } elseif ($operator === 'orIn') {
                    $query->orWhereIn($column, $value);
                } elseif ($operator === 'or>') {
                    $query->orWhere($column, '>', $value);
                } elseif ($operator === 'or>=') {
                    $query->orWhere($column, '>=', $value);
                } elseif ($operator === 'or<') {
                    $query->orWhere($column, '<', $value);
                } elseif ($operator === 'or>=') {
                    $query->orWhere($column, '>=', $value);
                } elseif ($operator === 'Has') {
                    $query->whereHas($column, function ($q) use ($value) {
                        $q->where($value[0], $value[1]);
                    });
                } elseif ($operator === 'Date') {
                    $query->whereDate($column, $value);
                } else {
                    $query->where($column, $operator, $value);
                }
            } elseif (count($condition) === 2) {
                [$column, $value] = $condition;
                $query->where($column, $value);
            }
        }

        $query = $query->where($where);

        return $query;
    }

    public function all(array $relation = [], array $conditions = [], array $select = [], array $order = []): Collection
    {
        $query = $this->getQueryForAll($conditions);
        $count = $query->count();
        $skip = $this->request->page ?? 0;
        $take = $this->request->limit ?? ($count - $skip);
        $order_by = $this->request->order_by != null ? $this->request->order_by : ($order['order_by'] ?? 'id');
        $order_type = $this->request->order_type != null ? $this->request->order_type : ($order['order_type'] ?? 'DESC');

        count($select) > 0 ? $query = $query->select($select) : '';
        $response = count($relation) > 0 ? $query->orderBy($order_by, $order_type)->skip($skip)->take($take)->with($relation)->get() : $query->orderBy($order_by, $order_type)->skip($skip)->take($take)->get();

        return $response;
    }

    public function allWithPaginate(array $relation = [], array $conditions = [], array $select = [], array $order = []): LengthAwarePaginator
    {
        $query = $this->getQueryForAll($conditions);

        $order_by = $this->request->order_by != null ? $this->request->order_by : ($order['order_by'] ?? 'id');
        $order_type = $this->request->order_type != null ? $this->request->order_type : ($order['order_type'] ?? 'DESC');

        $pageSize = $this->request->page_size ?? 10;
        $page = $this->request->input('page', 1);

        count($select) > 0 ? $query = $query->select($select) : '';
        $query = count($relation) > 0 ? $query->orderBy($order_by, $order_type)->with($relation) : $query->orderBy($order_by, $order_type);

        return $query->paginate($pageSize, ['*'], 'page', $page);
    }

    public function getClass()
    {
        return $this->model->class;
    }
}
