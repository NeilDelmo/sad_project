<?php

namespace App\Mail;

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Log\LoggerInterface;
use Stringable;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Component\Mime\RawMessage;

class BrevoTransport extends AbstractTransport implements Stringable
{
    protected TransactionalEmailsApi $api;

    public function __construct(string $apiKey, ?EventDispatcherInterface $dispatcher = null, ?LoggerInterface $logger = null)
    {
        parent::__construct($dispatcher, $logger);
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
        $this->api = new TransactionalEmailsApi(new GuzzleClient(), $config);
    }

    protected function doSend(SentMessage $message): void
    {
        /** @var Email $email */
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $send = new SendSmtpEmail();
        $send->setSubject($email->getSubject());

        $from = $email->getFrom()[0] ?? null;
        if ($from) {
            $send->setSender([
                'email' => $from->getAddress(),
                'name' => $from->getName() ?: $from->getAddress(),
            ]);
        }

        $send->setTo($this->mapAddresses($email->getTo()));
        if ($email->getCc()) {
            $send->setCc($this->mapAddresses($email->getCc()));
        }
        if ($email->getBcc()) {
            $send->setBcc($this->mapAddresses($email->getBcc()));
        }
        if ($email->getReplyTo()) {
            $reply = $email->getReplyTo()[0];
            $send->setReplyTo([
                'email' => $reply->getAddress(),
                'name' => $reply->getName() ?: $reply->getAddress(),
            ]);
        }

        if ($email->getHtmlBody()) {
            $send->setHtmlContent($email->getHtmlBody());
        }
        if ($email->getTextBody()) {
            $send->setTextContent($email->getTextBody());
        }

        if ($email->getAttachments()) {
            $attachments = [];
            foreach ($email->getAttachments() as $attachment) {
                $attachments[] = [
                    'name' => $attachment->getName(),
                    'content' => base64_encode((string) $attachment->getBody()),
                    'type' => $attachment->getMediaType().'/'.$attachment->getMediaSubtype(),
                ];
            }
            $send->setAttachment($attachments);
        }

        $this->api->sendTransacEmail($send);
    }

    protected function mapAddresses(array $addresses = []): array
    {
        $mapped = [];
        foreach ($addresses as $address) {
            $mapped[] = [
                'email' => $address->getAddress(),
                'name' => $address->getName() ?: $address->getAddress(),
            ];
        }
        return $mapped;
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}
