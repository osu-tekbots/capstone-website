<?php
namespace Email;

/**
 * Handles automated sending of emails based on events received by the server. The mailer will send emails using the
 * same 'From' address.
 */
class Mailer {
    
    /** @var string */
    private $from;

    /** @var string */
    private $subjectTag;

    /**
     * Creates a new mailer to send emails.
     *
     * @param string $from the from address for the email
     * @param string|null $subjectTag an optional tag to prefix the email subject with
     */
    public function __construct($from, $subjectTag = null) {
        $this->from = $from;
        $this->subjectTag = $subjectTag;
    }

    /**
     * Sends the provided email.
     *
     * @param string|string[] $to the email address or addresses to send the message to
     * @param string $subject the subject of the email
     * @param string $message the email content to send
     * @return boolean true on success, false otherwise
     */
    public function sendEmail($to, $subject, $message) {
        if ($this->subjectTag != null) {
            $subject = $this->subjectTag . ' ' . $subject;
        }

        $from = $this->from;

        $headers = array(
            "From: $from"
        );

        $headersStr = \implode('\r\n', $headers);
        
        if(\is_array($to)) {  
            $to = \implode(',', $to);
        }

        $accepted = \mail($to, $subject, $message, $headersStr);
        if (!$accepted) {
            return false;
        }

        return true;
    }
}
