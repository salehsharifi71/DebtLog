<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ارسال خودکار اعلان‌های سررسید هر روز ساعت 9 صبح
Schedule::command('app:send-due-notifications')
    ->dailyAt('09:00')
    ->timezone('Asia/Tehran')
    ->description('ارسال اعلان تلگرام برای اقساط سررسید فردا');
