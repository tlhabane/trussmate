<?php

namespace App\Domain\Messaging\Service;

use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;
use App\Util\Logger;
use Exception;

class EmailTransport extends PHPMailer
{
    public function __construct($exceptions = null)
    {
        parent::__construct($exceptions);
    }

    /**
     * Save email to a folder (via IMAP)
     *
     * This function will open an IMAP stream using the email
     * credentials previously specified, and will save the email
     * to a specified folder. Parameter is the folder name (ie, Sent)
     * if nothing was specified it will be saved in the inbox.
     *
     * @author David Tkachuk <http://davidrockin.com/>
     * @param string|null $folderPath
     */
    public function copyToFolder(?string $folderPath = 'Sent')
    {
        try {
            $message = $this->getSentMIMEMessage();
            $path = "INBOX" . (isset($folderPath) ? "." . $folderPath : ""); // Location to save the email
            $imapStream = imap_open("{" . $this->Host . "/tls/novalidate-cert/norsh/service=imap/user=" . $this->Username . "}" . $path, $this->Username, $this->Password);
            //$imapStream = imap_open("{" . $this->Host . "}" . $path , $this->Username, $this->Password);
            imap_append($imapStream, "{" . $this->Host . "}" . $path, $message . "\r\n", "\\Seen");
            imap_close($imapStream);
        } catch (Exception $exception) {
            Logger::addToLog(
                'mail_ERROR.log',
                sprintf('Error %s: %s', $exception->getCode(), $exception->getMessage()),
                true
            );
        }
    }
}
