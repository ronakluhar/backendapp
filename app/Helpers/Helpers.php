<?php

namespace App\Helpers;

use DB;
use Config;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

Class Helpers {
    
    public static function get_shorten_url($longUrl) {
        $response = $longUrl;

        // Get API key from : http://code.google.com/apis/console/
        $apiKey = 'AIzaSyDKMWbyLwqowXwHHWiqqXhd5sl3yNBnIMw';

        $postData = array('longUrl' => $longUrl);
        $jsonData = json_encode($postData);

        $curlObj = curl_init();

        curl_setopt($curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url?key=' . $apiKey);
        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlObj, CURLOPT_HEADER, 0);
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
        curl_setopt($curlObj, CURLOPT_POST, 1);
        curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);

        $result = curl_exec($curlObj);
        $json = json_decode($result);

        if (empty($json->error)) {
            $responseUrl = $json->id;
            $response = (preg_match("@^https?://@", $responseUrl) == true) ? preg_replace('/^https(?=:\/\/)/i', 'http', $responseUrl) : $responseUrl;
        }
        return $response;
    }
    
    
    public static function pushNotificationForiPhone($token,$message) {
        $pathForCertificate = public_path(Config::get('constant.CERTIFICATE_PATH'));
        $payload['aps'] = array('alert' => $message['message'],'action-loc-key' => 'View', 'data' => $message);

        $payload['aps']['badge'] = 1;
        $payload['aps']['loc-args'] = '123';

        $payload = json_encode($payload);
        $deviceToken = $token;  // iPhone 6+

        $apnsHost = 'gateway.push.apple.com';
        $apnsPort = 2195;
        $apnsCert = $pathForCertificate;

        $streamContext = stream_context_create();
        stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);

        $apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $streamContext);

        $apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $deviceToken)) . chr(0) . chr(strlen($payload)) . $payload;

        fwrite($apns, $apnsMessage);

        fclose($apns);
    }
    
    public static function pushNotificationForAndroid($token,$message) {
        $optionBuiler = new OptionsBuilder();
        $optionBuiler->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder('ProTeen');
        $notificationBuilder->setBody($message['message'])
                            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($message);

        $option = $optionBuiler->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $downstreamResponse = FCM::sendTo($token, $option, NULL, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

        $downstreamResponse->tokensToDelete();

        $downstreamResponse->tokensToModify();

        $downstreamResponse->tokensToRetry();
    }
    
}