<?php

use Illuminate\Database\Seeder;
use Illuminate\Model;
use App\Role;
use App\User;

class UsersRolesPivotTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // Populate the pivot table
        DB::table('role_user')->truncate();

        $roles = \App\Role::all();
        App\User::where('isMember','=',1)->each(function ($user) use ($roles) { 
            $user->roles()->attach(
                $roles->random(rand(1, 2))->pluck('id')->toArray()
            ); 
        });         
    }
}
