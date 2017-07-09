<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Media;
use Redirect;
use Illuminate\Validation\Rule;
use Image;
use Config;
use File;
use App\Notification;
use Helpers;
use App\User;


class MediaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->mediaOriginalImageUploadPath = Config::get('constant.MEDIA_ORIGINAL_IMAGE_UPLOAD_PATH');        
    }
    
    public function getMedia()
    {        
        $uploadMediaOriginalPath = $this->mediaOriginalImageUploadPath;
        $medias = Media::all();
        return view('admin.media-list',compact('medias','uploadMediaOriginalPath'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function addMedia()
    {        
        $uploadMediaOriginalPath = $this->mediaOriginalImageUploadPath;
        return view('admin.add-media',compact('uploadMediaOriginalPath'));
    }
    
    public function editMedia($id)
    {
        $media = Media::find($id);
        $uploadMediaOriginalPath = $this->mediaOriginalImageUploadPath;        
        return view('admin.add-media',compact('media','uploadMediaOriginalPath'));
    }
    
    
    public function saveMedia()
    {        
        $media = new Media(); 
        $postData = Input::all();   
        
        $hiddenProfile = Input::get('hidden_profile');
        $postData['media'] = $hiddenProfile;
        if (Input::file()) {
            $file = Input::file('media');
            
            if (!empty($file)) {                
                $fileName = 'media_' . time() . '.' . $file->getClientOriginalExtension();
                Input::file('media')->move($this->mediaOriginalImageUploadPath, $fileName);
                
                if ($hiddenProfile != '' && $hiddenProfile != "default.png") {
                    $imageOriginal = public_path($this->mediaOriginalImageUploadPath . $hiddenProfile);
                    if(file_exists($imageOriginal) && $hiddenProfile != ''){File::delete($imageOriginal);}
                }                    
                $postData['media'] = $fileName;                
            }
        }
        
//        function send_notification ($tokens, $message)
//{
//    $url = "https://fcm.googleapis.com/fcm/send";
//
////Title of the Notification.
//$title = "Message from PHP";
//
////Body of the Notification.
//$body = "Rupin Luhar";
//
////Creating the notification array.
//$notification = array('title' =>$title , 'body' => $body);
//
//
////The device token.
////$token = "";
////"dfzhunZckc8:APA91bHEgSqUiggcPOsD9AQPXP9hprE34SRFrizeFZ-2Xv_Jg-R0NuyYEkcMsL2FAKLUzOfKTZJOiGY8mhiMtu27xcGr5SUfeBBwh860xpy71ZZHLE5q1YZZXZeimhrcMBJ4gtdGoNqs";
//
////"e98YX8Fbm0M:APA91bEVzjH2rWs8iSFTF_W1jLpB_1Z9HtF950hMaMhe9Zt6WytXkX8nmXHZPXnjCsHhVQ8DI9erq6uIR8LMu9fBG213ZhHGLv7VN_pqrbjhHJoVefL2ALBNIoQZj7xcIZF-pwCcfYMt";
//
//
////This array contains, the token and the notification. The 'to' attribute stores the token.
//$arrayToSend = array('registration_ids'  => $tokens, 'notification' => $notification);
//    
//$fields = array(
//         'registration_ids' => $tokens,
//         'body' => 'hey'
//        );
//
//    $headers = array(
//        'Authorization:key = AIzaSyDiMG41zYaRaQQscM7t1egTj3Y54DTINSU',
//        'Content-Type: application/json'
//        );
//
//   $ch = curl_init();
//   curl_setopt($ch, CURLOPT_URL, $url);
//   curl_setopt($ch, CURLOPT_POST, true);
//   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//   curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);  
//   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayToSend));
//   $result = curl_exec($ch);           
//   if ($result === FALSE) {
//       die('Curl failed: ' . curl_error($ch));
//   }
//   curl_close($ch);
//   return $result;
//}
//
//
//$tokens = 
//array("e-3_uD-kLn8:APA91bET6tk8QPord1LvycmFrQWkij64fYnln5tW8Ntgvft-xhF-cTIwBI_wZEevc5Zs5SKnHILUsRI4ELHFty026Ce4KaH9eXPxCKSRPLhlar6P1y6pJNoVQayTiom1p9AbimxqVLuY");
//
//$message = array("message" => "FCM PUSH NOTIFICATION TEST MESSAGE");
//$message_status = send_notification($tokens, $message);
//echo $message_status;
//exit;

        
        if(isset($postData['id']) && $postData['id'] > 0)
        {
           $media = Media::find($postData['id']);
           $media->message = $postData['message'];          
           $media->save();
           return Redirect::to("/admin/media/")->with('success', 'Media has been updated successfully');
           exit;
        }
        else
        {           
           $media::create($postData);
           
           //Save Data in notification tables
           $mediaLink = asset($this->mediaOriginalImageUploadPath . $postData['media']);
           $shortURL =  Helpers::get_shorten_url($mediaLink);
           
           //Get all active users to save notification
           $users = User::where('is_admin',0)->get();
          
           if(isset($users) && !empty($users))
           {
               foreach($users as $key=>$val)
               {
                    $notificationData = array();
                    $notificationData['user_id'] = $val->id;
                    $notificationData['notification_text'] = $postData['message'];
                    $notificationData['notification_link'] = $shortURL;
                    Notification::create($notificationData);                                 
               }
           }
           
           return Redirect::to("/admin/media/")->with('success', 'Media has been created successfully');
           exit;
        }   
    }
    
    public function deleteMedia($id)
    {
        $media = Media::find($id);
        $media->delete();
        return Redirect::to("/admin/media/")->with('success', 'Media has been deleted successfully');
        exit;
    }
}
