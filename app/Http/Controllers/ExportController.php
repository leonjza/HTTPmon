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

namespace App\Http\Controllers;

use App\Certificate;
use App\Http\Requests;
use App\Url;
use League\Csv\Writer;
use SplTempFileObject;

/**
 * Class ExportController
 * @package App\Http\Controllers
 */
class ExportController extends Controller
{

    /**
     *
     */
    public function exportAll()
    {

        // Get the data and the CSV writer ready
        $urls = Url::with('meta', 'ssl')->get();
        $csv = Writer::createFromFileObject(new SplTempFileObject());

        // Add the field headings
        $csv->insertOne([
            'ID',
            'URL',
            'Last Check',
            'Status Code',
            'HTTP Protocol Version',
            'HTTP Reason Phrase',
            'Server Header',
            'SSL Name',
            'SSL Common Name',
            'SSL Hash',
            'SSL Version',
            'SSL Serial Number',
            'SSL Valid From',
            'SSL Valid To',
            'SSL Signature Type SN',
            'SSL Signature Type LN',
            'SSL Signature Type ID',
            'Key Bits',
            'Last SSL Labs Check',
            'SSL Labs Rating',
        ]);

        // Populate the data
        $csv->insertAll($urls->map(function ($entry) {

            $return = collect([

                $entry->id,
                $entry->url,
                $entry->last_check,

                // Meta
                $entry->meta->status_code,
                $entry->meta->protocol_version,
                $entry->meta->reason_phrase,
                $entry->meta->server,

            ]);

            // Add SSL information if we have it.
            if ($entry->ssl instanceof Certificate) {

                $return = $return->merge([
                    $entry->ssl->name ? $entry->ssl->name : null,
                    $entry->ssl->cn ? $entry->ssl->cn : null,
                    $entry->ssl->hash ? $entry->ssl->hash : null,
                    $entry->ssl->version ? $entry->ssl->version : null,
                    $entry->ssl->serial_number ? $entry->ssl->serial_number : null,
                    $entry->ssl->valid_from ? (string)$entry->ssl->valid_from : null,
                    $entry->ssl->valid_to ? (string)$entry->ssl->valid_to : null,
                    $entry->ssl->signature_type_sn ? $entry->ssl->signature_type_sn : null,
                    $entry->ssl->signature_type_ln ? $entry->ssl->signature_type_ln : null,
                    $entry->ssl->signature_type_id ? $entry->ssl->signature_type_id : null,
                    $entry->ssl->key_bits ? $entry->ssl->key_bits : null,
                    $entry->ssl->ssl_labs_last_update ? (string)$entry->ssl->ssl_labs_last_update : null,
                    $entry->ssl->ssl_labs_rating ? $entry->ssl->ssl_labs_rating : null,
                ]);
            }

            return $return->toArray();

        }));

        $csv->output('urls.csv');

    }
}
