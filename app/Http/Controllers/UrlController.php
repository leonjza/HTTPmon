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

use App\Events\UrlMustUpdateEvent;
use App\Http\Requests;
use App\Http\Requests\NewUrl;
use App\Http\Requests\UpdateUrl;
use App\Url;
use Illuminate\Support\Facades\Event;

/**
 * Class UrlController
 * @package App\Http\Controllers
 */
class UrlController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function newUrl()
    {

        return view('add');
    }

    /**
     * @param \App\Http\Requests\NewUrl $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addNewUrl(NewUrl $request)
    {

        $url = Url::create($request->all());

        // Fire the update event
        Event::fire(new UrlMustUpdateEvent($url));

        return redirect()->route('url', [
            'id' => $url->id
        ])->with('success', 'URL Added!');
    }

    /**
     * @param $url_id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getUrl($url_id)
    {

        $url = Url::with(
            'meta', 'cookies', 'headers', 'ssl', 'body'
        )->findOrFail($url_id);

        return view('url', compact('url'));
    }

    /**
     * @param \App\Http\Requests\UpdateUrl $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUrlFeatures(UpdateUrl $request)
    {

        Url::find($request->get('id'))
            ->fill(['features' => $request->get('features')])
            ->save();

        return redirect()->back()
            ->with('success', 'Features Updated');

    }

    public function updateUrl($id)
    {

        $url = Url::findOrFail($id);

        Event::fire(new UrlMustUpdateEvent($url));

        return redirect()->back()
            ->with('success', 'URL Updated. SSL Labs rating will update when ready.');
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteUrl($id)
    {

        Url::find($id)->delete();

        return redirect()->back()
            ->with('success', 'Deleted!');
    }
}
