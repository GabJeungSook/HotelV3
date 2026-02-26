<?php

namespace App\Console\Commands;

use App\Models\TemporaryCheckInKiosk;
use App\Models\Branch;
use Illuminate\Console\Command;

class CleanupTemporaryKiosk extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kiosk:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete temporary kiosk entries depending on kiosk timeout';

    /**
     * Execute the console command.
     *
     * @return int
     */
public function handle()
{
    $totalDeleted = 0;

    $branches = Branch::all();

    foreach ($branches as $branch) {

        $minutes = $branch->kiosk_time_limit ?? 10;

        $deleted = TemporaryCheckInKiosk::where('branch_id', $branch->id)
            ->where('created_at', '<=', now()->subMinutes($minutes))
            ->delete();

        $totalDeleted += $deleted;
    }

    $this->info("Deleted {$totalDeleted} expired kiosk entries.");
}
}
