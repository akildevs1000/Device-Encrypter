<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;

class CheckLicenseExpiry extends Command
{
    protected $signature = 'license:check';
    protected $description = 'Update company license status based on expiry date';

    public function handle()
    {
        $now = now()->startOfDay();

        // Expire old licenses
        $expiredCount = Company::whereDate('expiry_date', '<', $now)
            ->update(['status' => 'expired']);

        // Activate valid licenses
        $activeCount = Company::whereDate('expiry_date', '>=', $now)
            ->update(['status' => 'active']);

        $this->info("License check completed.");
        $this->info("Expired updated: {$expiredCount}");
        $this->info("Active updated: {$activeCount}");

        return Command::SUCCESS;
    }
}
