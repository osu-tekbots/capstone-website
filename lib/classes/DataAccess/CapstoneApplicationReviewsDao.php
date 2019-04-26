<?php
namespace DataAccess;

use Model\CapstoneApplicationReviewInterestLevel;
use Model\CapstoneApplicationReview;

class CapstoneApplicationReviewsDao {

    /** @var DatabaseConnection */
    private $conn;

    /** @var \Util\Logger */
    private $logger;

    /**
     * Creates a new data access object for interacting with application review data in the database.
     *
     * @param DatabaseConnection $connection the connection used to interact with the database
     * @param \Util\Logger $logger the logger used to record interactions with the database
     */
    public function __construct($connection, $logger) {
        $this->conn = $connection;
        $this->logger = $logger;
    }

    /**
     * Fetches the review associated with a student's application.
     *
     * @param string $applicationId the ID of the application whose review we need to fetch
     * @return \Model\CapstoneApplicationReview|boolean the review on success, false otherwise
     */
    public function getApplicationReviewForApplication($applicationId) {
        try {
            $sql = 'SELECT * FROM capstone_application_review, capstone_interest_level ';
            $sql .= 'WHERE car_ca_id = :aid AND car_cil_id = cil_id';
            $params = array(':aid' => $applicationId);
            $results = $this->conn->query($sql, $params);
            if(!$results || \count($results) == 0) {
                return false;
            }

            return self::ExtractCapstoneApplicationReviewFromRow($results[0]);
        } catch (\Exception $e) {
            $this->logger->error("Failed to get review for application '$applicationId': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Creates a new CapstoneApplicationReview object from information taken from a database row.
     *
     * @param mixed[] $row the database row to extract information from
     * @return \Model\CapstoneApplicationReview
     */
    public static function ExtractCapstoneApplicationReviewFromRow($row) {
        return (new CapstoneApplicationReview($row['car_id']))
            ->setInterestLevel(self::ExtractCapstoneApplicationReviewInterestLevelFromRow($row, true))
            ->setComments($row['car_comments']);
    }

    /**
     * Creates a new CapstoneApplicationReviewInterestLevel object from information taken from a database row
     *
     * @param mixed[] $row the database row to extract information from
     * @param boolean $reviewInRow indicates whether the application review is included in the row the function is
     * extracting data from. Defaults to false.
     * @return \Model\CapstoneApplicationReviewInterestLevel
     */
    public static function ExtractCapstoneApplicationReviewInterestLevelFromRow($row, $reviewInRow = false) {
        $id = $reviewInRow ? 'car_cil_id' : 'cil_id';
        return new CapstoneApplicationReviewInterestLevel($row[$id], $row['cil_name']);
    }
}
