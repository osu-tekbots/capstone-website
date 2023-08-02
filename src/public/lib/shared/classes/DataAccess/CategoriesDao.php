<?php
namespace DataAccess;

use Model\Category;

class CategoriesDao{
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

    public function getAllCategories() {
        try {
            $sql = '
            SELECT * 
            FROM capstone_project_category
            ';

            $results = $this->conn->query($sql);

            $categories = array();
            foreach ($results as $row) {
                $c = self::ExtractCategoryFromRow($row, true);
                $categories[] = $c;
            }

            return $categories;
        } catch (\Exception $e) {
            $this->logger->error('Failed to get many categories: ' . $e->getMessage());
            return false;
        }
    }

	
    public function getCategoriesForEntity($entityId) {
        try {
            $sql = '
            SELECT * 
            FROM capstone_project_category, capstone_project_category_for
			WHERE capstone_project_category_for.cpcf_cpc_id = capstone_project_category.cpc_id
			AND capstone_project_category_for.cpcf_entity_id = :entityId
            ';
         //8/5/2022: Removing category mapping per entity?
		/*	$sql = '
            SELECT * FROM capstone_project_category 
            ';
		*/	$params = array(':entityId' => $entityId);
            $results = $this->conn->query($sql, $params);

            $categories = array();
            foreach ($results as $row) {
                $c = self::ExtractCategoryFromRow($row, true);
                $categories[] = $c;
            }
           
            return $categories;
        } catch (\Exception $e) {
            $this->logger->error("Failed to get categories for object " . $e->getMessage());
            return false;
        }
    }

    public function getCategory($name) {
        try {
            $sql = '
            SELECT * 
            FROM capstone_project_category
			WHERE capstone_project_category.cpc_name = :name
            ';
            $params = array(':name' => $name);
            $results = $this->conn->query($sql, $params);
			
			if (\count($results) == 0) {
                return false;
            }

			return self::ExtractCategoryFromRow($results[0], true);
        } catch (\Exception $e) {
            $this->logger->error("Failed to get category for object " . $e->getMessage());
            return false;
        }
    }


    // May delete for category 
    public function categoryExists($category) {
        try {
            $sql = '
            SELECT * 
            FROM capstone_project_category
			WHERE capstone_project_category.cpc_name = :category
            ';
            $params = array(':category' => $category);
            $results = $this->conn->query($sql, $params);

			if (\count($results) == 0) {
					return false;
			}
			
			return true;
            
        } catch (\Exception $e) {
            $this->logger->error("Failed to check categories for object " . $e->getMessage());
            return false;
        }
    }

    public function categoryExistsForEntity($categoryId, $entityId) {	
		try {
            $sql = '
            SELECT * 
            FROM capstone_project_category, capstone_project_category_for 
			WHERE capstone_project_category_for.cpcf_cpc_id = capstone_project_category.cpc_id
			AND capstone_project_category.cpc_id = :categoryId
			AND capstone_project_category_for.cpcf_entity_id = :entityId
            ';
            $params = array(
                ':categoryId' => $categoryId, 
                ':entityId' => $entityId);
            $results = $this->conn->query($sql, $params);

			if (\count($results) == 0) {
					return false;
			}
			
			return true;
            
        } catch (\Exception $e) {
            $this->logger->error("Failed to check categories entity for object " . $e->getMessage());
            return false;
        }
    }

    public function addCategory($category) {
        try {
            $sql = '
            INSERT INTO capstone_project_category VALUES (
                NULL,
                :category,
            )';
            $params = array(
                ':category' => $category
            );
            $this->conn->execute($sql, $params);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to add new category: ' . $e->getMessage());
            return false;
        }
    }

    public function addCategoryInJoinTable($categoryId, $entityId) {
        try {
            $sql = '
            INSERT INTO capstone_project_category_for (cpcf_entity_id, cpcf_cpc_id) VALUES (
                :entityId,
				:categoryId
            )';
            $params = array(
                ':categoryId' => $categoryId,
                ':entityId' => $entityId
            );
            $this->conn->execute($sql, $params);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to add new category in join table: ' . $e->getMessage());
            return false;
        }
    }

    public function removeCategoryInJoinTable($categoryId, $entityId) {
        try {
            $sql = '
            DELETE FROM capstone_project_category_for 
            WHERE capstone_project_category_for.cpcf_entity_id = :entityId
            AND capstone_project_category_for.cpcf_cpc_id = :categoryId
            ';
            $params = array(
                ':entityId' => $entityId,
                ':categoryId' => $categoryId
            );
            $this->conn->execute($sql, $params);

            return true;
        } catch (\Excception $e) {
            $this->logger->error('Failed to delete a category from join table' . $e->getMessage());
            return false;
        }
    }

    public function removeAllCategoriesForEntity($entityId){
        try {
           $sql = '
           DELETE FROM capstone_project_category_for
           WHERE capstone_project_category_for.cpcf_entity_id = :entityId
           ';
           $params = array(
               ':entityId' => $entityId
           );
           $this->conn->execute($sql, $params);

           return true;
       } catch (\Exception $e) {
           $this->logger->error('Failed to delete all categoriess in join table: ' . $e->getMessage());
           return false;
        }
    }

    public static function ExtractCategoryFromRow($row){
        $category = new Category($row['cpc_id']);
        $category->setName($row['cpc_name']);
        return $category;
    } 
    

}


?>