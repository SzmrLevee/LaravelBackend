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
        DB::table("manufacturers")->insert([
            [
                "id" => "1",
                "name" => 'Rockrider',
                "website" => "https://www.decathlon.hu/"
            ],
            [
                "id" => "2",
                "name" => 'Elops',
                "website" => null
            ],
            [
                "id" => "3",
                "name" => 'Triban',
                "website" => "https://www.decathlon.hu/"
            ],
            [
                "id" => "4",
                "name" => 'Kruz Bike Company',
                "website" => "https://kruzbike.com/"
            ],
            [
                "id" => "5",
                "name" => 'Van Rysel',
                "website" => "https://www.vanryselcycling.com/"
            ],
            [
                "id" => "6",
                "name" => 'XFACT',
                "website" => "https://www.hervis.hu/shop/M%C3%A1rka/X-Fact/c/1_162"
            ],
        ]);
    }
}
