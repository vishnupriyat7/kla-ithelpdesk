<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SectionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('sections')->delete();
        
        \DB::table('sections')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Office of Secretary',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'JS/AS/SS',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'IT',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
            'name' => 'IT(eNiyamasabha)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Services A',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Services B',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Services C',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Accounts A',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Accounts B',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Accounts C',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'Accounts D',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'Accounts E',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'Accounts F',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'CAD Cell',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'Estimates Committee A',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'Estimates Committee B',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'Committee on Environment',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'Committee on Official Language',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'Committee on Petitions -A',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'Committee on Petitions -B',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'Committee on Petitions C',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            21 => 
            array (
                'id' => 22,
                'name' => 'Committee on Public Accounts A Section',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            22 => 
            array (
                'id' => 23,
                'name' => 'Committee on Public Accounts B Section',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            23 => 
            array (
                'id' => 24,
                'name' => 'Committee on Public Undertakings A',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            24 => 
            array (
                'id' => 25,
                'name' => 'Committee on Public Undertakings B',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            25 => 
            array (
                'id' => 26,
                'name' => 'Committee on Subordinate Legislation',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            26 => 
            array (
                'id' => 27,
                'name' => 'Committee on the Welfare of Fishermen and Allied Workers',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            27 => 
            array (
                'id' => 28,
                'name' => 'Committee on the Welfare of Non Resident Keralites',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            28 => 
            array (
                'id' => 29,
                'name' => 'Committee on the Welfare of Other Backward Classes',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            29 => 
            array (
                'id' => 30,
                'name' => 'Committee on the Welfare of Scheduled Castes and Scheduled Tribes- A',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            30 => 
            array (
                'id' => 31,
                'name' => 'Committee on the Welfare of Scheduled Castes and Scheduled Tribes B',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            31 => 
            array (
                'id' => 32,
                'name' => 'Committee on the Welfare of Senior Citizens',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            32 => 
            array (
                'id' => 33,
                'name' => 'Committee on the Welfare of Women Children Transgenders and Differently Abled A',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            33 => 
            array (
                'id' => 34,
                'name' => 'Committee on the Welfare of Women Children Transgenders and Differently Abled B',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            34 => 
            array (
                'id' => 35,
                'name' => 'Committee on the Welfare of Youth and Youth Affairs',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            35 => 
            array (
                'id' => 36,
                'name' => 'Data Resource Development  A',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            36 => 
            array (
                'id' => 37,
            'name' => 'AS (WCTDA, FAW)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            37 => 
            array (
                'id' => 38,
            'name' => 'AS (Table, CPL & Press)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            38 => 
            array (
                'id' => 39,
            'name' => 'AS (Executive Director (K-LAMPS))',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            39 => 
            array (
                'id' => 40,
            'name' => 'AS (Services)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            40 => 
            array (
                'id' => 41,
                'name' => 'House Keeping A',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            41 => 
            array (
                'id' => 42,
            'name' => 'JS (Envnt Com, HK & Sub Com D)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            42 => 
            array (
                'id' => 43,
            'name' => 'JS (Director K-LAMPS (PS & M) & Museum)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            43 => 
            array (
                'id' => 44,
            'name' => 'JS (Finance Officer)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            44 => 
            array (
                'id' => 45,
            'name' => 'JS (PUC, Snr Ctzn & Sub Com B)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            45 => 
            array (
                'id' => 46,
                'name' => 'Internal Audit',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            46 => 
            array (
                'id' => 47,
            'name' => 'JS (Legislation, DRD, & PMBR)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            47 => 
            array (
                'id' => 48,
            'name' => 'JS (Estate Officer)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            48 => 
            array (
                'id' => 49,
                'name' => 'Local Fund Accounts A',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            49 => 
            array (
                'id' => 50,
            'name' => 'JS (Petitions, YAC, Sub Com G & Press)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            50 => 
            array (
                'id' => 51,
                'name' => 'Local Fund Accounts B',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            51 => 
            array (
                'id' => 52,
            'name' => 'JS (PAC, Sub Com F, Int Audit)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            52 => 
            array (
                'id' => 53,
            'name' => 'JS (SCSTC, OLC & OBCC)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            53 => 
            array (
                'id' => 54,
            'name' => 'JS (Estimates)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            54 => 
            array (
                'id' => 55,
            'name' => 'JS (Question, Research, Protocol & CAD)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            55 => 
            array (
                'id' => 56,
                'name' => 'Museum',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            56 => 
            array (
                'id' => 57,
            'name' => 'JS (NORKA, Subnt Legsltn, Sub Com E)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            57 => 
            array (
                'id' => 58,
            'name' => 'JS (IT & e-Niyamasabha)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            58 => 
            array (
                'id' => 59,
                'name' => 'Press Relations',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            59 => 
            array (
                'id' => 60,
            'name' => 'Deputy Secretary (Committee on Local Fund Accounts,  Subject Committee C)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            60 => 
            array (
                'id' => 61,
            'name' => 'Deputy Secretary (Legislation)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            61 => 
            array (
                'id' => 62,
                'name' => 'Printing Press',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            62 => 
            array (
                'id' => 63,
                'name' => 'Private Member Bills and Resolutions',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            63 => 
            array (
                'id' => 64,
            'name' => 'Joint Director K-LAMPS (Parliamentary Studies)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            64 => 
            array (
                'id' => 65,
                'name' => 'Protocol',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            65 => 
            array (
                'id' => 66,
            'name' => 'Deputy Secretary (WCTDA Committee/FAW Committee)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            66 => 
            array (
                'id' => 67,
            'name' => 'Deputy Secretary (Committee on Estimates)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            67 => 
            array (
                'id' => 68,
            'name' => 'Joint Director K-LAMPS (Media)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            68 => 
            array (
                'id' => 69,
                'name' => 'Question -A',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            69 => 
            array (
                'id' => 70,
                'name' => 'Question -B',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            70 => 
            array (
                'id' => 71,
            'name' => 'Deputy Secretary (SCST Committee)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            71 => 
            array (
                'id' => 72,
                'name' => 'Reception',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            72 => 
            array (
                'id' => 73,
            'name' => 'Deputy Secretary (Accounts)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            73 => 
            array (
                'id' => 74,
            'name' => 'Deputy Secretary (Research Officer/Question)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            74 => 
            array (
                'id' => 75,
                'name' => 'Research',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            75 => 
            array (
                'id' => 76,
            'name' => 'Deputy Secretary (MLA Hostel)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            76 => 
            array (
                'id' => 77,
            'name' => 'Deputy Secretary (PAC/Sub.Com F)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            77 => 
            array (
                'id' => 78,
                'name' => 'Subject Committee A',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            78 => 
            array (
                'id' => 79,
            'name' => 'Deputy Secretary (Committee on Subordinate Legislation)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            79 => 
            array (
                'id' => 80,
                'name' => 'Subject Committee B',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            80 => 
            array (
                'id' => 81,
            'name' => 'Deputy Secretary (IT Section & E-Niyamasabha)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            81 => 
            array (
                'id' => 82,
            'name' => 'Deputy Secretary (Committee on Petition)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            82 => 
            array (
                'id' => 83,
            'name' => 'Deputy Secretary (House Keeping, Committee on Environment, Subject Committee D)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            83 => 
            array (
                'id' => 84,
                'name' => 'Subject Committee C',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            84 => 
            array (
                'id' => 85,
            'name' => 'Deputy Secretary (Committee on Public Undertakings, Subject Committee B, Committee on Senior Citizen)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            85 => 
            array (
                'id' => 86,
            'name' => 'Deputy Secretary (CAD Cell)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            86 => 
            array (
                'id' => 87,
            'name' => 'Office of Deputy Secretary (Services)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            87 => 
            array (
                'id' => 88,
                'name' => 'Table',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            88 => 
            array (
                'id' => 89,
                'name' => 'Subject Committee D',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            89 => 
            array (
                'id' => 90,
                'name' => 'Subject Committee E',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            90 => 
            array (
                'id' => 91,
                'name' => 'Subject Committee F',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            91 => 
            array (
                'id' => 92,
                'name' => 'Subject Committee G',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            92 => 
            array (
                'id' => 93,
                'name' => 'Special Secretary',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            93 => 
            array (
                'id' => 94,
                'name' => 'Committee on the Welfare of Women Children Transgenders and Differently Abled C',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            94 => 
            array (
                'id' => 95,
                'name' => 'Members\' Amenities Sections A',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            95 => 
            array (
                'id' => 96,
                'name' => 'Members\' Amenities Sections B',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            96 => 
            array (
                'id' => 97,
                'name' => 'Members\' Amenities Sections C',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            97 => 
            array (
                'id' => 98,
                'name' => 'Members\' Amenities Sections D',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            98 => 
            array (
                'id' => 99,
                'name' => 'Members\' Amenities Sections E',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            99 => 
            array (
                'id' => 100,
                'name' => 'Members\' Amenities Sections F',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            100 => 
            array (
                'id' => 101,
                'name' => 'Members\' Amenities Sections G',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            101 => 
            array (
                'id' => 102,
                'name' => 'Editing A',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            102 => 
            array (
                'id' => 103,
                'name' => 'Editing B',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            103 => 
            array (
                'id' => 104,
                'name' => 'Editing C',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            104 => 
            array (
                'id' => 105,
                'name' => 'Editing D',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            105 => 
            array (
                'id' => 106,
                'name' => 'Editing E',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            106 => 
            array (
                'id' => 107,
                'name' => 'Editing F',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            107 => 
            array (
                'id' => 108,
                'name' => 'Editing G',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            108 => 
            array (
                'id' => 109,
                'name' => 'Editing H',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            109 => 
            array (
                'id' => 110,
                'name' => 'Editing I',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            110 => 
            array (
                'id' => 111,
                'name' => 'Editing J',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            111 => 
            array (
                'id' => 112,
                'name' => 'Chief Editor',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            112 => 
            array (
                'id' => 113,
            'name' => 'Library (General)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => '2025-08-20 12:33:12',
            ),
            113 => 
            array (
                'id' => 114,
                'name' => 'Library Reference',
                'status' => '0',
                'created_at' => NULL,
                'updated_at' => '2025-08-20 12:31:08',
            ),
            114 => 
            array (
                'id' => 115,
                'name' => 'Library Documentation',
                'status' => '0',
                'created_at' => NULL,
                'updated_at' => '2025-08-20 12:31:36',
            ),
            115 => 
            array (
                'id' => 116,
                'name' => 'Library Technical',
                'status' => '0',
                'created_at' => NULL,
                'updated_at' => '2025-08-20 12:31:46',
            ),
            116 => 
            array (
                'id' => 117,
                'name' => 'Library Maintenance',
                'status' => '0',
                'created_at' => NULL,
                'updated_at' => '2025-08-20 12:31:59',
            ),
            117 => 
            array (
                'id' => 118,
                'name' => 'Library Digitisation',
                'status' => '0',
                'created_at' => NULL,
                'updated_at' => '2025-08-20 12:32:10',
            ),
            118 => 
            array (
                'id' => 119,
            'name' => 'K-LAMPS (PS) A',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            119 => 
            array (
                'id' => 120,
                'name' => 'Library Members Reference Branch',
                'status' => '0',
                'created_at' => NULL,
                'updated_at' => '2025-08-20 12:32:21',
            ),
            120 => 
            array (
                'id' => 121,
            'name' => 'K-LAMPS (PS) B',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            121 => 
            array (
                'id' => 122,
            'name' => 'K-LAMPS (Media) C',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            122 => 
            array (
                'id' => 123,
            'name' => 'K-LAMPS (Media) D',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            123 => 
            array (
                'id' => 124,
            'name' => 'K-LAMPS (Accounts)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            124 => 
            array (
                'id' => 125,
                'name' => 'House Keeping B',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            125 => 
            array (
                'id' => 126,
            'name' => 'Deputy Secretary (Table, CPL)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            126 => 
            array (
                'id' => 127,
            'name' => 'Office Section((Despatch)Assembly)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            127 => 
            array (
                'id' => 128,
            'name' => 'Office Section((Inward)Assembly)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            128 => 
            array (
                'id' => 129,
            'name' => 'Librarian (Reference & KLAMPS)',
                'status' => '0',
                'created_at' => NULL,
                'updated_at' => '2025-08-20 12:32:35',
            ),
            129 => 
            array (
                'id' => 130,
            'name' => 'Office Section(Despatch(Admin))',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            130 => 
            array (
                'id' => 131,
            'name' => 'Office Section(Inward(Admin))',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            131 => 
            array (
                'id' => 132,
                'name' => 'Fair Copy Branch 1',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            132 => 
            array (
                'id' => 133,
                'name' => 'Fair Copy Branch 2',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            133 => 
            array (
                'id' => 134,
                'name' => 'Fair Copy Branch 3',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            134 => 
            array (
                'id' => 135,
                'name' => 'Fair Copy Branch 4',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            135 => 
            array (
                'id' => 136,
                'name' => 'Fair Copy Branch 5',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            136 => 
            array (
                'id' => 137,
                'name' => 'Fair Copy Branch 6',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            137 => 
            array (
                'id' => 138,
                'name' => 'Fair Copy Branch 7',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            138 => 
            array (
                'id' => 139,
                'name' => 'Fair Copy Branch 8',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            139 => 
            array (
                'id' => 140,
            'name' => 'Office Section (KLAMPS Cum Fair Copy)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            140 => 
            array (
                'id' => 141,
                'name' => 'Office of Speaker',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            141 => 
            array (
                'id' => 142,
                'name' => 'Office of Deputy Speaker',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            142 => 
            array (
                'id' => 143,
                'name' => 'Data Resource Development B',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            143 => 
            array (
                'id' => 144,
                'name' => 'Legislation',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            144 => 
            array (
                'id' => 145,
                'name' => 'Committee on Papers Laid',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            145 => 
            array (
                'id' => 146,
            'name' => 'Office Section (MLA Hostel)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            146 => 
            array (
                'id' => 147,
            'name' => 'Library (MLA Hostel)',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            147 => 
            array (
                'id' => 148,
                'name' => 'Records',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            148 => 
            array (
                'id' => 149,
                'name' => 'Local Fund Accounts C',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            149 => 
            array (
                'id' => 150,
                'name' => 'e-Niyamasabha Task Force',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            150 => 
            array (
                'id' => 151,
                'name' => 'Digitization',
                'status' => '1',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}