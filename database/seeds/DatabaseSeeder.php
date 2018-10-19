<?php

use Illuminate\Database\Seeder;
//use App\ConfigProductCathegorySeeder;
//use App\ConfigProductTypeSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call([
                    UsersTableSeeder::class,
                    ConfigProductCathegorySeeder::class,
                    ConfigProductTypeSeeder::class]);
    }
}
