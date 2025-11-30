<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\CollectLinkMetrics;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

//
// 📅 SCHEDULED TASKS
//

// Collect link metrics every 5 minutes
Schedule::job(new CollectLinkMetrics())
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground();

// Calculate monthly SLA reports (1st day of each month at 00:30)
Schedule::command('sla:calculate-monthly')
    ->monthlyOn(1, '00:30')
    ->timezone('Asia/Kolkata');
