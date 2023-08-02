<?php
namespace Email;

/**
 * Responsible for sending emails related to actions on applications for capstone projects.
 */
class ApplicationMailer extends Mailer {



    /**
     * Constructs a new instance of a mailer specifically for capstone project application-related emails
     *
     * @param string $from the from address for emails
     * @param string|null $subjectTag an optional subject tag to prefix the provided subject tag with
     */
    public function __construct($from, $subjectTag = null) {
        parent::__construct($from, $subjectTag);
    }

    /**
     * Sends a confirmation email to the student of the provided application informing them their project was
     * submitted.
     *
     * @param \Model\CapstoneApplication $application the application being submitted by the student
     * @param string $link the URL of the page where the student can view their application
     * @return boolean true on success, false otherwise
     */
    public function sendApplicationSubmissionConfirmation($application, $link) {
        $first_name = $application->getStudent()->getFirstName();
        $last_name = $application->getStudent()->getLastName();
        $title =$application->getCapstoneProject()->getTitle();

        $subject = "Application Submission for: $title";

        $message = "
        Dear $first_name $last_name,

        Thank you for submitting your application for the following project: 

        ---------------------------
        Project Title: $title
        ---------------------------

        Your application can be viewed at: $link. 
        
        Thank you,

        Senior Design Capstone Team
        Oregon State University
        ";
        
        return $this->sendEmail($application->getStudent()->getEmail(), $subject, $message);
    }

    /**
     * Sends a notification email to the proposer of a project when a student submits an application for their
     * project.
     *
     * NOTE: The project reference associated with the application must have the proposer reference set.
     * 
     * @param \Model\CapstoneApplication $application the application submitted by the student
     * @param string $link the URL to the page where the proposer can view the application
     * @return boolean true on success, false otherwise
     */
    public function sendApplicationSubmissionNotification($application, $link) {
        $first_name = $application->getCapstoneProject()->getProposer()->getFirstName();
        $last_name = $application->getCapstoneProject()->getProposer()->getLastName();
        $title = $application->getCapstoneProject()->getTitle();
        $myapplicationLink = "https://eecs.engineering.oregonstate.edu/capstone/submission/pages/myApplications.php";

        $subject = "An Application has been submitted for: $title";

        $message = "

        Dear $first_name $last_name,

        An application has been submitted for the following project:

        ---------------------------
        Project Title: $title
        ---------------------------

        You can review this applications at: $link.
        
        Additionally, you can view all of your existing applications at: $myapplicationLink.
        
        Thank you,

        Senior Design Capstone Team
        Oregon State University
        ";

        return $this->sendEmail($application->getCapstoneProject()->getProposer()->getEmail(), $subject, $message);
    }

    public function sendUnreviewedApplicationNotification($first_name, $last_name, $title, $email_to){

        $myapplicationLink = "https://eecs.engineering.oregonstate.edu/capstone/submission/pages/myApplications.php";

        $subject = "Student Applications for your project: $title";

        $message = "

        Dear $first_name $last_name,

        There is at least one application submitted for your project, $title that has yet to be reviewed.

        You can view all of your existing applications at: $myapplicationLink.
        
        Thank you,

        Senior Design Capstone Team
        Oregon State University
        ";

        return $this->sendEmail($email_to, $subject, $message);
    }

    public function sendLastApplicationSubmittedDate(){
        $email_to = "eecs_capstone_staff@engr.orst.edu";
        $currentDate = date("Y-m-d");
        $currentDateTime = date("Y-m-d h:i:sa");


        $subject = "[LOG] Reminder Email Sent : $currentDate";

        $message = "

        Dear Capstone Administrators,

        Please use this for logging and documentation processes.
        
        A reminder email sent to proposers:

        ---------------------------
        $currentDateTime
        ---------------------------

        Thank you,

        Senior Design Capstone Team
        Oregon State University
        ";

        return $this->sendEmail($email_to, $subject, $message);
    }
}
