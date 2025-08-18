<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $param = [
            'name' => '山田太郎',
            'email' => 'yamada@gmail.com',
            'password' => Hash::make('12345678')
        ];
        DB::table('users')->insert($param);
    }
}
