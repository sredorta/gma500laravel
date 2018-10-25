<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Profile;

class ProfilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $faker = \Faker\Factory::create();
        // Let's truncate our existing records to start from scratch.
        Profile::truncate();      
        DB::table('profile_role')->truncate();

        $avatars[0] = 'url(data:image/png;base64,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)';
        $avatars[1] = 'url(data:image/png;base64,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)';
        $avatars[2] = 'url(data:image/png;base64,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)';
        $avatars[3] = 'url(data:image/png;base64,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)';
        $avatars[4] = 'url(data:image/png;base64,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)';
        $avatars[5] = 'url(data:image/png;base64,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)';
        $avatars[6] = 'url(data:image/png;base64,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)';
        $avatars[7] = 'url(data:image/png;base64,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)';
        $avatars[8] = 'url(data:image/png;base64,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)';
        $avatars[9] = 'url(data:image/png;base64,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)';

        $usersCount = 50;
        $membersCount = 30;
        $bureauCount = 5;

        for ($i = 0; $i < $usersCount; $i++) {
            $phone = '06';
            for ($j = 0; $j<8; $j++) {
                $phone .= mt_rand(0,9);
            }
            $profile = Profile::create([
                'firstName' => $faker->firstName,
                'lastName' => $faker->lastName,
                'mobile' => $phone,
                'email' => 'sergi.redorta' . $i . '@kubiiks.com',
                'avatar' => $avatars[mt_rand(0,9)],
                'isEmailValidated' => 1,
                'emailValidationKey' => Str::random(50)
            ]);
        }
        //ROLE ASSIGNMENT
        //Assing now randomly 30 members
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
        $profile = Profile::find(rand(1,$usersCount));
        $profile->roles()->attach(3);
        $profile = Profile::find(mt_rand(1,$usersCount));
        $profile->roles()->attach(4);
        $profile = Profile::find(mt_rand(1,$usersCount));
        $profile->roles()->attach(5);      
        $profile = Profile::find(mt_rand(1,$usersCount));
        $profile->roles()->attach(6);                
    }
}