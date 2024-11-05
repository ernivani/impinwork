<?php 

namespace Ernicani\Mail;

class Mail 
{
    private $to;
    private $from;
    private $subject;
    private $message;

    public function __construct(string $to, string $from, string $subject, string $message)
    {
        
        $this->to = $to;
        $this->from = $from;
        $this->subject = $subject;
        $this->message = $message;
    }

    public function send()
    {
        $headers = "From: $this->from" . "\r\n" .
            "Reply-To: $this->from" . "\r\n" .
            "X-Mailer: PHP/" . phpversion();

        mail($this->to, $this->subject, $this->message, $headers);
    }
}