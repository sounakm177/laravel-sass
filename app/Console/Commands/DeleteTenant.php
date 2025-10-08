<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteTenant extends Command
{
    protected $signature = 'tenant:delete {tenant_ids*} {--force : Force delete without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Delete one or more tenants, their databases, domains, and all related data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantIds = $this->argument('tenant_ids');
        $force = $this->option('force');

        foreach ($tenantIds as $tenantId) {
            $tenant = Tenant::find($tenantId);

            if (! $tenant) {
                $this->error("Tenant with ID '{$tenantId}' not found.");

                continue;
            }

            if (! $force && ! $this->confirm("Are you sure you want to delete tenant '{$tenantId}' and everything related?", false)) {
                $this->info("Skipping tenant '{$tenantId}'.");

                continue;
            }

            DB::beginTransaction();

            try {
                // $this->info("Backing up tenant database for ID: {$tenantId}...");
                // $this->backupTenantDatabase($tenant);

                $this->info('Deleting tenant domains...');
                $tenant->domains()->delete();

                $this->info('Deleting tenant files...');
                Storage::disk('local')->deleteDirectory("tenants/{$tenant->id}");

                $this->info('Removing tenant from central database...');
                $tenant->delete();

                $this->info('Dropping tenant database...');
                DB::statement("DROP DATABASE IF EXISTS `$tenant->tenancy_db_name`");

                DB::commit();
                $this->info("✅ Tenant '{$tenantId}' deleted successfully.");

            } catch (\Throwable $e) {
                DB::rollBack();
                $this->error("❌ Error while deleting tenant '{$tenantId}': ".$e->getMessage());
            }
        }

        return Command::SUCCESS;
    }

    protected function backupTenantDatabase(Tenant $tenant): void
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $backupDir = storage_path('backups');
        $backupPath = "{$backupDir}/{$tenant->id}_{$timestamp}.sql";

        if (! is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $databaseName = $tenant->database()->getName();
        $dbHost = config('database.connections.mysql.host');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        $command = "mysqldump -h {$dbHost} -u {$dbUser} --password=\"{$dbPass}\" {$databaseName} > {$backupPath}";
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('Backup failed using mysqldump. Output: '.implode("\n", $output));
        }

        $this->info("Backup saved at: {$backupPath}");
    }
}
