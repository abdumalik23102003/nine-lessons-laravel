<?php

use App\Console\Commands\ExpireAdvertsCommand;
use App\Console\Commands\ExpireBannersCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule expire commands to run daily at 2 AM
Schedule::command(ExpireAdvertsCommand::class)->dailyAt('02:00');
Schedule::command(ExpireBannersCommand::class)->dailyAt('02:00');
