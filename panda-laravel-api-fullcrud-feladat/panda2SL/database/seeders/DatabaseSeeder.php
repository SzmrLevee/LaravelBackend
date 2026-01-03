<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Panda;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Panda::truncate();
        $this->call(PandaSeeder::class);
    }
}
