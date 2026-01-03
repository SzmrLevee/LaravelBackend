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
        DB::table("bicycles")->insert([
            ['name' => 'MTB kerékpár ST 530 S, 27,5”, fekete, piros', 'wheel_size' => '27', 'gears' => '9', 'sex' => 'férfi', 'type' => 'MTB', 'size' => 'S', 'color' => 'fekete, piros', 'manufacturer_id' => '1'],
            ['name' => 'MTB kerékpár ST 530 S, 27,5”, fekete, piros', 'wheel_size' => '27', 'gears' => '9', 'sex' => 'férfi', 'type' => 'MTB', 'size' => 'M', 'color' => 'fekete, piros', 'manufacturer_id' => '1'],
            ['name' => 'MTB kerékpár ST 530 S, 27,5”, fekete, piros', 'wheel_size' => '27', 'gears' => '9', 'sex' => 'férfi', 'type' => 'MTB', 'size' => 'L', 'color' => 'fekete, piros', 'manufacturer_id' => '1'],
            ['name' => 'MTB kerékpár ST 530 S, 27,5”, fekete, piros', 'wheel_size' => '27', 'gears' => '9', 'sex' => 'férfi', 'type' => 'MTB', 'size' => 'XL', 'color' => 'fekete, piros', 'manufacturer_id' => '1'],
            ['name' => 'MTB kerékpár ST 50, 26”, fekete', 'wheel_size' => '26', 'gears' => '21', 'sex' => 'férfi', 'type' => 'MTB', 'size' => 'S', 'color' => 'fekete', 'manufacturer_id' => '1'],
            ['name' => 'MTB kerékpár ST 50, 26”, fekete', 'wheel_size' => '26', 'gears' => '21', 'sex' => 'férfi', 'type' => 'MTB', 'size' => 'M', 'color' => 'fekete', 'manufacturer_id' => '1'],
            ['name' => 'MTB kerékpár ST 50, 26”, fekete', 'wheel_size' => '26', 'gears' => '21', 'sex' => 'férfi', 'type' => 'MTB', 'size' => 'L', 'color' => 'fekete', 'manufacturer_id' => '1'],
            ['name' => 'Női MTB kerékpár ST 120, 27,5”, SÖTÉTKÉK', 'wheel_size' => '27.5', 'gears' => '9', 'sex' => 'női', 'type' => 'MTB', 'size' => 'S', 'color' => 'Tengerészkék', 'manufacturer_id' => '1'],
            ['name' => 'Női MTB kerékpár ST 120, 27,5”, SÖTÉTKÉK', 'wheel_size' => '27.5', 'gears' => '9', 'sex' => 'női', 'type' => 'MTB', 'size' => 'M', 'color' => 'Tengerészkék', 'manufacturer_id' => '1'],
            ['name' => 'Női MTB kerékpár ST 120, 27,5”, SÖTÉTKÉK', 'wheel_size' => '27.5', 'gears' => '9', 'sex' => 'női', 'type' => 'MTB', 'size' => 'L', 'color' => 'Tengerészkék', 'manufacturer_id' => '1'],
            ['name' => 'Városi kerékpár Elops 520, alacsony vázas, kék', 'wheel_size' => '26', 'gears' => '6', 'sex' => 'női', 'type' => 'városi', 'size' => 'XS', 'color' => 'Tengerészkék', 'manufacturer_id' => '2'],
            ['name' => 'Városi kerékpár Elops 520, alacsony vázas, kék', 'wheel_size' => '28', 'gears' => '6', 'sex' => 'női', 'type' => 'városi', 'size' => 'S', 'color' => 'Tengerészkék', 'manufacturer_id' => '2'],
            ['name' => 'Városi kerékpár Elops 520, alacsony vázas, kék', 'wheel_size' => '28', 'gears' => '6', 'sex' => 'női', 'type' => 'városi', 'size' => 'L', 'color' => 'Tengerészkék', 'manufacturer_id' => '2'],
            ['name' => 'Városi kerékpár Elops 100 alacsony vázas, fekete', 'wheel_size' => '28', 'gears' => '1', 'sex' => 'női', 'type' => 'városi', 'size' => NULL, 'color' => 'fekete', 'manufacturer_id' => '2'],
            ['name' => 'Női országúti kerékpár Triban Regular, egyenes kormánnyal, kékeszöld', 'wheel_size' => '28', 'gears' => '18', 'sex' => 'női', 'type' => 'országúti', 'size' => 'XS', 'color' => 'Zöldeskék', 'manufacturer_id' => '3'],
            ['name' => 'Női országúti kerékpár Triban Regular, egyenes kormánnyal, kékeszöld', 'wheel_size' => '28', 'gears' => '18', 'sex' => 'női', 'type' => 'országúti', 'size' => 'S', 'color' => 'Zöldeskék', 'manufacturer_id' => '3'],
            ['name' => 'Női országúti kerékpár Triban Regular, egyenes kormánnyal, kékeszöld', 'wheel_size' => '28', 'gears' => '18', 'sex' => 'női', 'type' => 'országúti', 'size' => 'M', 'color' => 'Zöldeskék', 'manufacturer_id' => '3'],
            ['name' => 'VÁROSI KERÉKPÁR KRUZ DIABLO - 7 SEBESSÉGES - FEKETE', 'wheel_size' => '28', 'gears' => '7', 'sex' => 'férfi', 'type' => 'városi', 'size' => '54', 'color' => 'fekete', 'manufacturer_id' => '4'],
            ['name' => 'VÁROSI KERÉKPÁR KRUZ DIABLO - 7 SEBESSÉGES - FEKETE', 'wheel_size' => '28', 'gears' => '7', 'sex' => 'férfi', 'type' => 'városi', 'size' => '57', 'color' => 'fekete', 'manufacturer_id' => '4'],
            ['name' => 'Városi kerékpár Elops 120 férfi vázas, kék', 'wheel_size' => '28', 'gears' => '6', 'sex' => 'férfi', 'type' => 'városi', 'size' => NULL, 'color' => 'Szürkéskék', 'manufacturer_id' => '2'],
            ['name' => 'Cyclocross kerékpár RCX Shimano GRX 1X11', 'wheel_size' => '28', 'gears' => '11', 'sex' => 'férfi', 'type' => 'országúti', 'size' => 'S', 'color' => 'kék', 'manufacturer_id' => '5'],
            ['name' => 'Cyclocross kerékpár RCX Shimano GRX 1X11', 'wheel_size' => '28', 'gears' => '11', 'sex' => 'férfi', 'type' => 'országúti', 'size' => 'M', 'color' => 'kék', 'manufacturer_id' => '5'],
            ['name' => 'Cyclocross kerékpár RCX Shimano GRX 1X11', 'wheel_size' => '28', 'gears' => '11', 'sex' => 'férfi', 'type' => 'országúti', 'size' => 'L', 'color' => 'kék', 'manufacturer_id' => '5'],
            ['name' => 'CROSS PRO 28', 'wheel_size' => '28', 'gears' => '30', 'sex' => 'férfi', 'type' => 'cross', 'size' => '18', 'color' => 'fekete', 'manufacturer_id' => '6'],
            ['name' => 'CROSS PRO 28', 'wheel_size' => '28', 'gears' => '30', 'sex' => 'férfi', 'type' => 'cross', 'size' => '20', 'color' => 'fekete', 'manufacturer_id' => '6'],
        ]);
    }
}
