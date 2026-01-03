<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Panda; 

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Panda::truncate();
        $this->call(PandaSeeder::class);
    }
}
