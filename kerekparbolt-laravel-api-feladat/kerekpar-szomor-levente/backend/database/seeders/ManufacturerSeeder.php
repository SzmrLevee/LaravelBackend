<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ManufacturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('manufacturers')->insert([
            ['name' => 'Rockrider', 'website' => 'https://www.decathlon.hu/'],
            ['name' => 'Elops', 'website' => null],
            ['name' => 'Triban', 'website' => 'https://www.decathlon.hu/'],
            ['name' => 'Kruz Bike Company', 'website' => 'https://kruzbike.com/'],
            ['name' => 'Hoprider', 'website' => 'https://www.decathlon.hu/'],
        ]);
    }
}
