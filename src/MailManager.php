<?php

namespace MorenoRafael\LaravelMail;

use Illuminate\Mail\MailManager as BaseMailManager;
use MorenoRafael\LaravelMail\Transports\SendGridTransport;
use MorenoRafael\LaravelMail\Transports\SendinblueTransport;
use SendGrid;
use SendGrid\Mail\Mail;

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

        $email = new Mail();
        $sendgrid = new SendGrid($config['key']);

        return new SendGridTransport($email, $sendgrid);
    }

    /**
     * Create an instance of the sendinblue Swift Transport driver.
     *
     * @param  array  $config
     * @return SendinblueTransport
     */
    protected function createSendinblueTransport(array $config)
    {
        if (! isset($config['key'])) {
            $config = $this->app['config']->get('services.sendinblue', []);
        }

        return new SendinblueTransport($config['key'], $config['url']);
    }
}
