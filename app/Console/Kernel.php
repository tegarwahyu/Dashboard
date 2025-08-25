<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Daftar custom Artisan commands.
     *
     * Kalau pakai Laravel 8+ atau 12, command biasanya autodiscover.
     * Kalau tidak terdeteksi, daftarkan manual di sini.
     */
    protected $commands = [
        \App\Console\Commands\UpdatePromosiStatus::class,
    ];

    /**
     * Define jadwal command Artisan.
     */
    protected function schedule(Schedule $schedule)
    {
        // Jalan setiap menit: update status promosi sesuai jadwal
        $schedule->command('promosi:update-status')->everyMinute();

        // Contoh jadwal lain jika mau: daily, hourly, dll
        // $schedule->command('backup:run')->daily();
    }

    /**
     * Daftarkan file commands Artisan di console.php.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        // Jika punya route console tambahan
        require base_path('routes/console.php');
    }
}