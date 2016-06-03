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

namespace App\Httpmon\Ssllabs;

use App\Httpmon\Ssllabs\Exceptions\InvalidEndpointException;
use GuzzleHttp\Client as GuzzleClient;

/**
 * Class Client
 * @package App\Httpmon\Ssllabs
 */
class Client
{

    /**
     * @var string
     */
    protected $base_url = 'https://api.ssllabs.com/api/v2/';

    /**
     * @var
     */
    private $client;

    /**
     * @var array
     */
    private $endpoints = ['info', 'analyze'];

    /**
     * @param \GuzzleHttp\Client $client
     */
    public function __construct(GuzzleClient $client)
    {

        $this->client = $client;
    }

    /**
     * @param \App\Httpmon\Ssllabs\string $endpoint
     * @param array                       $parameters
     *
     * @return mixed
     * @throws \App\Httpmon\Ssllabs\Exceptions\InvalidEndpointException
     */
    private function call($endpoint, array $parameters = [])
    {

        if (!in_array($endpoint, $this->endpoints))
            throw new InvalidEndpointException($endpoint . ' is not a valid endpoint.');

        $result = $this->client
            ->request('get', $this->base_url . '/' . $endpoint, [
                'headers' => [
                    'User-Agent' => 'httpmon/' . config('httpmon.version'),
                    'Accept'     => 'application/json',
                ],
                'query'   => $parameters,
            ]);

        return json_decode($result->getBody()->getContents());

    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function info()
    {

        return $this->call('info');
    }

    /**
     * @param        $host
     * @param string $publish
     * @param string $startNew
     * @param string $all
     *
     * @return mixed
     * @throws \Exception
     */
    private function analyze($host, $publish = 'off', $startNew = 'on', $all = 'done')
    {

        return $this->call('analyze', [
            'host'     => $host,
            'publish'  => $publish,
            'startNew' => $startNew,
            'all'      => $all,
        ]);
    }

    /**
     * @param $host
     *
     * @return mixed
     */
    public function newAnalysis($host)
    {

        return $this->analyze($host);
    }

    /**
     * @param $host
     *
     * @return mixed
     */
    public function analysisStatus($host)
    {

        return $this->analyze($host, 'off', 'off');
    }

}
