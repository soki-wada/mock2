<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class AttendancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $startDate = Carbon::create(2025, 6, 1);
        $endDate = Carbon::create(2025, 6, 30);

        for ($date = $startDate; $date->lte($endDate); $date->addDay()){
            DB::table('attendances')->insert([
                'user_id'    => 1,
                'date'       => $date->format('Y-m-d'),
                'clock_in'   => '09:00:00',
                'clock_out'  => '17:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
