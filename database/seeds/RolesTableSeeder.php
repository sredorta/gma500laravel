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
            'name' => 'Membre',
            'description' => 'Membre du club inscrit',
        ]);     
        Role::create([
            'name' => 'Bureau',
            'description' => 'Membre du bureau',
        ]);                
        Role::create([
            'name' => 'Président',
            'description' => 'Président du club',
        ]);   
        Role::create([
            'name' => 'VicePrésident',
            'description' => 'VicePrésident du club',
        ]);   
        Role::create([
            'name' => 'Sécretaire',
            'description' => 'Sécretaire du club',
        ]);                           
        Role::create([
            'name' => 'Trésorier',
            'description' => 'Trésorier du club',
        ]);            
    }
}
