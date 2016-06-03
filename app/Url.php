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

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Url
 * @package App
 */
class Url extends Model
{

    /**
     * @var array
     */
    protected $fillable = ['url', 'last_check', 'features'];

    /**
     * @var array
     */
    protected $casts = [
        'features' => 'array',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'last_check'];

    /**
     * @return bool|null
     * @throws \Exception
     */
    public function delete()
    {

        $this->meta()->delete();
        $this->cookies()->delete();
        $this->headers()->delete();
        $this->ssl()->delete();
        $this->body()->delete();

        return parent::delete();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function meta()
    {

        return $this->hasOne(Meta::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cookies()
    {

        return $this->hasMany(Cookie::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function headers()
    {

        return $this->hasMany(Header::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function ssl()
    {

        return $this->hasOne(Certificate::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function body()
    {

        return $this->hasOne(Body::class);
    }
}
