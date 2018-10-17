<?php

use Illuminate\Database\Seeder;
use App\ConfigProductCathegory;

class ConfigProductCathegorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Let's truncate our existing records to start from scratch.
        ConfigProductCathegory::truncate();

        ConfigProductCathegory::create(['name'=> 'Sécurité']);
        ConfigProductCathegory::create(['name'=> 'Escalade']);
        ConfigProductCathegory::create(['name'=> 'Ski']);
        ConfigProductCathegory::create(['name'=> 'Randonée']);
        ConfigProductCathegory::create(['name'=> 'Canyoning']);
        ConfigProductCathegory::create(['name'=> 'VTT']);
    }
}
