<?php

use Illuminate\Database\Seeder;
//use App\ConfigProductCathegorySeeder;
//use App\ConfigProductTypeSeeder;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		Eloquent::unguard();

		//disable foreign key check for this connection before running seeders
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');

         $this->call([
                    RolesTableSeeder::class,
                    ProfilesTableSeeder::class,            
                    UsersTableSeeder::class,
                    NotificationsTableSeeder::class,
                    ConfigProductCathegorySeeder::class,
                    ConfigProductTypeSeeder::class
                    ]);
		// supposed to only apply to a single connection and reset it's self
		// but I like to explicitly undo what I've done for clarity
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');               
    }
}
