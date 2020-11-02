<?php

namespace MorenoRafael\LaravelMail;

use GuzzleHttp\Client;
use Illuminate\Mail\MailManager as BaseMailManager;
use MorenoRafael\LaravelMail\Transports\SendGridTransport;
use MorenoRafael\LaravelMail\Transports\SendinblueTransport;
use MorenoRafael\LaravelMail\Transports\SendPulseTransport;
use SendGrid;
use SendGrid\Mail\Mail;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendSmtpEmail;
use Sendpulse\RestApi\ApiClient;
use Sendpulse\RestApi\Storage\FileStorage;

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

        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $config['key']);
        $transactionalEmailsApi = new TransactionalEmailsApi(new Client(), $config);
        $sendSmtpEmail = new SendSmtpEmail();

        return new SendinblueTransport($transactionalEmailsApi, $sendSmtpEmail);
    }

    /**
     * Create an instance of the SendPulse Swift Transport driver.
     *
     * @param  array  $config
     * @return SendPulseTransport
     */
    protected function createSendPulseTransport(array $config)
    {
        if (! isset($config['secret'])) {
            $config = $this->app['config']->get('services.sendpulse', []);
        }

        $spApiClient = new ApiClient($config['user_id'], $config['secret'], new FileStorage());

        return new SendPulseTransport($spApiClient);
    }
}
