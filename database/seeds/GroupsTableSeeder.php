<?php

use Illuminate\Database\Seeder;
use App\Group;
use App\Profile;
class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      // Let's truncate our existing records to start from scratch.
      Group::truncate();

      // And now, let's create a few articles in our database:
         Group::create([
             'name' => 'GMA500-ALL',
             'description' => 'Tous les membres du GMA500',
         ]);     
         Group::create([
             'name' => 'GMA500-SKI',
             'description' => 'Group technique de la section ski',
         ]);                
         Group::create([
             'name' => 'GMA500-ESCALADE',
             'description' => 'Group technique de la section escalade',
         ]);   
         Group::create([
             'name' => 'GMA500-CANYONING',
             'description' => 'Group tecnhique de la section canyoning',
         ]);   
         Group::create([
             'name' => 'GMA500-SOIREE-MONTAGNE',
             'description' => 'Organizateurs soiree montagne',
         ]);                           
         Group::create([
             'name' => 'GMA500-BUREAU',
             'description' => 'Membres du bureau',
         ]);   

        //GROUP ASSIGNMENT
        ///////////////////////////////////////////////////////////////
        $groupsCount = Group::all()->count();
        echo 'Groups count : ' . $groupsCount;
        $profiles = Profile::whereHas('users', function($q) { $q->where('access', 'Membre');})->get();
        echo 'Profiles count : ' . $profiles->count();
        $profiles->each(function($profile){
            $profile->groups()->attach(mt_rand(1,6));
            $profile->groups()->attach(mt_rand(1,6));
            $profile->groups()->attach(mt_rand(1,6));
        });                
    }
}
