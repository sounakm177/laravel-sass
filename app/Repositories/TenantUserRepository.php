<?php

namespace App\Repositories;

use App\Models\Tenant\TenantUser;
use App\Repositories\Interfaces\TenantUserRepositoryInterface;
use Illuminate\Http\Request;

final class TenantUserRepository extends BaseRepository implements TenantUserRepositoryInterface
{
    public function __construct(Request $request, TenantUser $model)
    {
        parent::__construct($request);
        $this->model = $model;
    }
}
