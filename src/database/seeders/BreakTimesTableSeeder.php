<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BreakTimesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        for ($attendanceId = 1; $attendanceId <= 30; $attendanceId++){
            DB::table('break_times')->insert([
                'attendance_id'    => $attendanceId,
                'break_start'   => '12:00:00',
                'break_end'  => '13:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
