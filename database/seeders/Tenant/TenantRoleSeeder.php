<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\TenantRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantRoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        TenantRole::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $roles = [
            ['name' => 'tenant admin'],
            ['name' => 'user'],
        ];

        foreach ($roles as $role) {
            TenantRole::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
