<?php

namespace NotificationChannels\Facebook;

use Illuminate\Support\ServiceProvider;

/**
 * Class FacebookServiceProvider.
 */
class FacebookServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->when(FacebookChannel::class)
            ->needs(Facebook::class)
            ->give(static function () {
                $facebook = new Facebook(config('services.facebook.page-token'));

                return $facebook
                    ->setGraphApiVersion(config('services.facebook.version', '4.0'))
                    ->setSecret(config('services.facebook.app-secret'));
            });
    }
}
