<?php

namespace Ernicani\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailConstructor
{
    private $to;
    private $from;
    private $subject;
    private $message;
    private $mailerPort;
    private $hostUrl;
    // private $HOST_URL = "services.communs1-s1.r1.pi2.minint.fr";

    public function __construct( array|string $to,string $from, string $subject, string $message, string $hostUrl, int $mailerPort = 25)
    {
        $this->to = $to;
        $this->from = $from;
        $this->subject = $subject;
        $this->message = $message;
        $this->hostUrl = $hostUrl;
        $this->mailerPort = $mailerPort;
    }

    public function send()
    {
        $mail = new PHPMailer(true);

        if (empty($this->to)) {
            throw new \Exception('No recipient specified');
        }

        try {
            $mail->isSMTP();
            $mail->Host = $this->hostUrl;
            $mail->SMTPAuth = false;
            $mail->Port = $this->mailerPort;

            $mail->setFrom($this->from, 'Formulaire Trauma');

            if (is_string($this->to)) {
                $mail->addAddress($this->to);
            }
            else {
                foreach ($this->to as $recipient) {
                    $mail->addAddress($recipient);
                }
            }

            $mail->isHTML(false);
            $mail->Subject = $this->subject;
            $mail->Body = $this->message;

            // disable SSL
            $mail->SMTPAutoTLS = false;
            $mail->SMTPSecure = false;

            // Character encoding
            $mail->CharSet = 'UTF-8';

            $mail->send();
            return true;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            return false;
        }
    }
}

