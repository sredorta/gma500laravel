<?php

use Illuminate\Database\Seeder;
use App\Notification;
use App\Profile;

class NotificationsTableSeeder extends Seeder
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
        Notification::truncate();
        $notif[0] = 'Bienvenu sur votre compte du GMA500';
        $notif[1] = 'Vous devez etre membre pour acceder aux autres functionalites';
        $notif[2] = 'Votre demande de membre a été accepter';
        $notif[3] = 'Vous avez reçu un nouveau message privé';
        $notif[4] = 'Votre inscription est accepté';
        $notif[5] = 'Votre inscription est refusé';
        $notif[6] = 'Vous avez un compte Admin disponible, verifier le mot de passe sur votre messagerie email';
        $notif[7] = 'Votre demande de compte Admin à été refusé';
        $notif[8] = 'Votre demande de group à été accpeté';
        
        $count = Profile::all()->count();
        for ($i = 1; $i <= $count; $i++) {
            $profile = Profile::find($i);

            $notification = Notification::create([
                'profile_id' => $i,
                'text' => $notif[mt_rand(0,8)],
                'isRead' => mt_rand(0,1)
            ]);
            $notification = Notification::create([
                'profile_id' => $i,
                'text' => $notif[mt_rand(0,8)],
                'isRead' => mt_rand(0,1)
            ]);
            $notification = Notification::create([
                'profile_id' => $i,
                'text' => $notif[mt_rand(0,8)],
                'isRead' => mt_rand(0,1)
            ]);
            $notification = Notification::create([
                'profile_id' => $i,
                'text' => $notif[mt_rand(0,8)],
                'isRead' => mt_rand(0,1)
            ]);
        }

    }
}
