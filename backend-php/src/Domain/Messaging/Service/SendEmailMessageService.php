<?php

namespace App\Domain\Messaging\Service;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;
use App\Exception\RuntimeException;
use App\Factory\ContainerFactory;
use App\Util\Logger;
use Exception;

final class SendEmailMessageService
{
    /**
     * @throws RuntimeException
     */
    public static function sendEmail(array $data): array
    {
        $mail = new EmailTransport(true);
        try {
            $container = (new ContainerFactory())->createInstance();
            $settings = $container->get('settings')['email'];
            $mail->isSMTP();
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            $mail->Host = $settings['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $settings['username'];
            $mail->Password = $settings['password'];
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $settings['port'];


            $mail->Subject = $data['subject'];
            $emails = preg_split("/[\s,;]+/", $data['recipientEmail']);
            if ($emails && count($emails) > 0) {
                foreach ($emails as $index => $email) {
                    if (!empty(trim($email))) {
                        $trimmedEmail = trim($email);
                        if ($index === 0) {
                            $mail->addAddress($trimmedEmail, $data['recipientName']);
                        } else {
                            $mail->addCC($trimmedEmail);
                        }
                    }
                }
            } else {
                $mail->addAddress($data['recipientEmail'], $data['recipientName']);
            }

            $mail->setFrom($data['senderEmail'], $data['senderName']);
            $mail->clearReplyTos();
            $mail->addReplyTo($data['senderEmail'], $data['senderName']);

            if (isset($data['attachments'])) {
                foreach ($data['attachments'] as $attachment) {
                    if (file_exists($attachment['fileSource'])) {
                        $mail->addAttachment($attachment['fileSource'], $attachment['fileName'] ?? '');
                    }
                }
            }
            $mail->isHTML();
            $mail->Body = $data['messageBody'];

            if ($mail->send()) {
                $mail->clearAddresses();
                $mail->clearAllRecipients();
                $mail->clearAttachments();
                $mail->copyToFolder();

                Logger::addToLog(
                    'mail_SUCCESS.log',
                    sprintf('Email successfully sent to %s (%s).', $data['recipientName'], $data['recipientEmail'])
                );
                return ['success' => "Email successfully sent to {$data['recipientName']}."];
            }

        } catch (MailException|Exception|NotFoundExceptionInterface|ContainerExceptionInterface $exception) {
            $mail->clearAddresses();
            $mail->clearAllRecipients();
            $mail->clearAttachments();

            Logger::addToLog(
                'mail_ERROR.log',
                sprintf('Error %s: %s', $exception->getCode(), $exception->getMessage()),
                true
            );
        }

        $mail->clearAddresses();
        $mail->clearAllRecipients();
        $mail->clearAttachments();

        throw new RuntimeException(
            'Oops! Something went wrong while processing your request, please try again.'
        );
    }
}
