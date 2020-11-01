<?php

namespace MorenoRafael\LaravelMail\Transports;

use Illuminate\Mail\Transport\Transport;
use Illuminate\Support\Facades\Http;
use Swift_Mime_SimpleMessage;

class SendGridTransport extends Transport
{
    /**
     * The SendGrid API key.
     *
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $url;

    public function __construct(string $key, string $url)
    {
        $this->key = $key;
        $this->url = $url;
    }

    /**
     * Send the given Message.
     *
     * Recipient/sender data will be retrieved from the Message API.
     * The return value is the number of recipients who were accepted for delivery.
     *
     * This is the responsibility of the send method to start the transport if needed.
     *
     * @param string[] $failedRecipients An array of failures by-reference
     *
     * @return int
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $to = $this->getTo($message);

        Http::withToken($this->key)->post($this->url, $this->payload($message, $to));

        return $this->numberOfRecipients($message);
    }

    /**
     * Get the HTTP payload for sending the SendGrid message.
     *
     * @param  \Swift_Mime_SimpleMessage $message
     * @param  array $to
     * @return array
     */
    protected function payload(Swift_Mime_SimpleMessage $message, array $to)
    {
        return [
            'personalizations' => [
                [
                    'to' => [$to],
                    'subject' => $message->getSubject(),
                ],
            ],
            'content' => [
                [
                    'type' => 'text/html',
                    'value' => $message->toString(),
                ],
            ],
            'from' => [
                'email' => config('mail.from.address'),
                'name' => config('mail.from.name'),
            ],
            'reply_to' => [
                'email' => config('mail.from.address'),
                'name' => config('mail.from.name'),
            ],
        ];
    }

    /**
     * Get the "to" payload field for the API request.
     *
     * @param  \Swift_Mime_SimpleMessage  $message
     * @return array
     */
    protected function getTo(Swift_Mime_SimpleMessage $message)
    {
        return collect($message->getTo())->map(function ($display, $address) {
            return ['email' => $address, 'name' => $display];
        })->first();
    }

    /**
     * Get the API key being used by the transport.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the API key being used by the transport.
     *
     * @param string $key
     * @return string
     */
    public function setKey(string $key)
    {
        return $this->key = $key;
    }

    /**
     * Get the domain being used by the transport.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the domain being used by the transport.
     *
     * @param string $url
     * @return string
     */
    public function setUrl(string $url)
    {
        return $this->url = $url;
    }
}