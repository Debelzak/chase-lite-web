<?php
namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer extends PHPMailer
{
    private $smtpHost = SMTP_HOST;
    private $smtpPort = SMTP_PORT;
    private $smtpUsername = SMTP_USERNAME;
    private $smtpPassword = SMTP_PASSWORD;
    private $smtpSenderMail = SMTP_SENDERMAIL;
    private $smtpSenderName = SMTP_SENDERNAME;

    public function __construct()
    {
        $this->exceptions = false;
        //$this->SMTPDebug = SMTP::DEBUG_SERVER;
        $this->isSMTP();
        $this->Host = $this->smtpHost;
        $this->SMTPAuth = true;
        $this->Username = $this->smtpUsername;
        $this->Password = $this->smtpPassword;
        $this->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->Port = $this->smtpPort;
        $this->isHTML(true);
        $this->setFrom($this->smtpSenderMail, $this->smtpSenderName);
    }

    public function sendMail($to, $subject, $body)
    {
        $this->addAddress($to);
        $this->Subject = $subject;
        $this->Body = $body;

        try
        {
            $this->send();
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
}