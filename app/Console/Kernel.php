<?php

namespace App\Console;

use App\Console\Commands\Httpmon\ResetAdmin;
use App\Events\UrlMustUpdateEvent;
use App\Url;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ResetAdmin::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->call(function () {

            Log::info('Starting scheduled updates');

            foreach (Url::all() as $url)
                Event::fire(new UrlMustUpdateEvent($url));

        })->twiceDaily(1, 13);
    }
}
