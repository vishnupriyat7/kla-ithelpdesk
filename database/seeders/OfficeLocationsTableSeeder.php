<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OfficeLocationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('office_locations')->delete();
        
        \DB::table('office_locations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'location' => 'Assembly Block',
                'administrative_office_id' => 1,
                'created_at' => NULL,
                'updated_at' => '2024-06-29 12:01:21',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'location' => 'Admin Block',
                'administrative_office_id' => 1,
                'created_at' => NULL,
                'updated_at' => '2024-06-29 12:01:28',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'location' => 'Museum',
                'administrative_office_id' => 1,
                'created_at' => '2025-02-03 10:23:43',
                'updated_at' => '2025-02-03 10:23:43',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'location' => 'Reception',
                'administrative_office_id' => 1,
                'created_at' => '2025-08-08 09:36:24',
                'updated_at' => '2025-08-08 09:36:24',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'location' => 'Nila',
                'administrative_office_id' => 2,
                'created_at' => NULL,
                'updated_at' => '2026-07-13 13:05:29',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'location' => 'Chandhragiri',
                'administrative_office_id' => 2,
                'created_at' => '2026-07-13 13:05:29',
                'updated_at' => '2026-07-13 13:05:29',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'location' => 'Periyar',
                'administrative_office_id' => 2,
                'created_at' => '2026-07-13 13:05:29',
                'updated_at' => '2026-07-13 13:05:29',
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'location' => 'Neyyar',
                'administrative_office_id' => 2,
                'created_at' => '2026-07-13 13:05:29',
                'updated_at' => '2026-07-13 13:05:29',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}