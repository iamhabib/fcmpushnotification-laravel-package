# FCM Push Notifications
Utility class to send push notifications using firebase FCM.

### Installation
**Be careful! This library needs curl module for php to work.**

```
composer require hrhabib/fcm-push-notification
```

Update firebase push key in config/fcm_push_notification.php

### Usage
Bind the PushNotification class in function

public function doSomething(PushNotification $pushNotification){
	// call function to send push
}

Available Function:

- sendToOne
- sendToTopic
- sendToAll
- sendMultiple


### License
FCM Push Notifications is licensed under the MIT license. See License File for more information.
