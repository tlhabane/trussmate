<?php

namespace App\Domain\Messaging\Service;

use PHPMailer\PHPMailer\Exception as MailException;
use App\Exception\RuntimeException;
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

                Logger::addToLog(
                    'mail_SUCCESS.log',
                    sprintf('Email successfully sent to %s (%s).', $data['recipientName'], $data['recipientEmail'])
                );
                return ['success' => "Email successfully sent to {$data['recipientName']}."];
            }

        } catch (MailException|Exception $exception) {
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
