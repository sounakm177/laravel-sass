<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

final class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected array $fillable = ['firstname', 'lastname', 'email', 'is_active', 'password', 'email_verified_at', 'provider', 'provider_id', 'last_login_at', 'created_by', 'updated_by'];

    public function __construct(Request $request, User $model)
    {
        parent::__construct($request);
        $this->model = $model;
    }

    public function totalCount(): int
    {
        return $this->model->count();
    }

    public function fetchCfoUsers()
    {
        return $this->model->whereHas('roles', function ($q) {
            $q->where('name', 'CFO');
        })->select('id', 'firstname', 'lastname', 'email')->get();
    }

    public function updateWhere(array $conditions, array $data): int
    {
        return $this->model->where($conditions)->update($data);
    }

    public function getAllDefaultCFOUsers()
    {
        return $this->model->where('is_default_cfo', 1)->get() ?? new Collection;
    }

    public function getAssignedCFOUser($companyId)
    {
        //
    }
}
