<?php

namespace Database\Seeders\Master;

use App\Models\Master\Leave;
use Illuminate\Database\Seeder;

class LeaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $leave = new Leave();
        $leave->name = 'Cuti Tahunan';
        $leave->allocated_days = '21';
        $leave->note = 'cuti tahunan, akumlasi maksimal dalam 1 tahun';
        $leave->save();

    }
}
