<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BicycleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bicycles')->insert([
            ['name' => 'MTB kerékpár ST 530 S, 27,5", fekete, piros', 'wheel_size' => 27, 'gears' => 9, 'sex' => 'férfi', 'type' => 'MTB', 'size' => 'S', 'color' => 'fekete, piros', 'manufacturer_id' => 1],
            ['name' => 'MTB kerékpár ST 530 S, 27,5", fekete, piros', 'wheel_size' => 27, 'gears' => 9, 'sex' => 'férfi', 'type' => 'MTB', 'size' => 'M', 'color' => 'fekete, piros', 'manufacturer_id' => 1],
            ['name' => 'MTB kerékpár ST 530 S, 27,5", fekete, piros', 'wheel_size' => 27, 'gears' => 9, 'sex' => 'férfi', 'type' => 'MTB', 'size' => 'L', 'color' => 'fekete, piros', 'manufacturer_id' => 1],
            ['name' => 'MTB kerékpár ST 530 S, 27,5", fekete, piros', 'wheel_size' => 27, 'gears' => 9, 'sex' => 'férfi', 'type' => 'MTB', 'size' => 'XL', 'color' => 'fekete, piros', 'manufacturer_id' => 1],
            ['name' => 'MTB kerékpár ST 530 S, 27,5", kék', 'wheel_size' => 27, 'gears' => 9, 'sex' => 'női', 'type' => 'MTB', 'size' => 'S', 'color' => 'kék', 'manufacturer_id' => 1],
            ['name' => 'MTB kerékpár ST 530 S, 27,5", kék', 'wheel_size' => 27, 'gears' => 9, 'sex' => 'női', 'type' => 'MTB', 'size' => 'M', 'color' => 'kék', 'manufacturer_id' => 1],
            ['name' => 'MTB kerékpár ST 530 S, 27,5", kék', 'wheel_size' => 27, 'gears' => 9, 'sex' => 'női', 'type' => 'MTB', 'size' => 'L', 'color' => 'kék', 'manufacturer_id' => 1],
            ['name' => 'MTB kerékpár ST 100, 27,5", sárga', 'wheel_size' => 27, 'gears' => 6, 'sex' => 'férfi', 'type' => 'MTB', 'size' => 'M', 'color' => 'sárga', 'manufacturer_id' => 1],
            ['name' => 'MTB kerékpár ST 100, 27,5", sárga', 'wheel_size' => 27, 'gears' => 6, 'sex' => 'férfi', 'type' => 'MTB', 'size' => 'L', 'color' => 'sárga', 'manufacturer_id' => 1],
            ['name' => 'MTB kerékpár ST 100, 27,5", sárga', 'wheel_size' => 27, 'gears' => 6, 'sex' => 'férfi', 'type' => 'MTB', 'size' => 'XL', 'color' => 'sárga', 'manufacturer_id' => 1],
            ['name' => 'MTB kerékpár ST 900, 29", szürke, narancs', 'wheel_size' => 29, 'gears' => 12, 'sex' => 'férfi', 'type' => 'MTB', 'size' => 'M', 'color' => 'szürke, narancs', 'manufacturer_id' => 1],
            ['name' => 'MTB kerékpár ST 900, 29", szürke, narancs', 'wheel_size' => 29, 'gears' => 12, 'sex' => 'férfi', 'type' => 'MTB', 'size' => 'L', 'color' => 'szürke, narancs', 'manufacturer_id' => 1],
            ['name' => 'MTB kerékpár ST 900, 29", szürke, narancs', 'wheel_size' => 29, 'gears' => 12, 'sex' => 'férfi', 'type' => 'MTB', 'size' => 'XL', 'color' => 'szürke, narancs', 'manufacturer_id' => 1],
            ['name' => 'Városi kerékpár Elops 500, alacsony vázas, fekete', 'wheel_size' => 28, 'gears' => 6, 'sex' => 'női', 'type' => 'városi', 'size' => null, 'color' => 'fekete', 'manufacturer_id' => 2],
            ['name' => 'Városi kerékpár Elops 500, alacsony vázas, kék', 'wheel_size' => 28, 'gears' => 6, 'sex' => 'női', 'type' => 'városi', 'size' => null, 'color' => 'kék', 'manufacturer_id' => 2],
            ['name' => 'Városi kerékpár Elops 120, alacsony vázas, fekete', 'wheel_size' => 28, 'gears' => 1, 'sex' => 'női', 'type' => 'városi', 'size' => null, 'color' => 'fekete', 'manufacturer_id' => 2],
            ['name' => 'Városi kerékpár Elops 120, alacsony vázas, kék', 'wheel_size' => 28, 'gears' => 1, 'sex' => 'női', 'type' => 'városi', 'size' => null, 'color' => 'kék', 'manufacturer_id' => 2],
            ['name' => 'Városi kerékpár Elops 120, magas vázas, fekete', 'wheel_size' => 28, 'gears' => 1, 'sex' => 'férfi', 'type' => 'városi', 'size' => null, 'color' => 'fekete', 'manufacturer_id' => 2],
            ['name' => 'Városi kerékpár Elops 120, magas vázas, kék', 'wheel_size' => 28, 'gears' => 1, 'sex' => 'férfi', 'type' => 'városi', 'size' => null, 'color' => 'kék', 'manufacturer_id' => 2],
            ['name' => 'Országúti kerékpár Triban 100, fekete', 'wheel_size' => 28, 'gears' => 14, 'sex' => 'férfi', 'type' => 'országúti', 'size' => 'M', 'color' => 'fekete', 'manufacturer_id' => 3],
            ['name' => 'Országúti kerékpár Triban 100, fekete', 'wheel_size' => 28, 'gears' => 14, 'sex' => 'férfi', 'type' => 'országúti', 'size' => 'L', 'color' => 'fekete', 'manufacturer_id' => 3],
            ['name' => 'Országúti kerékpár Triban 500, fekete, piros', 'wheel_size' => 28, 'gears' => 16, 'sex' => 'férfi', 'type' => 'országúti', 'size' => 'M', 'color' => 'fekete, piros', 'manufacturer_id' => 3],
            ['name' => 'Országúti kerékpár Triban 500, fekete, piros', 'wheel_size' => 28, 'gears' => 16, 'sex' => 'férfi', 'type' => 'országúti', 'size' => 'L', 'color' => 'fekete, piros', 'manufacturer_id' => 3],
            ['name' => 'Országúti kerékpár Triban 500, fekete, piros', 'wheel_size' => 28, 'gears' => 16, 'sex' => 'férfi', 'type' => 'országúti', 'size' => 'XL', 'color' => 'fekete, piros', 'manufacturer_id' => 3],
            ['name' => 'Országúti kerékpár Triban 520, fekete', 'wheel_size' => 28, 'gears' => 22, 'sex' => 'férfi', 'type' => 'országúti', 'size' => 'M', 'color' => 'fekete', 'manufacturer_id' => 3],
            ['name' => 'Országúti kerékpár Triban 520, fekete', 'wheel_size' => 28, 'gears' => 22, 'sex' => 'férfi', 'type' => 'országúti', 'size' => 'L', 'color' => 'fekete', 'manufacturer_id' => 3],
            ['name' => 'Országúti kerékpár Triban 520, fekete', 'wheel_size' => 28, 'gears' => 22, 'sex' => 'férfi', 'type' => 'országúti', 'size' => 'XL', 'color' => 'fekete', 'manufacturer_id' => 3],
            ['name' => 'Gravel kerékpár GRVL 900, halványrózsaszín', 'wheel_size' => 27, 'gears' => 22, 'sex' => 'női', 'type' => 'cross', 'size' => 'S', 'color' => 'halványrózsaszín', 'manufacturer_id' => 3],
            ['name' => 'Gravel kerékpár GRVL 900, halványrózsaszín', 'wheel_size' => 27, 'gears' => 22, 'sex' => 'női', 'type' => 'cross', 'size' => 'M', 'color' => 'halványrózsaszín', 'manufacturer_id' => 3],
            ['name' => 'Gravel kerékpár GRVL 900, fekete, narancs', 'wheel_size' => 27, 'gears' => 22, 'sex' => 'unisex', 'type' => 'cross', 'size' => 'S', 'color' => 'fekete, narancs', 'manufacturer_id' => 3],
            ['name' => 'Gravel kerékpár GRVL 900, fekete, narancs', 'wheel_size' => 27, 'gears' => 22, 'sex' => 'unisex', 'type' => 'cross', 'size' => 'M', 'color' => 'fekete, narancs', 'manufacturer_id' => 3],
            ['name' => 'Gravel kerékpár GRVL 900, fekete, narancs', 'wheel_size' => 27, 'gears' => 22, 'sex' => 'unisex', 'type' => 'cross', 'size' => 'L', 'color' => 'fekete, narancs', 'manufacturer_id' => 3],
            ['name' => 'Gravel kerékpár GRVL 900, fekete, narancs', 'wheel_size' => 27, 'gears' => 22, 'sex' => 'unisex', 'type' => 'cross', 'size' => 'XL', 'color' => 'fekete, narancs', 'manufacturer_id' => 3],
            ['name' => 'Riverside 500, szürke', 'wheel_size' => 28, 'gears' => 18, 'sex' => 'férfi', 'type' => 'cross', 'size' => 'M', 'color' => 'szürke', 'manufacturer_id' => 5],
            ['name' => 'Riverside 500, szürke', 'wheel_size' => 28, 'gears' => 18, 'sex' => 'férfi', 'type' => 'cross', 'size' => 'L', 'color' => 'szürke', 'manufacturer_id' => 5],
            ['name' => 'Riverside 500, szürke', 'wheel_size' => 28, 'gears' => 18, 'sex' => 'férfi', 'type' => 'cross', 'size' => 'XL', 'color' => 'szürke', 'manufacturer_id' => 5],
            ['name' => 'Gravel kerékpár GRVL 120, fekete', 'wheel_size' => 28, 'gears' => 16, 'sex' => 'férfi', 'type' => 'cross', 'size' => 'M', 'color' => 'fekete', 'manufacturer_id' => 3],
            ['name' => 'Gravel kerékpár GRVL 120, fekete', 'wheel_size' => 28, 'gears' => 16, 'sex' => 'férfi', 'type' => 'cross', 'size' => 'L', 'color' => 'fekete', 'manufacturer_id' => 3],
            ['name' => 'Gravel kerékpár GRVL 120, fekete', 'wheel_size' => 28, 'gears' => 16, 'sex' => 'férfi', 'type' => 'cross', 'size' => 'XL', 'color' => 'fekete', 'manufacturer_id' => 3],
            ['name' => 'Riverside 120, szürke', 'wheel_size' => 28, 'gears' => 6, 'sex' => 'férfi', 'type' => 'cross', 'size' => null, 'color' => 'szürke', 'manufacturer_id' => 5],
        ]);
    }
}
