<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule monthly Telegram report
Schedule::command('telegram:monthly-report')
    ->monthlyOn(1, '09:00')
    ->timezone('Asia/Phnom_Penh')
    ->description('Send monthly survey report to Telegram');
