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

    /** @var \Util\Logger */
    protected $logger;

    /**
     * Creates a new mailer to send emails.
     *
     * @param string $from the from address for the email
     * @param string|null $subjectTag an optional tag to prefix the email subject with
     * @param \Util\Logger|null $logger an optional logger to capture error messages from the mail() function
     */
    public function __construct($from, $subjectTag = null, $logger = null) {
        $this->from = $from;
        $this->subjectTag = $subjectTag;
        $this->logger = $logger;
    }

    /**
     * Sends the provided email.
     *
     * @param string|string[] $to the email address or addresses to send the message to
     * @param string $subject the subject of the email
     * @param string $message the email content to send
     * @param boolean $html indicates whether the message content is HTML or plain text
     * @return boolean true on success, false otherwise
     */
    public function sendEmail($to, $subject, $message, $html = false) {
        if ($this->subjectTag != null) {
            $subject = '[' . $this->subjectTag . '] ' . $subject;
        }

        $from = $this->from;

        $headers = array();

        if ($html) {
            $message = "
            <html>
            <head> 
                <title>$subject</title>
            </head>
            <body>
                $message
            </body>
            </html>
            ";

            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-Type: text/html;charset=UTF-8';
        }

        $headers[] = "From: $from";

        $headersStr = \implode('\r\n', $headers);
        
        if (\is_array($to)) {
            $to = \implode(',', $to);
        }

        $accepted = \mail($to, $subject, $message, $headersStr);
        if (!$accepted) {
            if ($this->logger != null) {
                $this->logger->error("Failed to send email to $to from $from: " . error_get_last()['message']);
                $this->logger->error($message);
            }
            return false;
        }

        return true;
    }
}
