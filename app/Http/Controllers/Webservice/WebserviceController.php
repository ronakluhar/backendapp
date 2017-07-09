<?php

namespace App\Http\Controllers\Webservice;

use File;
use Image;
use Input;
use Auth;
use DB;
use Config;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\UserDeviceToken;
use App\Notification;

class WebserviceController extends Controller {

    public function __construct() {
        $this->servicesBeforeLogin = array("checkUniqueId");
    }

    public function index(Request $request) {
        $methodName = $request->input('methodName');
        $body = $request->all();
        $userId = '';
        //$secret_key = (isset($body['secret_key'])) ? $body['secret_key'] : '';
        //$accessMethodNameArray = ["getCountryList"];
        //$objTeenagerLoginToken = new TeenagerLoginToken();
//        if (!in_array($methodName, $this->servicesBeforeLogin)) {
//            if (isset($body['userid']) && $body['userid'] > 0 && isset($body['loginToken']) && $body['loginToken'] != '') {
//                if (!$this->TeenagersRepository->checkActiveTeenager($body['userid'])) {
//                    $response['status'] = 0;
//                    $response['message'] = trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg');
//                }
//                $userId = ((isset($body['methodName']) && $body['methodName'] == 'getTeenagerProfileData')?$body['isLoggedInUser']:$body['userid']);
//                if (!$objTeenagerLoginToken->validateAccessToken($userId, $body['loginToken'])) {
//                    $response['status'] = 3;
//                    $response['message'] = trans('appmessages.invalid_access');
//                }
//            } else {
//                $response['status'] = 0;
//                $response['message'] = trans('appmessages.missing_data_msg');
//            }
//            if (isset($response)) {
//                echo json_encode($response, JSON_UNESCAPED_SLASHES);
//                exit;
//            }
//        }
        $this->$methodName($body);
    }

    
    public function checkUniqueId($body) {
        $response = [];
        $response['status'] = 0;
        $response['message'] = trans('appmessages.default_error_msg');
        if(isset($body['unique_id']) && $body['unique_id'] != '')
        {
            //check valid unique id 
            $userData = User::where('unique_id',$body['unique_id'])->first();
            if(isset($userData) && !empty($userData)){
                
               //check if device token already exist 
               $deviceToken = UserDeviceToken::where('user_id',$userData->id)->where('device_token',$body['device_token'])->first(); 
               $tokenData = array();
               $tokenData['user_id'] = $userData->id;
               $tokenData['device_token'] = $body['device_token'];
               $tokenData['device_type'] = $body['device_type'];
               $tokenData['device_id'] = $body['device_id'];
               if(count($deviceToken) > 0)
               {
                   UserDeviceToken::where('device_token',$body['device_token'])->where('user_id',$userData->id)->update($tokenData);
               }else{
                   UserDeviceToken::create($tokenData);
               }                                              
               $response['status'] = 1;
               $response['message'] = "Success";     
               $response['data'] = $userData;
                
            }else{
                $response['message'] = "Invalid unique Id";
            }
            
        }else{
            $response['message'] = trans('appmessages.invalid_userid_msg');
        }
        echo json_encode($response);
    }
    
    public function getUserNotification($body) {
        $response = [];
        $response['status'] = 0;
        $response['message'] = trans('appmessages.default_error_msg');
        $notificationData = array();
        if(isset($body['user_id']) && $body['user_id'] != '')
        {
            //check valid unique id 
            $userData = Notification::where('user_id',$body['user_id'])->first();
            if(isset($userData) && !empty($userData)){                
               //check if device token already exist                
               $notifications = Notification::where('user_id',$body['user_id'])->orderBy('created_at', 'desc')->take($body['limit'])->get();
               if(isset($notifications) && !empty($notifications))
               {
                  $notificationData = $notifications->toArray(); 
               }                
               $response['status'] = 1;
               $response['message'] = "Success";     
               $response['data'] = $notificationData;
                
            }else{
                $response['message'] = "Invalid user Id";
            }
            
        }else{
            $response['message'] = trans('appmessages.invalid_userid_msg');
        }
        echo json_encode($response);
    }        
}