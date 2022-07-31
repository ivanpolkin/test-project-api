<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Seeder;

class FakeSeeder extends Seeder
{
    const COUNT = 10;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Movie::factory()
            ->count(self::COUNT)
            ->hasActors(3)
            ->create();
    }
}
