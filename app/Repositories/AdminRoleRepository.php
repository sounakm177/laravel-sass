<?php

namespace App\Repositories;

use App\Models\Role;
use App\Repositories\Interfaces\AdminRoleRepositoryInterface;
use Illuminate\Http\Request;

final class AdminRoleRepository extends BaseRepository implements AdminRoleRepositoryInterface
{
    public function __construct(Request $request, Role $model)
    {
        parent::__construct($request);
        $this->model = $model;
    }
}
