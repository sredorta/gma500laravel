<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\User;
use App\Profile;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        // Let's truncate our existing records to start from scratch.
        User::truncate();

        //We first find all profiles and create one user with 'standard' access
        $count = Profile::all()->count();
        for ($i = 1; $i <= $count; $i++) {
            $profile = Profile::find($i);
            $account = User::create([
                'profile_id' => $i,
                'email' => $profile->email,
                'password' => Hash::make('Secure0', ['rounds' => 12]),
                'access' => 'Pré-inscrit'
            ]);
        }
        //Switch all Profiles having a "member" role to 'member' access
        $profiles = Profile::whereHas(
            'roles', function($q){
                $q->where('name', 'Membre');
            }
        )->get();
        foreach ($profiles as $profile) {
            $user = User::where('profile_id', $profile->id)->get()->first();
            $user->access = "Membre";
            $user->password = Hash::make('Member0', ['rounds' => 12]);
            $user->save();        
        }
        //Add admin access to some User member
        $count = 0;
        foreach ($profiles as $profile) {
            if ($count < 5) {
                $user = User::where('profile_id', $profile->id)->get()->first();
                User::create([
                    'profile_id' => $user->profile_id,
                    'email' => $user->email,
                    'password' => Hash::make('Admin0', ['rounds' => 12]),
                    'access' => 'Admin'
                ]);
                $count++;        
            }   
        }       


    }
}
