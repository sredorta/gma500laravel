<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notification;

class NotificationController extends Controller
{
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
}
