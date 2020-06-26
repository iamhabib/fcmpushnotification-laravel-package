<?php

namespace FcmPushNotification\FcmPushNotification;
use Log;

class PushNotification {

    private $push_key = '';
    private $ttl = 10;

    public function __construct($ttl = 10){
        $this->push_key = config('fcm_push_notification.fcm_push_app_key');
        $this->ttl = $ttl;
    }

    // sending push message to single user by firebase reg id
    public function sendToOne($to, $title, $message, $image = '', $background = false, $data = null) {
        try{
			$fields = array(
				'to' => $to,
                'data' => $this->getPushMessageJson($title, $message, $image, $background, $data),
                'time_to_live' => $this->ttl
			);
			return $this->sendPushNotification($fields);
		}catch(\Exception $ex){
			Log::error('Push Notification Issue at sendToOne: '.$ex->getMessage());
        }
        return false;
    }

    // Sending message to a topic by topic name
    public function sendToTopic($to, $title, $message, $image = '', $background = false, $data = null) {
        try{
			$fields = array(
				'to' => '/topics/' . $to,
                'data' => $this->getPushMessageJson($title, $message, $image, $background, $data),
                'time_to_live' => $this->ttl
			);
			return $this->sendPushNotification($fields);
		}catch(\Exception $ex){
			Log::error('Push Notification Issue at sendToTopic: '.$ex->getMessage());
        }
        return false;
    }

    // Sending message to a topic by topic global
    public function sendToAll($title, $message, $image = '', $background = false, $data = null) {
        try{
			$fields = array(
				'to' => '/topics/global',
                'data' => $this->getPushMessageJson($title, $message, $image, $background, $data),
                'time_to_live' => $this->ttl
			);
			return $this->sendPushNotification($fields);
		}catch(\Exception $ex){
			Log::error('Push Notification Issue at sendToAll: '.$ex->getMessage());
        }
        return false;
    }

    // sending push message to multiple users by firebase registration ids
    public function sendMultiple($registration_ids, $title, $message, $image = '', $background = false, $data = null) {
        try{
            $res = [];
			$collection = array_chunk($registration_ids, 1000);
			foreach ($collection as $chunk){
				$fields = array(
					'registration_ids' => $chunk,
                    'data' => $this->getPushMessageJson($title, $message, $image, $background, $data),
                    'time_to_live' => $this->ttl
				);
				$res[] = $this->sendPushNotification($fields);
			}
			return $res;
		}catch(\Exception $ex){
			Log::error('Push Notification Issue at sendMultiple: '.$ex->getMessage());
        }
        return false;
    }

    // function makes curl request to firebase servers
    private function sendPushNotification($fields) {

        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = array(
            'Authorization: key='.$this->push_key,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);

        return json_decode($result);
    }

    private function getPushMessageJson($title, $message, $image, $background, $data) {
        $res = array();
        $res['data']['title'] = $title;
        $res['data']['description'] = $message;
        $res['data']['image'] = $image;
        $res['data']['background'] = $background;
        $res['data']['payload'] = $data;
        $res['data']['timestamp'] = date('Y-m-d H:i:s');
        return $res;
    }

}