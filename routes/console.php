<?php

use App\Jobs\SyncCommentsFromVideos;
use App\Jobs\SyncVideosFromChannel;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
  $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new SyncVideosFromChannel)
  ->daily()
  ->name('sync-videos')
  ->onOneServer()
  ->withoutOverlapping();

Schedule::job(new SyncCommentsFromVideos)
  ->everyThirtyMinutes()
  ->name('sync-comments')
  ->onOneServer()
  ->withoutOverlapping();
