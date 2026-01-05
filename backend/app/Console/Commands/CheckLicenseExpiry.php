<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;

class CheckLicenseExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'license:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'license:check';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Company::whereDate('expiry_date', '<', now())
            ->where('status', '!=', 'expired')
            ->update(['status' => 'expired']);
    }
}
