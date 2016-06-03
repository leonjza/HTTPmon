<?php
/*
This file is part of HTTPmon

Copyright (C) 2016  Leon Jacobs

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software Foundation,
Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
*/

namespace App\Listeners;

use App\Certificate;
use App\Events\UrlMustUpdateEvent;
use App\Httpmon\Ssllabs\Client;
use App\Url;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Class UpdateSslLabsListener
 * @package App\Listeners
 */
class UpdateSslLabsListener implements ShouldQueue
{

    /**
     * @var \App\Httpmon\Ssllabs\Client
     */
    private $labs_client;

    /**
     * @var string
     */
    private $ssllabs_ready_status = 'READY';

    /**
     * @param \App\Httpmon\Ssllabs\Client $labs_client
     */
    public function __construct(Client $labs_client)
    {

        $this->labs_client = $labs_client;
    }

    /**
     * @param \App\Events\UrlMustUpdateEvent $event
     */
    public function handle(UrlMustUpdateEvent $event)
    {

        if (!in_array('ssl', $event->url->features))
            return;
       
        Log::info($event->url->url . ' Processing SSL Labs Update Job');

        // Ensure that we have not reached the max simultaneous scans
        while (!$this->canQueue())
            sleep(10);

        // Log the new scan and query its status.
        $this->labs_client->newAnalysis($event->url->url);
        Log::info($event->url->url . ' New analysis job queued');

        $report = $this->labs_client->analysisStatus($event->url->url);

        // Wait for the report to complete by polling the SSL Labs
        // API and inspecting the response status.
        while ($report->status !== $this->ssllabs_ready_status) {

            sleep(20);
            $report = $this->labs_client->analysisStatus($event->url->url);

            Log::info($event->url->url . ' Report status is ' . $report->status);
        }

        $this->updateRating($event->url, $report->endpoints[0]->grade);

        return;
    }

    /**
     * @return bool
     */
    public function canQueue()
    {

        $info = $this->labs_client->info();

        Log::info('SSL Labs reports current assessment count as '
            . $info->currentAssessments . '. Max is ' . $info->maxAssessments);

        if ($info->currentAssessments < $info->maxAssessments)
            return true;

        return false;
    }

    /**
     * @param \App\Url $url
     * @param          $rating
     */
    public function updateRating(Url $url, $rating)
    {

        // Reload the ssl relation. This is to prevent
        // a case where there may already be Cert info
        // but the relation is not up to date yet.
        $url = $url->load('ssl');

        $ssl = $url->ssl instanceof Certificate ? $url->ssl : new Certificate;

        $ssl->ssl_labs_rating = $rating;
        $ssl->ssl_labs_last_update = Carbon::now();
        $url->ssl()->save($ssl);

        return;
    }

}