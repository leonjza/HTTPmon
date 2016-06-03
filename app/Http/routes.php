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

Route::group(['middleware' => 'auth.basic'], function () {

    Route::get('/', ['as' => 'dashboard', 'uses' => 'DashboardController@getDashboard']);

    Route::group(['prefix' => 'url'], function () {

        Route::get('/new', ['as' => 'url.new', 'uses' => 'UrlController@newUrl']);
        Route::post('/new', ['as' => 'url.add.new', 'uses' => 'UrlController@addNewUrl']);
        Route::get('/{id}', ['as' => 'url', 'uses' => 'UrlController@getUrl'])->where('id', '[0-9]+');
        Route::post('/update/features', ['as' => 'url.update.features', 'uses' => 'UrlController@updateUrlFeatures']);
        Route::get('/update/{id}', ['as' => 'url.update', 'uses' => 'UrlController@updateURL'])->where('id', '[0-9]+');
        Route::get('/delete/{id}', ['as' => 'url.delete', 'uses' => 'UrlController@deleteUrl'])->where('id', '[0-9]+');
        Route::get('export', ['as' => 'url.export', 'uses' => 'ExportController@exportAll']);
    });

    Route::post('/search', ['as' => 'search', 'uses' => 'SearchController@searchUrls']);
});


