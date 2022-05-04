<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'firstName' => Str::random(10),
            'lastName' => Str::random(10),
            'email' => 'admin@admin.com',
            'password' => bcrypt('P@$$w0rd'),
        ]);
    }
}
