<?php

namespace FcmPushNotification\FcmPushNotification;

class PushNotification {

    private $push_key = '';

    public function __construct(){
        $this->push_key = config('fcm_push_notification.fcm_push_app_key');
    }

    // sending push message to single user by firebase reg id
    public function sendToOne($to, $title, $message, $image = '', $background = false, $data = null) {
        try{
			$fields = array(
				'to' => $to,
				'data' => $this->getPushMessageJson($title, $message, $image, $background, $data),
			);
			return $this->sendPushNotification($fields);
		}catch(\Exception $ex){
			return $ex->getMessage();
		}
    }

    // Sending message to a topic by topic name
    public function sendToTopic($to, $title, $message, $image = '', $background = false, $data = null) {
        try{
			$fields = array(
				'to' => '/topics/' . $to,
				'data' => $this->getPushMessageJson($title, $message, $image, $background, $data),
			);
			return $this->sendPushNotification($fields);
		}catch(\Exception $ex){
			return $ex->getMessage();
		}
    }

    // Sending message to a topic by topic global
    public function sendToAll($title, $message, $image = '', $background = false, $data = null) {
        try{
			$fields = array(
				'to' => '/topics/global',
				'data' => $this->getPushMessageJson($title, $message, $image, $background, $data),
			);
			return $this->sendPushNotification($fields);
		}catch(\Exception $ex){
			return $ex->getMessage();
		}
    }

    // sending push message to multiple users by firebase registration ids
    public function sendMultiple($registration_ids, $title, $message, $image = '', $background = false, $data = null) {
        try{
			$collection = array_chunk($registration_ids, 1000);
			foreach ($collection as $chunk){
				$fields = array(
					'registration_ids' => $chunk,
					'data' => $this->getPushMessageJson($title, $message, $image, $background, $data),
				);
				$this->sendPushNotification($fields);
			}
			return 'done';
		}catch(\Exception $ex){
			return $ex->getMessage();
		}
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

        return $result;
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