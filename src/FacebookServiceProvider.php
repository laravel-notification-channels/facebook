<?php

namespace NotificationChannels\Facebook;

use Illuminate\Support\ServiceProvider;

class FacebookServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->when(FacebookChannel::class)
            ->needs(Facebook::class)
            ->give(function () {
                $pageToken = config('services.facebook.page-token');

                return new Facebook($pageToken);
            });
    }

    /**
     * Register any package services.
     */
    public function register()
    {
    }
}
