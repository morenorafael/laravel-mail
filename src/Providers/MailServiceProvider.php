<?php

namespace MorenoRafael\LaravelMail\Providers;

use Illuminate\Mail\MailServiceProvider as BaseMailServiceProvider;
use MorenoRafael\LaravelMail\MailManager;

class MailServiceProvider extends BaseMailServiceProvider
{
    /**
     * Register the Illuminate mailer instance.
     *
     * @return void
     */
    protected function registerIlluminateMailer()
    {
        $this->app->singleton('mail.manager', function ($app) {
            return new MailManager($app);
        });

        $this->app->bind('mailer', function ($app) {
            return $app->make('mail.manager')->mailer();
        });
    }
}
