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

use App\Body;
use App\Certificate;
use App\Cookie;
use App\Events\SomeEvent;
use App\Events\UrlMustUpdateEvent;
use App\Header;
use App\Meta;
use App\Url;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;
use League\Uri\Schemes\Http;
use Psr\Http\Message\ResponseInterface;

/**
 * Class UpdateUrlListener
 * @package App\Listeners
 */
class UpdateUrlListener
{

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var \GuzzleHttp\Cookie\CookieJar
     */
    protected $jar;

    /**
     * @param \GuzzleHttp\Client           $client
     * @param \GuzzleHttp\Cookie\CookieJar $jar
     */
    public function __construct(Client $client, CookieJar $jar)
    {

        $this->client = $client;
        $this->jar = $jar;
    }

    /**
     * @param \App\Events\UrlMustUpdateEvent $event
     *
     * @return bool
     */
    public function handle(UrlMustUpdateEvent $event)
    {

        Log::info($event->url->url . ' Starting Url update based on features');

        $response = $this->updateUrl($event->url);

        // Check the features and process as needed
        if (in_array('cookies', $event->url->features))
            $this->updateCookies($event->url);

        if (in_array('headers', $event->url->features))
            $this->updateHeaders($event->url, $response);

        if (in_array('body', $event->url->features))
            $this->updateBody($event->url, $response);

        if (in_array('ssl', $event->url->features))
            $this->updateSsl($event->url, $response);

        return;
    }

    /**
     * @param \App\Url $url
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function updateUrl(Url $url)
    {

        try {

            $result = $this->client
                ->request('get', $url->url, [
                    'headers'         => [
                        'User-Agent' => 'httpmon/' . config('httpmon.version'),
                    ],
                    'cookies'         => $this->jar,
                    'allow_redirects' => false,
                    'verify'          => false, // Bad? Sure.
                ]);

        } catch (ClientException $e) {

            Log::warning($url->url . ' Guzzle ClientException caught during update. ' .
                $e->getCode() . ':' . $e->getMessage());

            $result = $e->getResponse();
        }

        $result->getProtocolVersion();
        $result->getReasonPhrase();

        // Update the meta information from the request
        $meta = $url->meta instanceof Meta ? $url->meta : new Meta;

        $meta->status_code = $result->getStatusCode();
        $meta->server = $result->getHeaderLine('server');
        $meta->protocol_version = $result->getProtocolVersion();
        $meta->reason_phrase = $result->getReasonPhrase();
        $url->meta()->save($meta);

        $url->last_check = Carbon::now();
        $url->save();

        return $result;
    }

    /**
     * @param \App\Url $url
     */
    public function updateCookies(Url $url)
    {

        Log::info($url->url . ' Updating Cookies');

        foreach ($this->jar->toArray() as $cookie) {

            if ($url->cookies()->where('name', $cookie['Name'])->count())
                continue;

            $new_cookie = new Cookie;
            $new_cookie->name = $cookie['Name'];
            $new_cookie->value = $cookie['Value'];
            $new_cookie->domain = $cookie['Domain'];
            $new_cookie->path = $cookie['Path'];
            $new_cookie->max_age = $cookie['Max-Age'];
            $new_cookie->expires = $cookie['Expires'];
            $new_cookie->secure = $cookie['Secure'];
            $new_cookie->discard = $cookie['Discard'];
            $new_cookie->httponly = $cookie['HttpOnly'];

            $url->cookies()->save($new_cookie);
        }

        return;
    }

    /**
     * @param \App\Url                            $url
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function updateHeaders(Url $url, ResponseInterface $response)
    {

        Log::info($url->url . ' Updating Headers');

        foreach ($response->getHeaders() as $name => $values) {

            if ($url->headers()->where('name', $name)->count())
                continue;

            $new_header = new Header;
            $new_header->name = $name;
            $new_header->values = $values;

            $url->headers()->save($new_header);

        }

        return;
    }

    /**
     * @param \App\Url                            $url
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function updateBody(Url $url, ResponseInterface $response)
    {

        Log::info($url->url . ' Updating Body');

        $source = preg_replace('/\s+/', ' ', $response->getBody()->getContents());
        $hash = hash('sha256', $source);

        $body = $url->body instanceof Body ? $url->body : new Body;

        $body->data = $source;
        $body->sha256 = $hash;

        $url->body()->save($body);

        return;
    }

    /**
     * @param \App\Url $url
     */
    public function updateSsl(Url $url)
    {

        Log::info($url->url . ' Updating SSL');

        $uri = Http::createFromString($url->url);

        $socket_uri = 'ssl://' . $uri->getHost() . ':'
            . (is_null($uri->getPort()) ? 443 : $uri->getPort());

        // Save the current error reporter and disable warnings for now
        set_error_handler(function () {

            return true;
        });

        // Get the peer certificate
        $socket_response = stream_socket_client(
            $socket_uri, $errno, $errstr,
            5, // timeout
            STREAM_CLIENT_CONNECT,
            stream_context_create([
                'ssl' => [
                    'capture_peer_cert' => true
                ]
            ]));

        // Restore the error reporter
        restore_error_handler();

        if ($errno !== 0) {

            Log::warning($url->url . ' Failed to read SSL information for ' . $socket_uri
                . '. Error: ' . $errstr);

            return;
        }

        if (!$socket_response) {

            Log::warning($url->url . ' Unable to get a connection to ' . $socket_uri);

            return;
        }

        $certificate = stream_context_get_params(
                           $socket_response)['options']['ssl']['peer_certificate'];
        $parsed_certificate = openssl_x509_parse($certificate);
        $public_key_info = openssl_pkey_get_details(
            openssl_pkey_get_public($certificate));

        // Update the certificate information for the URL
        $ssl = $url->ssl instanceof Certificate ? $url->ssl : new Certificate;

        $ssl->name = $parsed_certificate['name'];
        $ssl->cn = $parsed_certificate['subject']['CN'];
        $ssl->subject = $parsed_certificate['subject'];
        $ssl->hash = $parsed_certificate['hash'];
        $ssl->issuer = $parsed_certificate['issuer'];
        $ssl->version = $parsed_certificate['version'];
        $ssl->serial_number = $parsed_certificate['serialNumber'];
        $ssl->valid_from = Carbon::createFromTimestamp($parsed_certificate['validFrom_time_t']);
        $ssl->valid_to = Carbon::createFromTimestamp($parsed_certificate['validTo_time_t']);
        $ssl->purposes = $parsed_certificate['purposes'];
        $ssl->extentions = $parsed_certificate['extensions'];
        $ssl->key_bits = $public_key_info['bits'];
        $ssl->public_key = $public_key_info['key'];

        $url->ssl()->save($ssl);

        return;
    }
}
