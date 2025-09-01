<?php

namespace App\Console\Commands;
use Carbon\Carbon;
use App\Models\Daily_usages;
use Illuminate\Console\Command;

class clean_daily_usages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean_daily_usages';

    /**
     * The console command description.
     *
     * @var string
     */
     protected $description = 'Delete old daily usage data, keeping only current month records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $_start_of_month = $now->startOfMonth()->toDateString();
        Daily_usages::where('date', '<', $_start_of_month)->delete();
    }
}
