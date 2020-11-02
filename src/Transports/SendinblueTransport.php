<?php

namespace MorenoRafael\LaravelMail\Transports;

use Illuminate\Mail\Transport\Transport;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\ApiException;
use SendinBlue\Client\Model\SendSmtpEmail;
use SendinBlue\Client\Model\SendSmtpEmailAttachment;
use SendinBlue\Client\Model\SendSmtpEmailSender;
use SendinBlue\Client\Model\SendSmtpEmailTo;
use Swift_Mime_SimpleMessage;

class SendinblueTransport extends Transport
{
    /**
     * @var TransactionalEmailsApi
     */
    protected $transactionalEmailsApi;
    /**
     * @var SendSmtpEmail
     */
    private $sendSmtpEmail;

    public function __construct(TransactionalEmailsApi $transactionalEmailsApi, SendSmtpEmail $sendSmtpEmail)
    {
        $this->transactionalEmailsApi = $transactionalEmailsApi;
        $this->sendSmtpEmail = $sendSmtpEmail;
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
        $this->setPayload($message, $to);

        $this->transactionalEmailsApi->sendTransacEmail($this->sendSmtpEmail);

        return $this->numberOfRecipients($message);
    }

    /**
     * Get the HTTP payload for sending the SendGrid message.
     *
     * @param  \Swift_Mime_SimpleMessage $message
     * @param  array $to
     * @return array
     */
    protected function setPayload(Swift_Mime_SimpleMessage $message, array $to)
    {
        $this->sendSmtpEmail->setSender(new SendSmtpEmailSender([
            'email' => config('mail.from.address'),
            'name' => config('mail.from.name')
        ]))
            ->setTo([new SendSmtpEmailTo($to)])
            ->setHtmlContent($message->getBody())
            ->setSubject($message->getSubject());

        if (count($message->getChildren()) > 0) {

            $file = [];

            foreach ($message->getChildren() as $child) {
                $file[] = new SendSmtpEmailAttachment([
                    'content' => base64_encode($child->getBody()),
                    'name' => $child->getFilename(),
                ]);
            }

            $this->sendSmtpEmail->setAttachment($file);
        }
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
