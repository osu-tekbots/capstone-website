<?php
namespace DataAccess;

use Model\Keyword;

class KeywordsDao{
	/** @var DatabaseConnection */
    private $conn;

    /** @var \Util\Logger */
    private $logger;

    /**
     * Creates a new instance of the data access object for capstone project data.
     *
     * @param DatabaseConnection $connection the connection to use to communiate with the database
     * @param \Util\Logger $logger the logger to use to log details about the interactions with the database
     */
    public function __construct($connection, $logger) {
        $this->conn = $connection;
        $this->logger = $logger;
    }
	

    public function getAllKeywords() {
        try {
            $sql = '
            SELECT * 
            FROM capstone_keyword
            ';

            $results = $this->conn->query($sql);

            $keywords = array();
            foreach ($results as $row) {
                $k = self::ExtractKeywordFromRow($row, true);
                $keywords[] = $k;
            }

            return $keywords;
        } catch (\Exception $e) {
            $this->logger->error('Failed to get many keywords: ' . $e->getMessage());
            return false;
        }
    }
	

    public function getKeywordsForObject($objectId) {
        try {
            $sql = '
            SELECT * 
            FROM capstone_keyword, capstone_keyword_for
			WHERE capstone_keyword_for.ckf_ck_id = capstone_keyword.ck_id
			AND capstone_keyword_for.ckf_entity_id = :objectId
            ';
            $params = array(':objectId' => $objectId);
            $results = $this->conn->query($sql, $params);

            $keywords = array();
            foreach ($results as $row) {
                $k = self::ExtractKeywordFromRow($row, true);
                $keywords[] = $k;
            }
           
            return $keywords;
        } catch (\Exception $e) {
            $this->logger->error("Failed to get keyowrds for object " . $e->getMessage());
            return false;
        }
    }
	
	public static function ExtractKeywordFromRow($row){
		$keyword = new Keyword($row['ck_id']);
		$keyword->setName($row['ck_name'])
			->setParentId($row['ck_parent_ck_id'])
			->setApproved($row['ck_approved']);
		return $keyword;
	}
	
}

?>