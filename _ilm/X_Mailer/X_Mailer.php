<?php

namespace _ilmComm\Core\X_Mailer;

use _ilmComm\Exceptions\IlmExceptions;

class X_Mailer
{
    /**
     * Email from
     *
     * @var string
     */
    protected $Sender;

    /**
     * Email to
     *
     * @var string
     */
    protected $Receiver;

    /**
     * Email header
     *
     * @var string
     */
    protected $Headers;

    /**
     * Email subject
     *
     * @var string
     */
    protected $Subject;

    /**
     * Set email from
     *
     * @param string $from
     * @return void
     */
    public function setEmailSender(string $from)
    {
        $this->Sender = $from;
    }

    /**
     * Set email to
     *
     * @param string $to
     * @return void
     */
    public function setEmailReceiver(string $to)
    {
        $this->Receiver  = $to;
    }

    /**
     * Set email subject
     *
     * @param string $subject
     * @return void
     */
    public function setEmailSubject(string $subject)
    {
        $this->Subject = $subject;
    }

    /**
     * Set email headers
     *
     * @param string $headers
     * @return void
     */
    public function setHeaders(string $headers = "")
    {
        if (!$headers) {
            if (!$this->Sender) {
                $this->Sender = ADMIN_EMAIL;
            }

            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= 'From: ' . $this->Sender . "\r\n";
        }

        $this->Headers = $headers;
    }

    /**
     * Send mail
     *
     * @throws IlmExceptions
     * @return boolean
     */
    public function send(string $message): bool
    {
        if (@mail($this->Receiver, $this->Subject, $message, $this->Headers)) {
            return true;
        }

        throw new IlmExceptions("No PHP mailer integrated !");
    }
}
