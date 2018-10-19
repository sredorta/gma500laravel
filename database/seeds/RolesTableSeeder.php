<?php

use Illuminate\Database\Seeder;
use App\Role;
class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
     // Let's truncate our existing records to start from scratch.
     Role::truncate();

     // And now, let's create a few articles in our database:
     /*    Role::create([
             'name' => 'default',
             'description' => 'Compte utilizateur non inscrit au club',
         ]);*/
         Role::create([
            'name' => 'member',
            'description' => 'Compte utilizateur inscrit au club',
        ]);     
        Role::create([
            'name' => 'admin',
            'description' => 'Compte administrateur',
        ]);                
    }
}
