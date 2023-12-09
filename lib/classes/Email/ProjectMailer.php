<?php
namespace Email;

use Model\CapstoneProjectNDAIP;

class ProjectMailer extends Mailer {
    /**
     * Constructs a new instance of a mailer specifically for capstone project-related emails
     *
     * @param string $from the from address for emails
     * @param string|null $subjectTag an optional subject tag to prefix the provided subject tag with
     */
    public function __construct($from, $subjectTag = null) {
        parent::__construct($from, $subjectTag);
    }

    /**
     * Sends a confirmation email to the proposer after the have submitted their project.
     *
     * @param \Model\CapstoneProject $project 
     * @param string $link the URL allowing the user to view the project
     * @return boolean true on success, false otherwise
     */
    public function sendProjectSubmissionConfirmationEmail($project, $link) {
        global $configManager;

        $userName = $project->getProposer()->getFirstName() . ' ' . $project->getProposer()->getLastName();
        $pid = $project->getId();
        $title = $project->getTitle();

        $subject = "Project Submitted for Approval";

        $NDA_message = $project->getNdaIp()->getId() == CapstoneProjectNDAIP::NO_AGREEMENT_REQUIRED ? '' : '
        If your project requires an NDA and/or IP agreement, it must be indicated at the time the students select the 
        projects.

        If your company intends to provide proprietary materials or confidential information requiring an NDA, OSU can 
        arrange for a written agreement to reviewed and signed amongst the students, your company, and OSU.

        Such an agreement will authorize the students to use and discuss the provided materials or information with 
        each other and their instructor in confidence.

        The university will not participate in any agreement that requires students to transfer intellectual property 
        rights ownership to your company or puts overly burdensome confidentiality obligations on the students.

        Though OSU certainly appreciates your company’s sponsorship, we strongly discourage any agreements that could 
        deter students from sharing the results of their academic work at OSU with fellow students, parents or future 
        employers.

        This does not prevent a separate arrangement between you each student individually.
        
        If you require an NDA/IP agreement, please have your company\'s legal team fill out and return the NDA agreement ('.$configManager->getBaseURL().$configManager->get('files.nda_agreement') .') as soon as possible.';

        $message = "
        Dear $userName,

        Thank you for submitting your project!
        ---------------------------
        Project ID: $pid
        Project Title: $title
        ---------------------------

        Your project is now awaiting for approval from an administrator.

        Your project can now be viewed at: $link

        $NDA_message

        * Your project has the ability to be modified by an administrator for final revisions *

        Sincerely,

        Senior Design Capstone Team
        Oregon State University
        ";
		
		$to = Array($project->getProposer()->getEmail(), "eecs_capstone_staff@engr.orst.edu");
        return $this->sendEmail($to, $subject, $message);
    }

    /**
     * Sends an email to the proposer of a project informing them that their project was approved.
     *
     * @param \Model\CapstoneProject $project the project that is being approved
     * @param string $link the URL at which the project can be viewed
     * @return boolean true on success, false otherwise
     */
    public function sendProjectApprovedEmail($project, $link) {
        $userName = $project->getProposer()->getFirstName() . ' ' . $project->getProposer()->getLastName();
        $pid = $project->getId();
        $title = $project->getTitle();

        $subject = "Project Approved: $title";

        $content = "
        Dear $userName,

        Your project has been approved!
        ---------------------------
        Project ID: $pid
        Project Title: $title
        ---------------------------

        Your project can now be viewed at: $link

        * Your project has the ability to be modified by an administrator for final revisions *

        Sincerely,

        Senior Design Capstone Team
        Oregon State University
        ";

        return $this->sendEmail($project->getProposer()->getEmail(), $subject, $content);
    }

    /**
     * Sends an email to the proposer of a project informing them that their project was rejected.
     *
     * @param \Model\CapstoneProject $project the project that is being rejected
     * @param string $reason the reason the project is being rejected
     * @return boolean true on success, false otherwise
     */
    public function sendProjectRejectedEmail($project, $reason) {
        $userName = $project->getProposer()->getFirstName() . ' ' . $project->getProposer()->getLastName();
        $pid = $project->getId();
        $title = $project->getTitle();

        $subject = "Project Rejected: $title";

        $content = "
        Dear $userName,

        We regret to inform you that your project was not approved.
        ---------------------------
        Project ID: $pid
        Project Title: $title
        Reason for rejection: $reason
        ---------------------------

        If you have any further questions, please send us an email at eecs_capstone_staff@engr.orst.edu.

        Sincerely,

        Senior Design Capstone Team
        Oregon State University
        ";

        return $this->sendEmail($project->getProposer()->getEmail(), $subject, $content);
    }


    /**
     * Sends a notification email to the website admins about actions they need to take.
     *
     * @param int $pendingProjects the number of projects to approve
     * @param int $pendingCategories the number of projects that need categorization
     * @param string[] $addresses and array of addresses to send the email to
     * @return boolean
     */
    public function sendProjectNotificationsToAdmin($pendingProjects, $pendingCategories, $addresses) {

        $subject = "Projects Need To Be Approved!";

        $message = "
        Just a reminder, you have
        $pendingProjects - Pending Projects that need to be approved.
        $pendingCategories - Pending Projects that need categorization.
        ";

        return $this->sendEmail($addresses, $subject, $message);

    }

    public function sendActiveProjectsReminder($name, $email, $projectNames, $totalHours) {
        global $configManager;

        $projectCount = \count($projectNames);
        
        // Generate text based on number of projects
        if($projectCount > 1) {
            $projectCountText = "$projectCount projects";

            $projectListText = "As a reminder, your projects are:<BR>
                <ul>";
            foreach($projectNames as $projectName) {
                $projectListText .= "<li>$projectName</li>\n";
            }
            $projectListText .= "</ul>\n";
            
        } else {
            $projectCountText = "project";

            $projectListText = "As a reminder, your project is \"".$projectNames[0]."\".";
        }

        $subject = "Confirmation of Current Projects";

        $message = "
            Dear $name, <BR>
            <BR>
            As we get ready to kick off our Capstone projects, we would like to confirm that you are willing and able to
            serve as a project partner (dedicating one hour per team per project per week to mentoring the student team)
            for the $projectCountText you have submitted. $projectListText<br>
            ";

        if($totalHours > 1) {
            $message .= "As you have indicated willingess to mentor more than one team, we would especially like to
            confirm that you are able to dedicate up to <b>$totalHours hours per week</b> to serving as a mentor.<BR>";
        }

        $message .= "
        <BR>
        Sincerely,<BR>
        <BR>
        Senior Design Capstone Team<BR>
        Oregon State University";

		$to = $email;
        return $this->sendEmail($to, $subject, $message, true);
    }
}
