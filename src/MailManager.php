<?php

namespace MorenoRafael\LaravelMail;

use Illuminate\Mail\MailManager as BaseMailManager;
use MorenoRafael\LaravelMail\Transports\SendGridTransport;

class MailManager extends BaseMailManager
{
    /**
     * Create an instance of the SendGrid Swift Transport driver.
     *
     * @param  array  $config
     * @return SendGridTransport
     */
    protected function createSendGridTransport(array $config)
    {
        if (! isset($config['key'])) {
            $config = $this->app['config']->get('services.sendgrid', []);
        }

        return new SendGridTransport($config['key'], $config['url']);
    }
}