<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notification;
use Validator;

class NotificationController extends Controller
{

   //Delete the notification
    public function delete(Request $request)
    {
        $id = $request->id;
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),400);
        }          

        Notification::where('profile_id', $request->get('myProfile'))->find($id)->delete();
        return response()->json(null, 204);
    }

   //Delete the notification
   public function markAsRead(Request $request)
   {
        $id = $request->id;
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(),400);
        }   

       $notification = Notification::where('profile_id', $request->get('myProfile'))->find($id);
       $notification->isRead = true;
       $notification->save();
       return response()->json(null, 204);
   }    

   //Delete the notification
   public function getAll(Request $request)
   {
       //We need to get current User
       $notifications = Notification::where('profile_id', $request->get('myProfile'))->orderBy('created_at','DESC')->get();
       return response()->json($notifications, 200);
   }      
}
