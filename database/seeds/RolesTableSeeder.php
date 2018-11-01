<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\Profile;
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


        //ROLE ASSIGNMENT
        //////////////////////////////////////////////////////////////
            
        $membersCount = 50;
        $bureauCount = 5;
        $usersCount = Profile::all()->count();
        
        for ($i = 0; $i < $membersCount; $i++) {
            $id = mt_rand(1,$usersCount);
            $profile = Profile::find($id);
            $profile->roles()->detach(1);
            $profile->roles()->attach(1);
        }

        //Asign now randomly 5 bureau
        for ($i = 0; $i < $bureauCount; $i++) {
            $profile = Profile::find(mt_rand(1,$usersCount));
            $profile->roles()->attach(2);
        }

        //Asign now randomly the roles Président...        
        $profile = Profile::find(mt_rand(1,$usersCount));
        $profile->roles()->attach(3);
        $profile = Profile::find(mt_rand(1,$usersCount));
        $profile->roles()->attach(4);
        $profile = Profile::find(mt_rand(1,$usersCount));
        $profile->roles()->attach(5);      
        $profile = Profile::find(mt_rand(1,$usersCount));
        $profile->roles()->attach(6);                
    }
}
