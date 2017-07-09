<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notification;
use Helpers;

class SendNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification to users about post';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //get user for notification
        $objNotification = new Notification();
        $users = $objNotification->getNotifications();
        
        foreach ($users AS $key => $value) {
            if ($value->device_token != '') {
                $token = $value->device_token;
                $data = [];
                $data['message'] = $value->notification_link;
                if ($value->device_type == 1) {
                    $return = Helpers::pushNotificationForiPhone($token,$data);
                } else if ($value->device_type == 2) {
                    $return = Helpers::pushNotificationForAndroid($token,$data);
                }
            }
            $result = $objNotification->updateNotificationStatusById($value->id);
        }
        $this->info('Push Notifications sent successfully!');
        
    }
}
