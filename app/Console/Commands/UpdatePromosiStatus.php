<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\models\Promosi;

class UpdatePromosiStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:update-promosi-status';
    protected $signature = 'promosi:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'Command description';
    protected $description = 'Update status promosi sesuai jadwal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();

        $scheduledToActive = Promosi::where('schedule_status', 'Scheduled')
            ->where('mulai_promosi', '<', $now)
            ->update(['schedule_status' => 'Active']);

        $activeToExpired = Promosi::where('schedule_status', 'Active')
            ->where('akhir_promosi', '<', $now)
            ->update(['schedule_status' => 'Expired']);

        $activeToScheduled = Promosi::where('schedule_status', 'Active')
        ->where('mulai_promosi', '>', $now)
        ->update(['schedule_status' => 'Scheduled']);
        
        $this->info("Updated: $scheduledToActive Scheduled->Active, $activeToExpired Active->Expired, $activeToScheduled Active->Scheduled");
    }
}
