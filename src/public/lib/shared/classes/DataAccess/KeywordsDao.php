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
	
    public function adminGetApprovedKeywords() {
        try {
            $sql = '
            SELECT * 
            FROM capstone_keyword
			WHERE ck_approved = 1
			ORDER BY ck_name ASC
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
	
    public function adminGetUnapprovedKeywords() {
        try {
            $sql = '
            SELECT * 
            FROM capstone_keyword
			WHERE ck_approved = 0
			ORDER BY ck_name ASC
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

	public function adminRemoveKeywordEverywhere($keywordId){
		try {
            $sql = '
            DELETE FROM capstone_keyword_for
            WHERE capstone_keyword_for.ckf_ck_id = :keywordId
            ';
            $params = array(
                ':keywordId' => $keywordId
            );
            $this->conn->execute($sql, $params);

            $sql = '
            DELETE FROM `capstone_keyword` 
            WHERE ck_id = :keywordId
            ';
            $params = array(
                ':keywordId' => $keywordId
            );
            $this->conn->execute($sql, $params);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to delete keyword everywhere: ' . $e->getMessage());
            return false;
        }
	}

    public function adminUpdateKeyword($keywordId, $keywordText) {
        try {
            $sql = '
            UPDATE `capstone_keyword` 
            SET `ck_name` = :keywordText
            WHERE `capstone_keyword`.`ck_id` = :keywordId;
            ';
            $params = array(
                ':keywordText' => $keywordText,
                ':keywordId' => $keywordId
            );
            $this->conn->execute($sql, $params);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to update keyword: ' . $e->getMessage());
            return false;
        }
    }

    public function adminUpdateApproval($keywordId, $approved) {
        try {
            $sql = '
            UPDATE `capstone_keyword` 
            SET `ck_approved` = :approved
            WHERE `capstone_keyword`.`ck_id` = :keywordId;
            ';
            $params = array(
                ':approved' => $approved,
                ':keywordId' => $keywordId
            );
            $this->conn->execute($sql, $params);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to update keyword approval: ' . $e->getMessage());
            return false;
        }
    }

    public function adminGetNumberUsedIn($keywordId) {
        try {
            $sql = '
            SELECT COUNT(*) FROM `capstone_keyword_for`
            WHERE ckf_ck_id = :keywordId;
            ';
            $params = array(
                ':keywordId' => $keywordId
            );
            $results = $this->conn->query($sql, $params);

            return implode("&",array_map(function($a) {return implode("~",$a);},$results)); // Not sure why this works
        } catch (\Exception $e) {
            $this->logger->error('Failed to update keyword approval: ' . $e->getMessage());
            return false;
        }
        
    }
    
    public function adminMergeKeywords($keywordIds) {
        try {
            // Get the first match that's approved or use first value if the query didn't find any approved keywords
            $sql = '
                SELECT ck_id, min(ck_name) FROM capstone_keyword 
                WHERE ck_id IN (:keywordIds)
                AND NOT ck_approved = 0';
            $params = array(
                ':keywordIds' => implode(', ', $keywordIds)
            );
            $results = $this->conn->query($sql, $params);
            $finalId = $results[0]['ck_id'] ?? intval($keywordIds[0]);
            //unset($keywordIds[array_search($finalId, $keywordIds)]);
            
            // Escape the input with intval to prevent arbitrary SQL injection
            $ids = implode(',', array_map('intval', $keywordIds));

            // Get any projects that use a soon-to-be-removed keyword but not the final keyword
            // NOTE: FIND_IN_SET used bc $this->conn automatically adds quotes around string vars, preventing WHERE _ IN
            //     from executing properly
            $sql = '
                WITH uniqueEntities AS (
                    SELECT * FROM capstone_keyword_for
                    WHERE FIND_IN_SET(ckf_ck_id, :keywordIds) <> 0
                    GROUP BY ckf_entity_id
                    )
                
                SELECT * FROM uniqueEntities
                WHERE ckf_ck_id <> :finalId;';
            $params = array(
                ':keywordIds' => $ids,
                ':finalId' => $finalId
            );
            $results = $this->conn->query($sql, $params);
            $entities = array_map(function ($val) {return $val['ckf_entity_id'];}, $results);
            $this->logger->info('Keyword ID list: '.$ids.' -> '.$finalId);
            // $this->logger->info('Keyword entities list: '.var_export($entities, true));

            // Add a database row with the final keyword & any projects that don't already have it
            $insertArray = '';
            foreach($entities as $entity) {
                $insertArray .= '(' .
                    $finalId . ', "' . $entity .
                    '"), ';
            }
            $insertArray = rtrim($insertArray, ", ");

            if(strlen($insertArray)) {
                $sql = 'INSERT INTO capstone_keyword_for(ckf_ck_id, ckf_entity_id) VALUES '.$insertArray;
                $this->conn->execute($sql);
            }
            
            // Remove all database rows that use a merged keyword that's not the final one
            // NOTE: FIND_IN_SET used bc $this->conn automatically adds quotes around string vars, preventing WHERE _ IN
            //     from executing properly
            $sql = '
                DELETE FROM capstone_keyword_for
                WHERE FIND_IN_SET(ckf_ck_id, :keywordIds) <> 0
                    AND ckf_ck_id <> :finalId';
            $params = array(
                ':keywordIds' => $ids,
                ':finalId' => $finalId
            );
            $results = $this->conn->query($sql, $params);

            // Remove now-unused keywords from keyword table
            $sql = '
                DELETE FROM `capstone_keyword` 
                WHERE FIND_IN_SET(ck_id, :keywordIds) <> 0
                    AND NOT ck_id = :finalId';
            $params = array(
                ':keywordIds' => $ids,
                ':finalId' => $finalId
            );
            $this->conn->execute($sql, $params);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to merge keywords: ' . $e->getMessage());
            return false;
        }
    }
	
	
    public function getAllKeywords() {
        try {
            $sql = '
            SELECT * 
            FROM capstone_keyword
			WHERE ck_approved = 1
			ORDER BY ck_name ASC
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