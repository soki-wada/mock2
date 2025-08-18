<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminsTableSeeder extends Seeder
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
            'name' => '佐藤一郎',
            'email' => 'satou@gmail.com',
            'password' => Hash::make('87654321')
        ];
        DB::table('admins')->insert($param);
    }
}
