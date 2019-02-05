<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Patient;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        if (app()->environment('local')) {
            DB::table('users')->delete();
        }

        if (app()->environment('staging')) {
            DB::table('users')->delete();
        }

        Patient::create([
            'id' => 1,
            'study_id' => 1,
            'site_id' => 1,
            'initials' => 'T_U',
            'dob' => Carbon::parse('1977-09-13'),
            'gender' => 'Male',
            'race' => 'Asian',
            'icf' => 1,
            'icf_date' => Carbon::parse('2018-10-23'),
            'status' => 0,
            'pat_id' => 1,
            'prefix' => 'VIOL-IND-001-PMCF/001',
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
