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

namespace App\Console\Commands\Httpmon;

use App\User;
use Illuminate\Console\Command;

class ResetAdmin extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'httpmon:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the admin password';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $admin = User::firstOrNew(['name' => 'admin']);

        $password = null;
        while (strlen($password) < 6)
            $password = $this->secret(
                'Please enter a min 6 character password for the \'admin\' user');

        $admin->fill([
            'name'     => 'admin',
            'email'    => 'admin@httpmon',
            'password' => bcrypt($password)
        ])->save();

        $this->info('Done');
    }
}
