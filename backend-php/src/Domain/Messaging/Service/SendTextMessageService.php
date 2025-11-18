<?php

namespace App\Domain\Messaging\Service;

use App\Exception\RuntimeException;
use App\Util\Logger;
use Exception;

final class SendTextMessageService
{
    private static string $textMessageClientId = 'a89def27-f285-46c0-b02e-35758fa5eeaa';
    private static string $textMessageAPISecret = 'aN2aVo0Rd1fmR6JCXrf7ZckEvRBaXxdH';
    private static string $textMessageAuthUrl = 'https://rest.smsportal.com/Authentication';
    private static string $textMessageUrl = 'https://rest.smsportal.com/bulkmessages';

    /**
     * @throws RuntimeException
     */
    public static function sendTextMessage(array $textMessages): string
    {
        try {
            $baseAccountAPICredentials = self::$textMessageClientId . ':' . self::$textMessageAPISecret;
            $encodedAccountAPICredentials = base64_encode($baseAccountAPICredentials);
            $authHeader = 'Authorization: Basic ' . $encodedAccountAPICredentials;

            $authOptions = array(
                'http' => array(
                    'header' => $authHeader,
                    'method' => 'GET',
                    'ignore_errors' => true
                )
            );

            $authContext = stream_context_create($authOptions);
            $result = file_get_contents(self::$textMessageAuthUrl, false, $authContext);
            $authResult = json_decode((string)$result);

            $status_line = $http_response_header[0];
            preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
            $status = $match[1];

            if ($status === '200') {
                $authToken = $authResult->{'token'};

                // send request
                $authHeader = 'Authorization: Bearer ' . $authToken;

                $sendData = '{"messages" : ' . json_encode($textMessages, JSON_PRETTY_PRINT) . '}';

                $options = array(
                    'http' => array(
                        'header' => array("Content-Type: application/json", $authHeader),
                        'method' => 'POST',
                        'content' => $sendData,
                        'ignore_errors' => true
                    )
                );
                $context = stream_context_create($options);

                $sendResult = file_get_contents(self::$textMessageUrl, false, $context);
                $status_line = $http_response_header[0];
                preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
                $status = $match[1];

                if ($status === '200') {
                    $sendResult = json_decode((string)$sendResult);
                    return (string)$sendResult->{'eventId'};
                }
            }
        } catch (Exception $exception) {
            Logger::addToLog(
                'sms_ERROR.log',
                sprintf('Error %s: %s', $exception->getCode(), $exception->getMessage()),
                true
            );

            throw new RuntimeException($exception->getMessage(), $exception->getCode());
        }

        throw new RuntimeException(
            'Oops! Something went wrong while processing your request, please try again.'
        );
    }
}
