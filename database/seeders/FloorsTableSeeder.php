<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FloorsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('floors')->delete();
        
        \DB::table('floors')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Cellar',
                'created_at' => '2026-07-13 13:24:06',
                'updated_at' => '2026-07-13 13:24:06',
                'sort_order' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Ground',
                'created_at' => '2026-07-13 13:24:06',
                'updated_at' => '2026-07-13 13:24:06',
                'sort_order' => 2,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'First',
                'created_at' => '2026-07-13 13:24:06',
                'updated_at' => '2026-07-13 13:24:06',
                'sort_order' => 3,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Second',
                'created_at' => '2026-07-13 13:24:06',
                'updated_at' => '2026-07-13 13:24:06',
                'sort_order' => 4,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Third',
                'created_at' => '2026-07-13 13:24:06',
                'updated_at' => '2026-07-13 13:24:06',
                'sort_order' => 5,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Fourth',
                'created_at' => '2026-07-13 13:24:06',
                'updated_at' => '2026-07-13 13:24:06',
                'sort_order' => 6,
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Fifth',
                'created_at' => '2026-07-13 13:24:06',
                'updated_at' => '2026-07-13 13:24:06',
                'sort_order' => 7,
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Sixth',
                'created_at' => '2026-07-13 13:24:06',
                'updated_at' => '2026-07-13 13:24:06',
                'sort_order' => 8,
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Seventh',
                'created_at' => '2026-07-13 13:24:06',
                'updated_at' => '2026-07-13 13:24:06',
                'sort_order' => 9,
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Eighth',
                'created_at' => '2026-07-13 13:24:06',
                'updated_at' => '2026-07-13 13:24:06',
                'sort_order' => 10,
            ),
        ));
        
        
    }
}