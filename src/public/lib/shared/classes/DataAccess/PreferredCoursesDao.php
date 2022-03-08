<?php
namespace DataAccess;

use Model\PreferredCourse;

class PreferredCoursesDao{
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

    public function getAllPreferredCourses() {
        try {
            $sql = '
            SELECT * 
            FROM capstone_pref_course
            ';

            $results = $this->conn->query($sql);

            $preferredCourses = array();
            foreach ($results as $row) {
                $p = self::ExtractPreferredCourseFromRow($row, true);
                $preferredCourses[] = $p;
            }

            return $preferredCourses;
        } catch (\Exception $e) {
            $this->logger->error('Failed to get many preferred courses: ' . $e->getMessage());
            return false;
        }
    }

    public function getPreferredCoursesForEntity($entityId) {
        try {
            $sql = '
            SELECT * 
            FROM capstone_pref_course, capstone_pref_course_for
			WHERE capstone_pref_course_for.cpcf_cpc_id = capstone_pref_course.cpc_id
			AND capstone_pref_course_for.cpcf_entity_id = :entityId
            ';
            $params = array(':entityId' => $entityId);
            $results = $this->conn->query($sql, $params);

            $preferredCourses = array();
            foreach ($results as $row) {
                $p = self::ExtractPreferredCourseFromRow($row, true);
                $preferredCourses[] = $p;
            }
           
            return $preferredCourses;
        } catch (\Exception $e) {
            $this->logger->error("Failed to get preferred courses for object " . $e->getMessage());
            return false;
        }
    }

    public function getPreferredCourseByName($name) {
        try {
            $sql = '
            SELECT * 
            FROM capstone_pref_course
			WHERE capstone_pref_course.cpc_name = :name
            ';
            $params = array(':name' => $name);
            $results = $this->conn->query($sql, $params);
			
			if (\count($results) == 0) {
                return false;
            }

			return self::ExtractPreferredCourseFromRow($results[0], true);
        } catch (\Exception $e) {
            $this->logger->error("Failed to get preferred course for object " . $e->getMessage());
            return false;
        }
    }

    public function getPreferredCourseByCode($code) {
        try {
            $sql = '
            SELECT * 
            FROM capstone_pref_course
			WHERE capstone_pref_course.cpc_code = :code
            ';
            $params = array(':code' => $code);
            $results = $this->conn->query($sql, $params);
			
			if (\count($results) == 0) {
                return false;
            }

			return self::ExtractPreferredCourseFromRow($results[0], true);
        } catch (\Exception $e) {
            $this->logger->error("Failed to get preferred course for object " . $e->getMessage());
            return false;
        }
    }

    public function getPreferredCourseById($id) {
        try {
            $sql = '
            SELECT * 
            FROM capstone_pref_course
			WHERE capstone_pref_course.cpc_id = :id
            ';
            $params = array(':id' => $id);
            $results = $this->conn->query($sql, $params);
			
			if (\count($results) == 0) {
                return false;
            }

			return self::ExtractPreferredCourseFromRow($results[0], true);
        } catch (\Exception $e) {
            $this->logger->error("Failed to get preferred course for object " . $e->getMessage());
            return false;
        }
    }

    // May delete  
    public function preferredCourseExists($preferredCourse) {
        try {
            $sql = '
            SELECT * 
            FROM capstone_pref_course
			WHERE capstone_pref_course.cc_name = :preferredCourse
            ';
            $params = array(':preferredCourse' => $preferredCourse);
            $results = $this->conn->query($sql, $params);

			if (\count($results) == 0) {
					return false;
			}
			
			return true;
            
        } catch (\Exception $e) {
            $this->logger->error("Failed to check preferred course for object " . $e->getMessage());
            return false;
        }
    }

    public function preferredCourseExistsForEntity($preferredCourseId, $entityId) {
        try {
            $sql = '
            SELECT * 
            FROM capstone_pref_course, capstone_pref_course_for 
			WHERE capstone_pref_course_for.cpcf_cpc_id = capstone_pref_course.cpc_id
			AND capstone_pref_course.cpc_id = :preferredCourseId
			AND capstone_pref_course_for.cpcf_entity_id = :entityId
            ';
            $params = array(
                ':preferredCourseId' => $preferredCourseId, 
                ':entityId' => $entityId);
            $results = $this->conn->query($sql, $params);

			if (\count($results) == 0) {
					return false;
			}
			
			return true;
            
        } catch (\Exception $e) {
            $this->logger->error("Failed to check preferred course entity for object " . $e->getMessage());
            return false;
        }
    }

    public function addPreferredCourse($preferredCourse) {
        try {
            $sql = '
            INSERT INTO capstone_pref_course VALUES (
                NULL,
                :preferredCourse,
            )';
            $params = array(
                ':preferredCourse' => $preferredCourse
            );
            $this->conn->execute($sql, $params);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to add new preferred course: ' . $e->getMessage());
            return false;
        }
    }

    public function addPreferredCourseInJoinTable($preferredCourseId, $entityId) {
        try {
            $sql = '
            INSERT INTO capstone_pref_course_for VALUES (
                :preferredCourseId,
				:entityId
            )';
            $params = array(
                ':preferredCourseId' => $preferredCourseId,
                ':entityId' => $entityId
            );
            $this->conn->execute($sql, $params);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to add new preferred course in join table: ' . $e->getMessage());
            return false;
        }
    }

    public function removePreferredCourseInJoinTable($preferredCourseId, $entityId) {
        try {
            $sql = '
            DELETE FROM capstone_pref_course_for 
            WHERE capstone_pref_course_for.ccf_entity_id = :entityId
            AND capstone_pref_course_for.ccf_cc_id = :preferredCourseId
            ';
            $params = array(
                ':entityId' => $entityId,
                ':preferredCourseId' => $preferredCourseId
            );
            $this->conn->execute($sql, $params);

            return true;
        } catch (\Excception $e) {
            $this->logger->error('Failed to delete a preferred course from join table' . $e->getMessage());
            return false;
        }
    }

    public function removeAllPreferredCoursesForEntity($entityId){
        try {
           $sql = '
           DELETE FROM capstone_pref_course_for
           WHERE capstone_pref_course_for.cpcf_entity_id = :entityId
           ';
           $params = array(
               ':entityId' => $entityId
           );
           $this->conn->execute($sql, $params);

           return true;
       } catch (\Exception $e) {
           $this->logger->error('Failed to delete all preferred courses in join table: ' . $e->getMessage());
           return false;
        }
    }

    public static function ExtractPreferredCourseFromRow($row){
        $preferredCourse = new PreferredCourse($row['cpc_id']);
        $preferredCourse->setCode($row['cpc_code']);
        $preferredCourse->setName($row['cpc_name']);
        return $preferredCourse;
    } 
    

}


?>

