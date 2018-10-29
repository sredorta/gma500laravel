<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notification;
use JWTAuth;

class NotificationController extends Controller
{

    //Checks if we are currently logged in and returns user if we are and null if not
    public function isLogged(Request $request) {
        if ($request->bearerToken()=== null) {
            return false;
        }
        //We need to remove the catch and redirect to loggin in production
        //try {
        JWTAuth::setToken($request->bearerToken()) ;
        $result = JWTAuth::toUser();
        //} catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        //    $result = null;
        //}
        return $result;
    }



   //Delete the notification
    public function delete($id)
    {
        Notification::find($id)->delete();
        return response()->json(null, 204);
    }
   //Delete the notification
   public function markAsRead($id)
   {
       $notification = Notification::find($id);
       $notification->isRead = true;
       $notification->save();
       //echo 'done';
       //$notification->save();
       return response()->json(null, 204);
   }    

   //Delete the notification
   public function getAll(Request $request)
   {
       //We need to get current User
       $user = $this->isLogged($request);
       $notifications = Notification::where('profile_id', $user->profile_id)->orderBy('created_at','DESC')->get();
       //echo 'done';
       //$notification->save();
       return response()->json($notifications, 200);
   }      
}
