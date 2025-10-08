<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TenantRequest;
use App\Services\TenantService;

class TenantController extends Controller
{
    public function __construct(
        protected TenantService $tenantService,
    ) {}

    public function store(TenantRequest $request)
    {
        try {
            $data = $request->all();
            $tenantFolder = 'tenants_logo/'.str_replace(' ', '_', $request->company_name);

            $imagePath = $request->hasFile('logo')
                ? $request->file('logo')->store($tenantFolder, 'public')
                : null;

            $data['logo'] = $imagePath;

            $result = $this->tenantService->createTenant($data);

            return response()->json([
                'status' => 'success',
                'access_token' => $result['token'],
                'token_type' => 'Bearer',
                'user' => $result['tenantUser'],
                'tenant' => [
                    'id' => $result['tenant']->id,
                    'domain' => $result['domain']['domain'],
                    'name' => $result['tenant']->name,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
