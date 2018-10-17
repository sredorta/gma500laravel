<?php

use Illuminate\Database\Seeder;
use App\ConfigProductType;

class ConfigProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Let's truncate our existing records to start from scratch.
        ConfigProductType::truncate();

        ConfigProductType::create(['name'=> 'Batons']);
        ConfigProductType::create(['name'=> 'Chaussures']);
        ConfigProductType::create(['name'=> 'Arva']);
        ConfigProductType::create(['name'=> 'Pelle']);
        ConfigProductType::create(['name'=> 'Sonde']);
        ConfigProductType::create(['name'=> 'Skis']);
        ConfigProductType::create(['name'=> 'Corde']);
    }
}