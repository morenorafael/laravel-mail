<?php

namespace MorenoRafael\LaravelMail\Transports;

use Illuminate\Mail\Transport\Transport;
use SendGrid;
use SendGrid\Mail\Mail;
use Swift_Mime_SimpleMessage;

class SendGridTransport extends Transport
{
    /**
     * @var Mail
     */
    protected $mail;

    /**
     * @var SendGrid
     */
    protected $sendgrid;

    public function __construct(Mail $mail, SendGrid $sendgrid)
    {
        $this->mail = $mail;
        $this->sendgrid = $sendgrid;
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

        $this->sendgrid->send($this->mail);

        return $this->numberOfRecipients($message);
    }

    /**
     * @param Swift_Mime_SimpleMessage $message
     * @param array $to
     * @throws SendGrid\Mail\TypeException
     */
    protected function setPayload(Swift_Mime_SimpleMessage $message, array $to)
    {
        $this->mail->setFrom(config('mail.from.address'), config('mail.from.name'));
        $this->mail->setSubject($message->getSubject());
        $this->mail->addTo($to['email'], $to['name']);
        $this->mail->addContent("text/html", $message->getBody());

        if (count($message->getChildren()) > 0) {
            foreach ($message->getChildren() as $file) {
                $this->mail->addAttachment(
                    base64_encode($file->getBody()),
                    $file->getContentType(),
                    $file->getFilename(),
                    $file->getDisposition(),
                );
            }
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
