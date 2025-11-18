<?php

namespace App\Domain\Messaging\Service;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use App\Util\Logger;

class EmailTransport extends PHPMailer
{
    /**
     * Save email to a folder (via IMAP)
     *
     * This function will open an IMAP stream using the email
     * credentials previously specified, and will save the email
     * to a specified folder. Parameter is the folder name (ie, Sent)
     * if nothing was specified it will be saved in the inbox.
     *
     * @author David Tkachuk <http://davidrockin.com/>
     * @param null $folderPath
     */
    public function copyToFolder($folderPath = null)
    {
        try {
            $message = $this->getSentMIMEMessage();
            $path = "INBOX" . (isset($folderPath) ? "." . $folderPath : ""); // Location to save the email
            $imapStream = imap_open(
                "{" . $this->Host . "/tls/novalidate-cert/norsh/service=imap/user=" . $this->Username . "}" .
                $path,
                $this->Username,
                $this->Password
            );
            //$imapStream = imap_open("{" . $this->Host . "}" . $path , $this->Username, $this->Password);
            imap_append($imapStream, "{" . $this->Host . "}" . $path, $message . "\r\n", "\\Seen");
            imap_close($imapStream);
        } catch (Exception $exception) {
            Logger::addToLog(
                'auto_backup_ERROR.log',
                sprintf('Error %s: %s', $exception->getCode(), $exception->getMessage()),
                true
            );
        }
    }
}
