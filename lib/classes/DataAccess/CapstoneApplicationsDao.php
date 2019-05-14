<?php
namespace DataAccess;

use Model\CapstoneApplication;
use Model\CapstoneApplicationStatus;
use Model\CapstoneInterestLevel;

/**
 * Contains logic for database interactions with capstone project application data in the database. 
 * 
 * DAO stands for 'Data Access Object'
 */
class CapstoneApplicationsDao {

    /** @var DatabaseConnection */
    private $conn;

    /** @var \Util\Logger */
    private $logger;

    /**
     * Constructs a new instance of a Capstone Application Data Access Object.
     *
     * @param DatabaseConnection $connection the connection used to perform application related queries on the database
     * @param \Util\Logger $logger the logger to use for logging messages and errors associated with fetching data
     */
    public function __construct($connection, $logger = null) {
        $this->conn = $connection;
        $this->logger = $logger;
    }

    /**
     * Fetches all of the applications in the database.
     *
     * @param boolean $includeUser indicates whether to also fetch the user and create an associated User object.
     * Defaults to true.
     * @param boolean $includeProject indicates whether to also fetch the project and create an associated
     * CapstoneProject object. Defaults to true.
     * @return \Model\CapstoneApplication|boolean an array of the resulting applications, or false if the fetch fails
     */
    public function getAllApplications($includeUser = true, $includeProject = true) {
        try {
            $userTable = $includeUser ? ', user ' : '';
            $userAnd = $includeUser ? 'AND ca_u_id = u_id' : '';
            $projectTable = $includeProject ? ', capstone_project ' : '';
            $projectAnd = $includeProject ? 'AND ca_cp_id = cp_id' : '';
            $sql = "
            SELECT * FROM capstone_application, capstone_application_status $userTable $projectTable
            WHERE ca_cas_id = cas_id $userAnd $projectAnd
            ";
            $results = $this->conn->query($sql);

            $applications = array();
            foreach ($results as $row) {
                $applications[] = self::ExtractApplicationFromRow($row, true);
            }

            return $applications;
        } catch (\Exception $e) {
            $this->logError('Failed to get all applications: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches all of the applications in the database associated with the project with the provided ID.
     *
     * @param string $projectId the ID of the project whose applications the DAO will fetch
     * @param boolean $submitted indicates whether to only fetch submitted applications
     * @return \Model\CapstoneApplication|boolean an array of the resulting applications, or false if the fetch fails
     */
    public function getAllApplicationsForProject($projectId, $submitted = false) {
        try {
            $sql = 'SELECT * FROM capstone_application, capstone_application_status, capstone_project, user ';
            $sql .= 'WHERE ca_cp_id = :id AND ca_cp_id = cp_id AND ca_cas_id = cas_id AND ca_u_id = u_id';
            $params = array(':id' => $projectId);
            if ($submitted) {
                $sql .= ' AND ca_cas_id = :status';
                $params[':status'] = CapstoneApplicationStatus::SUBMITTED;
            }
            $results = $this->conn->query($sql, $params);

            $applications = array();
            foreach ($results as $row) {
                $applications[] = self::ExtractApplicationFromRow($row, true);
            }

            return $applications;
        } catch (\Exception $e) {
            $this->logError('Failed to get applications for project with id "' . $projectId . '": ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches all of the applications in the database associated with the user with the provided ID.
     *
     * @param string $projectId the ID of the user whose applications the DAO will fetch
     * @return \Model\CapstoneApplication[]|boolean an array of the resulting applications, or false if the fetch fails
     */
    public function getAllApplicationsForUser($userId) {
        try {
            $sql = 'SELECT * FROM capstone_application, capstone_application_status, capstone_project, user ';
            $sql .= 'WHERE ca_u_id = :id AND ca_cas_id = cas_id AND ca_u_id = u_id AND ca_cp_id = cp_id ';
            $sql .= 'ORDER BY ca_date_created DESC';
            $params = array(':id' => $userId);
            $results = $this->conn->query($sql, $params);

            $applications = array();
            foreach ($results as $row) {
                $applications[] = self::ExtractApplicationFromRow($row, true);
            }

            return $applications;
        } catch (\Exception $e) {
            $this->logError('Failed to get applications for user with id "' . $userId . '": ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches a single application with the provided ID from the database.
     * 
     * Although project data is included, proposer data is not included. This is because of conflicts in field names
     * between proposer and student. This could be corrected in the future.
     * // TODO: investigate the structure of the query and related models for merging proposer data
     *
     * @param string $id
     * @return \Model\CapstoneApplication|boolean the resulting application if it exists and the query succeeds, false
     * otherwise
     */
    public function getApplication($id) {
        try {
            $sql = 'SELECT * FROM capstone_application, capstone_application_status, capstone_project, user ';
            $sql .= 'WHERE ca_id = :id AND ca_cas_id = cas_id AND ca_u_id = u_id AND ca_cp_id = cp_id';
            $params = array(':id' => $id);
            
            $results = $this->conn->query($sql, $params);
            if (\count($results) == 0) {
                return false;
            }

            return self::ExtractApplicationFromRow($results[0], true);
        } catch (\Exception $e) {
            $this->logError('Failed to get application with id "' . $id . '": ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Adds a new capstone application object to the database. This will also add a new entry for application review.
     *
     * @param \Model\CapstoneApplication $application the application to add
     * @return boolean true if the execution of the query succeeds, false otherwise
     */
    public function addNewApplication($application) {
        try {
            $this->conn->startTransaction();

            // First create the application entry
            $sql = '
            INSERT INTO capstone_application (
                ca_id, ca_cp_id, ca_u_id, ca_justification, ca_time_available, ca_skill_set, ca_portfolio_link, 
                ca_cas_id, ca_review_cil_id, ca_review_proposer_comments, ca_date_created, ca_date_updated, 
                ca_date_submitted
            ) VALUES (
                :id, :projid, :userid, :just, :timeav, :skills, :port, :statid, :rilid, :rilpc, :datec, :dateu, :dates
            )
            ';
            $params = array(
                ':id' => $application->getId(),
                ':projid' => $application->getCapstoneProject()->getId(),
                ':userid' => $application->getStudent()->getId(),
                ':just' => $application->getJustification(),
                ':timeav' => $application->getTimeAvailable(),
                ':skills' => $application->getSkillSet(),
                ':port' => $application->getPortfolioLink(),
                ':statid' => $application->getStatus()->getId(),
                ':rilid' =>$application->getReviewInterestLevel()->getId(),
                ':rilpc' => $application->getReviewProposerComments(),
                ':datec' => QueryUtils::FormatDate($application->getDateCreated()),
                ':dateu' => QueryUtils::FormatDate($application->getDateUpdated()),
                ':dates' => QueryUtils::FormatDate($application->getDateSubmitted())
            );
            $this->conn->execute($sql, $params);

            $this->conn->commit();
            
            return true;
        } catch (\Exception $e) {
            $this->conn->rollback();
            $this->logError('Failed to insert new application with id "' . $application->getId() . '": ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update selected fields for an existing capstone application in the database.
     * 
     * @param \Model\CapstoneApplication $application the capstone application to update
     * @return boolean true if the update succeeds, false otherwise
     */
    public function updateApplication($application) {
        try {
            $sql = '
            UPDATE capstone_application SET 
                ca_justification = :just,
                ca_time_available = :timeav,
                ca_skill_set = :skills,
                ca_portfolio_link = :port,
                ca_cas_id = :statid,
                ca_review_cil_id = :rilid,
                ca_review_proposer_comments = :rilpc,
                ca_date_updated = :dateu,
                ca_date_submitted = :dates
            WHERE ca_id = :id
            ';
            $params = array(
                ':id' => $application->getId(),
                ':just' => $application->getJustification(),
                ':timeav' => $application->getTimeAvailable(),
                ':skills' => $application->getSkillSet(),
                ':port' => $application->getPortfolioLink(),
                ':statid' => $application->getStatus()->getId(),
                ':rilid' => $application->getReviewInterestLevel()->getId(),
                ':rilpc' => $application->getReviewProposerComments(),
                ':dateu' => QueryUtils::FormatDate($application->getDateUpdated()),
                ':dates' => QueryUtils::FormatDate($application->getDateSubmitted())
            );
            $this->conn->execute($sql, $params);
            return true;
        } catch (\Exception $e) {
            $this->logError('Failed to update application with id "' . $application->getId() . '": ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches the enumerated interest level values for indicating how interested a proposer is in the user that
     * submitted the application
     *
     * @return \Model\CapstoneInterestLevel[]|boolean an array if interest levels on success, false otherwise
     */
    public function getApplicationReviewInterestLevels() {
        try {
            $sql = 'SELECT * FROM capstone_interest_level';
            $results = $this->conn->query($sql);

            $levels = array();
            foreach($results as $row) {
                $levels[] = self::ExtractCapstoneInterestLevelFromRow($row);
            }

            return $levels;

        } catch(\Exception $e) {
            $this->logError('Failed to get application review interest levels: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Creates a new capstone application object using data from a database row.
     * 
     * @param mixed[] $row the database row being used to create the capstone application
     * @param boolean $includeProject flag to indicate whether to also extract the project from the row.
     * @return \Model\CapstoneApplication the extracted application
     */
    public static function ExtractApplicationFromRow($row, $includeProject = false) {
        $app = new CapstoneApplication($row['ca_id']);
        $app->setStudentId($row['ca_u_id'])
            ->setCapstoneProjectId($row['ca_cp_id'])
            ->setStudent(UsersDao::ExtractUserFromRow($row))
            ->setJustification($row['ca_justification'])
            ->setTimeAvailable($row['ca_time_available'])
            ->setSkillSet($row['ca_skill_set'])
            ->setPortfolioLink($row['ca_portfolio_link'])
            ->setStatus(self::ExtractApplicationStatusFromRow($row, true))
            ->setReviewInterestLevel(self::ExtractCapstoneInterestLevelFromRow($row, true))
            ->setReviewProposerComments($row['ca_review_proposer_comments'])
            ->setDateCreated(new \DateTime($row['ca_date_created']))
            ->setDateUpdated(new \DateTime($row['ca_date_updated']))
            ->setDateSubmitted(new \DateTime($row['ca_date_submitted']));
        if ($includeProject) {
            $app->setCapstoneProject(CapstoneProjectsDao::ExtractCapstoneProjectFromRow($row));
        }
        return $app;
    }

    /**
     * Creates a new capstone application status object using data from a database row.
     * 
     * @param mixed[] $row the database row being used to create the capstone application status
     * @param boolean $applicationInRow indicating whether entries from the capstone application table are in the row
     * @return \Model\CapstoneApplication the extracted application status
     */
    public static function ExtractApplicationStatusFromRow($row, $applicationInRow = false) {
        $idKey = $applicationInRow ? 'ca_cas_id' : 'cas_id';
        $name = isset($row['cas_name']) ? $row['cas_name'] : null;
        return new CapstoneApplicationStatus($row[$idKey], $name);
    }

    /**
     * Creates a new capstone application interest level enumeration object from a row in the database.
     *
     * @param mixed[] $row the row from the database
     * @param boolean $applicationInRow indicating whether entries from the capstone application table are in the row
     * @return \Model\CapstoneInterestLevel the extracted interest level
     */
    public static function ExtractCapstoneInterestLevelFromRow($row, $applicationInRow = false) {
        $idKey = $applicationInRow ? 'ca_review_cil_id' : 'cil_id';
        $name = isset($row['cil_name']) ? $row['cil_name'] : null;
        return new CapstoneInterestLevel($row[$idKey], $name);
    }


    /**
     * Logs an error if a logger was provided to the class when it was constructed.
     * 
     * Essentially a wrapper around the error logging so we don't cause the equivalent of a null pointer exception.
     *
     * @param string $message the message to log.
     * @return void
     */
    private function logError($message) {
        if ($this->logger != null) {
            $this->logger->error($message);
        }
    }
}
