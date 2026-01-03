<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("cities")->insert([
            ['id' => '1','country_code' => 'US','name' => 'New York City'],
            ['id' => '2','country_code' => 'US','name' => 'Chicago'],
            ['id' => '3','country_code' => 'CN','name' => 'Hong Kong'],
            ['id' => '4','country_code' => 'CN','name' => 'Guangzhou'],
            ['id' => '5','country_code' => 'CN','name' => 'Shenzhen'],
            ['id' => '6','country_code' => 'CN','name' => 'Kaohsiung'],
            ['id' => '7','country_code' => 'MY','name' => 'Kuala Lumpur'],
            ['id' => '8','country_code' => 'CN','name' => 'Shanghai'],
            ['id' => '9','country_code' => 'AE','name' => 'Dubai'],
            ['id' => '10','country_code' => 'CN','name' => 'Taipei'],
            ['id' => '11','country_code' => 'AU','name' => 'Gold Coast'],
            ['id' => '12','country_code' => 'CN','name' => 'Wuhan'],
            ['id' => '13','country_code' => 'CN','name' => 'Nanjing'],
            ['id' => '14','country_code' => 'CN','name' => 'Beijing'],
            ['id' => '15','country_code' => 'KW','name' => 'Kuwait City'],
            ['id' => '16','country_code' => 'CN','name' => 'Tianjin'],
            ['id' => '17','country_code' => 'CN','name' => 'Wuxi'],
            ['id' => '18','country_code' => 'CN','name' => 'Wenzhou'],
            ['id' => '19','country_code' => 'SA','name' => 'Mecca'],
            ['id' => '20','country_code' => 'VN','name' => 'Hanoi'],
            ['id' => '21','country_code' => 'RU','name' => 'Moscow'],
            ['id' => '22','country_code' => 'AE','name' => 'Abu Dhabi'],
            ['id' => '23','country_code' => 'CN','name' => 'Shenyang'],
            ['id' => '24','country_code' => 'CN','name' => 'Chongqing'],
            ['id' => '25','country_code' => 'CN','name' => 'Dalian'],
            ['id' => '26','country_code' => 'CN','name' => 'Kunming'],
            ['id' => '27','country_code' => 'KR','name' => 'Seoul'],
            ['id' => '28','country_code' => 'US','name' => 'Los Angeles'],
            ['id' => '29','country_code' => 'CN','name' => 'Zhuhai'],
            ['id' => '30','country_code' => 'CN','name' => 'Yantai'],
            ['id' => '31','country_code' => 'CN','name' => 'Nanning'],
            ['id' => '32','country_code' => 'VN','name' => 'Ho Chi Minh City'],
            ['id' => '33','country_code' => 'CN','name' => 'Changsha'],
            ['id' => '34','country_code' => 'US','name' => 'Philadelphia'],
            ['id' => '35','country_code' => 'CN','name' => 'Zhenjiang'],
            ['id' => '36','country_code' => 'US','name' => 'San Francisco'],
            ['id' => '37','country_code' => 'HU','name' => 'Budapest'],
        ]);
    }
}
