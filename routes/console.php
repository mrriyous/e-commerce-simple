<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('products:check-low-stock')
    ->everyThirtyMinutes()
    ->withoutOverlapping();

Schedule::command('sales:generate-daily-report')
    ->dailyAt('23:00')
    ->withoutOverlapping();

