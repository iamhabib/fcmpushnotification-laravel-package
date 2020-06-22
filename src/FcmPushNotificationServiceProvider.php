<?php

namespace FcmPushNotification\FcmPushNotification;

use Illuminate\Support\ServiceProvider;

class FcmPushNotificationServiceProvider extends ServiceProvider
{
    /**
     * Publishes configuration file.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/fcm_push_notification.php' => config_path('fcm_push_notification.php'),
            ], 'fcm-push-notification-config');
        }
    }

    /**
     * Make config publishment optional by merge the config from the package.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/fcm_push_notification.php',
            'fcm_push_notification'
        );
    }
}