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
			WHERE ck_approved = 1
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
	
	
    public function getKeywordsForEntity($entityId) {
        try {
            $sql = '
            SELECT * 
            FROM capstone_keyword, capstone_keyword_for
			WHERE capstone_keyword_for.ckf_ck_id = capstone_keyword.ck_id
			AND capstone_keyword_for.ckf_entity_id = :entityId
            ';
            $params = array(':entityId' => $entityId);
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
	
	public function getKeyword($name) {
        try {
            $sql = '
            SELECT * 
            FROM capstone_keyword
			WHERE capstone_keyword.ck_name = :name
            ';
            $params = array(':name' => $name);
            $results = $this->conn->query($sql, $params);
			
			if (\count($results) == 0) {
                return false;
            }

			return self::ExtractKeywordFromRow($results[0], true);
        } catch (\Exception $e) {
            $this->logger->error("Failed to get keyowrds for object " . $e->getMessage());
            return false;
        }
    }
	
	public function keywordExists($keyword) {
        try {
            $sql = '
            SELECT * 
            FROM capstone_keyword
			WHERE capstone_keyword.ck_name = :keyword
            ';
            $params = array(':keyword' => $keyword);
            $results = $this->conn->query($sql, $params);

			if (\count($results) == 0) {
					return false;
			}
			
			return true;
            
        } catch (\Exception $e) {
            $this->logger->error("Failed to check keywords for object " . $e->getMessage());
            return false;
        }
    }
	
	
	public function keywordExistsForEntity($keyword, $entityId) {
        try {
            $sql = '
            SELECT * 
            FROM capstone_keyword, capstone_keyword_for 
			WHERE capstone_keyword_for.ckf_ck_id = capstone_keyword.ck_id
			AND capstone_keyword.ck_name = :keyword
			AND capstone_keyword_for.ckf_entity_id = :entityId
            ';
            $params = array(':keyword' => $keyword, ':entityId' => $entityId);
            $results = $this->conn->query($sql, $params);

			if (\count($results) == 0) {
					return false;
			}
			
			return true;
            
        } catch (\Exception $e) {
            $this->logger->error("Failed to check keywords entity for object " . $e->getMessage());
            return false;
        }
    }
	
	
	public function addKeyword($keyword, $approved) {
        try {
            $sql = '
            INSERT INTO capstone_keyword VALUES (
                NULL,
                :keyword,
                NULL,
                :approved
            )';
            $params = array(
                ':keyword' => $keyword,
                ':approved' => $approved
            );
            $this->conn->execute($sql, $params);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to add new keyword: ' . $e->getMessage());
            return false;
        }
    }
	
	
	public function addKeywordInJoinTable($keyword, $entityId) {
        try {
            $sql = '
            INSERT INTO capstone_keyword_for VALUES (
                :keywordId,
				:entityId
            )';
            $params = array(
                ':keywordId' => $keyword->getId(),
                ':entityId' => $entityId
            );
            $this->conn->execute($sql, $params);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to add new keyword in join table: ' . $e->getMessage());
            return false;
        }
    }

	public function removeAllKeywordsForEntity($entityId){
		 try {
            $sql = '
            DELETE FROM capstone_keyword_for
			WHERE capstone_keyword_for.ckf_entity_id = :entityId
            ';
            $params = array(
                ':entityId' => $entityId
            );
            $this->conn->execute($sql, $params);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to delete all keywords in join table: ' . $e->getMessage());
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