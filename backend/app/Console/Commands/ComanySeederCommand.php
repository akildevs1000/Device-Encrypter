<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;

class ComanySeederCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:company-seeder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Company::create([
            "name" => "francis",
            "number" => "0554501483",
            "email" => "company@gmail.com",
            "location" => "Dubai",
        ]);

        return 0;
    }
}
