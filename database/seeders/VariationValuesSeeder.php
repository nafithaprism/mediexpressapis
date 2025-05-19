<?php

namespace Database\Seeders;

use App\Models\VariationValues;

use Illuminate\Database\Seeder;

class VariationValuesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VariationValues::factory()->times(10)->create();

    }

}
