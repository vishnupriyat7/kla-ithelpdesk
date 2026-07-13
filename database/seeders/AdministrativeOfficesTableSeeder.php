<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdministrativeOfficesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('administrative_offices')->delete();
        
        \DB::table('administrative_offices')->insert(array (
            0 => 
            array (
                'id' => 1,
                'office_name' => 'Secretariat',
                'created_at' => '2024-06-29 11:56:55',
                'updated_at' => '2024-06-29 11:56:55',
            ),
            1 => 
            array (
                'id' => 2,
                'office_name' => 'MLA Hostel',
                'created_at' => '2024-06-29 11:57:03',
                'updated_at' => '2024-06-29 11:57:03',
            ),
            2 => 
            array (
                'id' => 3,
                'office_name' => 'Speaker Office',
                'created_at' => '2024-07-05 12:08:46',
                'updated_at' => '2024-07-05 12:08:46',
            ),
            3 => 
            array (
                'id' => 4,
                'office_name' => 'DySpeaker Office',
                'created_at' => '2024-07-05 12:08:56',
                'updated_at' => '2024-07-05 12:08:56',
            ),
        ));
        
        
    }
}