<?php

namespace MorenoRafael\LaravelMail\Transports;

use Illuminate\Mail\Transport\Transport;
use Sendpulse\RestApi\ApiClient;
use Swift_Mime_SimpleMessage;

class SendPulseTransport extends Transport
{
    /**
     * @var ApiClient
     */
    protected $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
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

        $this->apiClient->smtpSendMail($this->setPayload($message, $to));
    }

    /**
     * @param Swift_Mime_SimpleMessage $message
     * @param array $to
     * @return array
     */
    protected function setPayload(Swift_Mime_SimpleMessage $message, array $to)
    {
        $email = [
            'html' => $message->getBody(),
            'text' => $message->toString(),
            'subject' => $message->getSubject(),
            'from' => [
                'email' => config('mail.from.address'),
                'name' => config('mail.from.name')
            ],
            'to' => [$to],
        ];

        if (count($message->getChildren()) > 0) {

            $email['attachments'] = [];

            foreach ($message->getChildren() as $child) {
                $email['attachments'] = [$child->getFilename() => $child->getBody()];
            }
        }

        return $email;
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
}
