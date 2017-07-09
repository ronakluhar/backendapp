<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use DB;

class Notification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'notification_text', 'notification_link', 'status'
    ];   
    
    
    public function getNotifications() {
        $users = DB::table("notifications AS notification")
                    ->join("user_device_tokens AS token", 'token.user_id', '=', 'notification.user_id')
                    ->join("users AS user", 'user.id', '=', 'notification.user_id')
                    ->selectRaw('token.device_token,token.device_type,notification.notification_text,notification.notification_link,notification.user_id,notification.id')
                    ->where('notification.status' , '=', 0)
                    ->where('user.is_admin' , '=', 0)
                    ->take(50)
                    ->get();
        return $users;
    }
    
    public function updateNotificationStatusById($id) {
        $result = DB::table('notifications')->where('id', $id)->update(['status'=>1]);
        return $result;
    }
}
