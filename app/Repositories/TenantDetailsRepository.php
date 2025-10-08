<?php

namespace App\Repositories;

use App\Models\TenantDetail;
use App\Repositories\Interfaces\TenantDetailsRepositoryInterface;
use Illuminate\Http\Request;

final class TenantDetailsRepository extends BaseRepository implements TenantDetailsRepositoryInterface
{
    public function __construct(Request $request, TenantDetail $model)
    {
        parent::__construct($request);
        $this->model = $model;
    }
}
