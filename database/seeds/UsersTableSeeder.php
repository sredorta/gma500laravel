<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        //User::truncate();

        $faker = \Faker\Factory::create();
  
        $user = User::create([
            'firstName' => "Sergi",
            'name' => "Redorta",
            'email' => 'sergi.redorta@hotmail.com', 
            'mobile' => '0623133212',
            'isValidated' => true,
            'password'=>'Secure0'           
        ]);
        $role = new Role();
        $role->role = "Bureau";
        $user->roles()->save($role);
        $role = new Role();  
        $role->role = "Membre";
        $user->roles()->save($role);

        $user = User::create([
            'firstName' => "Pierre",
            'name' => "Durin",
            'email' => 'pierre.durin@hotmail.com', 
            'mobile' => '0611223344',
            'isValidated' => true,
            'password'=>'Secure0'           
        ]);
        $role = new Role();
        $role->role = "President";
        $user->roles()->save($role);  
        $role = new Role();    
        $role->role = "Bureau";
        $user->roles()->save($role);   
        $role = new Role();           
        $role->role = "Membre";
        $user->roles()->save($role);   

        $user = User::create([
            'firstName' => "Ben",
            'name' => "Vignot",
            'email' => 'ben.vignot@hotmail.com', 
            'mobile' => '0611223355',
            'isValidated' => true,
            'password'=>'Secure0'           
        ]);
        $role = new Role();
        $role->role = "Trésorier";      
        $user->roles()->save($role);  
        $role = new Role();        
        $role->role = "Bureau";
        $user->roles()->save($role); 
        $role = new Role();  
        $role->role = "Membre";
        $user->roles()->save($role);   


        $user = User::create([
            'firstName' => "test",
            'name' => "NonMembre",
            'email' => 'test.nonmembre@hotmail.com', 
            'mobile' => '0611222222',
            'isValidated' => true,
            'password'=>'Secure0'           
        ]);
    }
}
