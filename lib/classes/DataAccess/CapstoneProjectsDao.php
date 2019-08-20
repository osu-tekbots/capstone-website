<?php
namespace DataAccess;

use Model\CapstoneProjectCategory;
use Model\CapstoneProjectCompensation;
use Model\CapstoneProjectCop;
use Model\CapstoneProjectFocus;
use Model\CapstoneProjectNDAIP;
use Model\CapstoneProjectStatus;
use Model\CapstoneProjectType;
use Model\CapstoneProject;
use Model\CapstoneProjectImage;

use DataAccess\KeywordsDao;

/**
 * Handles all of the logic related to queries on capstone project resources in the database.
 */
class CapstoneProjectsDao {

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

    /**
     * Fetches several capstone projects from a specified range.
     *
     * @param integer $offset the offset into the results to fetch
     * @param integer $limit the max number of results to fetch in this batch
     * @return \Model\CapstoneProject[]|boolean an array of projects on success, false otherwise
     */
    public function getBrowsableCapstoneProjects($offset = 0, $limit = -1) {
        try {
            $sql = '
            SELECT * 
            FROM capstone_project, capstone_project_compensation, capstone_project_category, capstone_project_type, 
                capstone_project_focus, capstone_project_cop, capstone_project_nda_ip, capstone_project_status, user
            WHERE cp_cpcmp_id = cpcmp_id AND cp_cpc_id = cpc_id AND cp_cpt_id = cpt_id 
                AND cp_cpf_id = cpf_id AND cp_cpcop_id = cpcop_id AND cp_cpni_id = cpni_id 
                AND cp_cps_id = cps_id AND cp_u_id = u_id AND cp_is_hidden = :hidden AND cp_cps_id = :status
                AND cp_archived = :archived
            ';
            // TODO: enable pagination with the offset and limit
            $params = array(
                ':hidden' => false, 
                ':status' => CapstoneProjectStatus::ACCEPTING_APPLICANTS,
                ':archived' => false);
            $results = $this->conn->query($sql, $params);

            $projects = array();
            foreach ($results as $row) {
                $project = self::ExtractCapstoneProjectFromRow($row, true);
                $this->getCapstoneProjectImages($project, true);
                $projects[] = $project;
            }

            return $projects;
        } catch (\Exception $e) {
            $this->logger->error('Failed to get many projects: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches capstone projects associated with a user.
     *
     * @param string $userId the ID of the user whose projects to fetch
     * @return \Model\CapstoneProject[]|boolean an array of projects on success, false otherwise
     */
    public function getCapstoneProjectsForUser($userId) {
        try {
            $sql = '
            SELECT * 
            FROM capstone_project, capstone_project_compensation, capstone_project_category, capstone_project_type, 
                capstone_project_focus, capstone_project_cop, capstone_project_nda_ip, capstone_project_status, user
            WHERE cp_cpcmp_id = cpcmp_id AND cp_cpc_id = cpc_id AND cp_cpt_id = cpt_id AND cp_cpf_id = cpf_id 
                AND cp_cpcop_id = cpcop_id AND cp_cpni_id = cpni_id AND cp_cps_id = cps_id AND cp_u_id = u_id 
                AND cp_u_id = :uid AND cp_archived = :archived
            ';
            $params = array(':uid' => $userId, ':archived' => false);
            $results = $this->conn->query($sql, $params);

            $projects = array();
            foreach ($results as $row) {
                $project = self::ExtractCapstoneProjectFromRow($row, true);
                $this->getCapstoneProjectImages($project, true);
                $projects[] = $project;
            }
           
            return $projects;
        } catch (\Exception $e) {
            $this->logger->error("Failed to get capstone project for user '$userId': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches capstone projects relevent for an admin to view.
     *
     * @return \Model\CapstoneProject[]|boolean an array of projects on success, false otherwise
     */
    public function getCapstoneProjectsForAdmin($userId) {
        try {
            $sql = '
            SELECT * 
            FROM capstone_project, capstone_project_compensation, capstone_project_category, capstone_project_type, 
                capstone_project_focus, capstone_project_cop, capstone_project_nda_ip, capstone_project_status, user
            WHERE cp_cpcmp_id = cpcmp_id AND cp_cpc_id = cpc_id AND cp_cpt_id = cpt_id AND cp_cpf_id = cpf_id 
                AND cp_cpcop_id = cpcop_id AND cp_cpni_id = cpni_id AND cp_cps_id = cps_id AND u_id = cp_u_id
               
            ';
          
            $results = $this->conn->query($sql);

            $projects = array();
            foreach ($results as $row) {
                $project = self::ExtractCapstoneProjectFromRow($row, true);
                $this->getCapstoneProjectImages($project, true);
                $projects[] = $project;
            }

            return $projects;
        } catch (\Exception $e) {
            $this->logger->error('Failed to get projects for admin: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches all the pending capstone projects for admins to review.
     *
     * @return \Model\CapstoneProject[]|boolean an array of the proejcts to reviewon success, false otherwise
     */
    public function getPendingCapstoneProjects() {
        try {
            $sql = '
            SELECT * 
            FROM capstone_project, capstone_project_compensation, capstone_project_category, capstone_project_type, 
                capstone_project_focus, capstone_project_cop, capstone_project_nda_ip, capstone_project_status, user
            WHERE cp_cpcmp_id = cpcmp_id AND cp_cpc_id = cpc_id AND cp_cpt_id = cpt_id AND cp_cpf_id = cpf_id 
                AND cp_cpcop_id = cpcop_id AND cp_cpni_id = cpni_id AND cp_cps_id = cps_id AND cp_cps_id = :status 
                AND u_id = cp_u_id AND cp_archived = :archived
            ';
            $params = array(
                ':status' => CapstoneProjectStatus::PENDING_APPROVAL, 
                ':archived' => false
            );
            $results = $this->conn->query($sql, $params);

            $projects = array();
            foreach ($results as $row) {
                $project = self::ExtractCapstoneProjectFromRow($row, true);
                $this->getCapstoneProjectImages($project, true);
                $projects[] = $project;
            }
           
            return $projects;
        } catch (\Exception $e) {
            $this->logger->error('Failed to get pending capstone projects: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches various statistical information about the types of capstone projects in the database and their status.
     * 
     * Currently the resulting returned associative array will have the following keys:
     * - projectsPending
     * - projectsNeedingCategoryPlacement
     *
     * @return mixed[]|boolean an associative array of statistics on success, false otherwise
     */
    public function getCapstoneProjectStats() {
        try {
            $sql = '
            SELECT (   
                SELECT COUNT(*) 
                FROM capstone_project 
                WHERE cp_cps_id = :pending AND cp_archived = :archived
            ) AS projectsPending,
                (
                SELECT COUNT(*) 
                FROM capstone_project 
                WHERE cp_cpc_id = :category AND cp_archived = :archived AND cp_cps_id > 1
            ) AS projectsNeedingCategoryPlacement
            ';
            $params = array(
                ':pending' => CapstoneProjectStatus::PENDING_APPROVAL,
                ':category' => CapstoneProjectCategory::NONE,
                ':archived' => false
            );
            $results = $this->conn->query($sql, $params);
            return $results[0];
        } catch (\Exception $e) {
            $this->logger->error('Failed to get capstone project statistics: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches projects related to the provided capstone project.
     *
     * @param \Model\CapstoneProject $project
     * @return \Model\CapstoneProject|boolean the projects on success, false otherwise
     */
    public function getRelatedCapstoneProjects($project) {
        return false;
    }

    /**
     * Fetches the capstone project with the provided ID
     *
     * @param string $id
     * @return \Model\CapstoneProject|boolean the project on success, false otherwise
     */
    public function getCapstoneProject($id) {
        try {
            // First fetch the project
            $sql = '
            SELECT * 
            FROM capstone_project, capstone_project_compensation, capstone_project_category,
                capstone_project_type, capstone_project_focus, capstone_project_cop, capstone_project_nda_ip,
                capstone_project_status, user 
            WHERE cp_cpcmp_id = cpcmp_id AND cp_cpc_id = cpc_id AND cp_cpt_id = cpt_id AND cp_cpf_id = cpf_id 
                AND cp_cpcop_id = cpcop_id AND cp_cpni_id = cpni_id AND cp_cps_id = cps_id AND cp_u_id = u_id 
                AND cp_id = :id AND cp_archived = :archived
            ';
            $params = array(':id' => $id, ':archived' => false);
            $results = $this->conn->query($sql, $params);
            if (\count($results) == 0) {
                return false;
            }

            $project =  self::ExtractCapstoneProjectFromRow($results[0], true);

            // Now fetch any images for the project
            $images = $this->getCapstoneProjectImages($project);
            if ($images) {
                $project->setImages($images);
            } else {
                $project->setImages(array());
            }

            return $project;
        } catch (\Exception $e) {
            $this->logger->error("Failed to fetch project with id '$id': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Adds a new capstone project entry into the database.
     *
     * @param \Model\CapstoneProject $project the project to add
     * @return boolean true if successful, false otherwise
     */
    public function addNewCapstoneProject($project) {
        try {
            $sql = '
            INSERT INTO capstone_project VALUES (
                :id,
                :uid,
                :title,
                :mot,
                :desc,
                :obj,
                :dstart,
                :dend,
                :minq,
                :prefq,
                :cmpid,
                :emails,
                :cpcid,
                :cptid,
                :cpfid,
                :cpcopid,
                :cpniid,
                :website,
                :video,
                :hidden,
                :comments,
                :cpsid,
                :archived,
                :dcreated,
                :dupdated
            )
            ';
            $params = array(
                ':id' => $project->getId(),
                ':uid' => $project->getProposer()->getId(),
                ':title' => $project->getTitle(),
                ':mot' => $project->getMotivation(),
                ':desc' => $project->getDescription(),
                ':obj' => $project->getObjectives(),
                ':dstart' => QueryUtils::FormatDate($project->getDateStart()),
                ':dend' => QueryUtils::FormatDate($project->getDateEnd()),
                ':minq' => $project->getMinQualifications(),
                ':prefq' => $project->getPreferredQualifications(),
                ':cmpid' => $project->getCompensation()->getId(),
                ':emails' => $project->getAdditionalEmails(),
                ':cpcid' => $project->getCategory()->getId(),
                ':cptid' => $project->getType()->getId(),
                ':cpfid' => $project->getFocus()->getId(),
                ':cpcopid' => $project->getCop()->getId(),
                ':cpniid' => $project->getNdaIp()->getId(),
                ':website' => $project->getWebsiteLink(),
                ':video' => $project->getVideoLink(),
                ':hidden' => $project->getIsHidden(),
                ':comments' => $project->getProposerComments(),
                ':cpsid' => $project->getStatus()->getId(),
                ':archived' => $project->getIsArchived(),
                ':dcreated' => QueryUtils::FormatDate($project->getDateCreated()),
                ':dupdated' => QueryUtils::FormatDate($project->getDateUpdated())
            );
            $this->conn->execute($sql, $params);
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to create new project: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates an existing capstone project entry into the database.
     *
     * @param \Model\CapstoneProject $project the project to update
     * @return boolean true if successful, false otherwise
     */
    public function updateCapstoneProject($project) {
        try {
            $sql = '
            UPDATE capstone_project SET
                cp_title = :title,
                cp_motivation = :mot,
                cp_description = :desc,
                cp_objectives = :obj,
                cp_date_start = :dstart,
                cp_date_end = :dend,
                cp_min_qual = :minq,
                cp_preferred_qual = :prefq,
                cp_cpcmp_id = :cmpid,
                cp_additional_emails = :emails,
                cp_cpc_id = :cpcid,
                cp_cpt_id = :cptid,
                cp_cpf_id = :cpfid,
                cp_cpcop_id = :cpcopid,
                cp_cpni_id = :cpniid,
                cp_website_link = :website,
                cp_video_link = :video,
                cp_is_hidden = :hidden,
                cp_proposer_comments = :comments,
                cp_cps_id = :cpsid,
                cp_archived = :archived,
                cp_date_updated = :dupdated
            WHERE cp_id = :id
            ';
            $params = array(
                ':id' => $project->getId(),
                ':title' => $project->getTitle(),
                ':mot' => $project->getMotivation(),
                ':desc' => $project->getDescription(),
                ':obj' => $project->getObjectives(),
                ':dstart' => QueryUtils::FormatDate($project->getDateStart()),
                ':dend' => QueryUtils::FormatDate($project->getDateEnd()),
                ':minq' => $project->getMinQualifications(),
                ':prefq' => $project->getPreferredQualifications(),
                ':cmpid' => $project->getCompensation()->getId(),
                ':emails' => $project->getAdditionalEmails(),
                ':cpcid' => $project->getCategory()->getId(),
                ':cptid' => $project->getType()->getId(),
                ':cpfid' => $project->getFocus()->getId(),
                ':cpcopid' => $project->getCop()->getId(),
                ':cpniid' => $project->getNdaIp()->getId(),
                ':website' => $project->getWebsiteLink(),
                ':video' => $project->getVideoLink(),
                ':hidden' => $project->getIsHidden(),
                ':comments' => $project->getProposerComments(),
                ':cpsid' => $project->getStatus()->getId(),
                ':archived' => $project->getIsArchived(),
                ':dupdated' => QueryUtils::FormatDate($project->getDateUpdated())
            );
            $this->conn->execute($sql, $params);
            return true;
        } catch (\Exception $e) {
            $id = $project->getId();
            $this->logger->error("Failed to update project with id '$id': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Inserts metadata for a new image for a project in the databsae.
     *
     * @param \Model\CapstoneProjectImage $image the image metadata to insert into the database
     * @return boolean true on success, false otherwise
     */
    public function addNewCapstoneProjectImage($image) {
        try {
            $sql = '
            INSERT INTO capstone_project_image 
            (
                cpi_id, cpi_cp_id, cpi_name, cpi_is_default
            ) VALUES (
                :id,
                :pid,
                :name,
                :default
            )';
            $params = array(
                ':id' => $image->getId(),
                ':pid' => $image->getProject()->getId(),
                ':name' => $image->getName(),
                ':default' => $image->getIsDefault()
            );
            $this->conn->execute($sql, $params);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to add new image metadata: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches image metadata for an image associated with a project.
     * 
     * The image metadata will NOT include a reference to the project with which it is associated.
     *
     * @param string $id the ID of the image to fetch
     * @return \Model\CapstoneProjectImage the image on success, false otherwise
     */
    public function getCapstoneProjectImage($id) {
        try {
            $sql = 'SELECT * FROM capstone_project_image WHERE cpi_id = :id';
            $params = array(':id' => $id);
            $results = $this->conn->query($sql, $params);
            if (\count($results) == 0) {
                return false;
            }

            return self::ExtractCapstoneProjectImageFromRow($results[0]);
        } catch (\Exception $e) {
            $this->logger->error("Failed to fetch image metadata with id '$id': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates metadata about an existing capstone project image in the database.
     * 
     * The associated project MUST be set in the image. The provided image will be set as the default image
     * REGARDLESS of the value of its `$isDefault` property.
     *
     * @param \Model\CapstoneProjectImage $image the image metadata to persist
     * @return boolean true on success, false otherwise
     */
    public function updateCapstoneProjectDefaultImage($image) {
        try {
            $this->conn->startTransaction();
            // First unset the default
            $sql = 'UPDATE capstone_project_image SET cpi_is_default = 0 WHERE cpi_cp_id = :id';
            $params = array(':id' => $image->getProject()->getId());
            $this->conn->execute($sql, $params);

            // Now we can set the new default
            $sql = '
            UPDATE capstone_project_image SET
                cpi_is_default = :default
            WHERE cpi_id = :id
            ';
            $params = array(
                ':id' => $image->getId(),
                ':default' => true
            );
            $this->conn->execute($sql, $params);

            $this->conn->commit();

            return true;
        } catch (\Exception $e) {
            $this->conn->rollback();
            $iid = $image->getId();
            $this->logger->error("Failed to update image metadata with id '$iid': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches a list of categories for capstone projects
     *
     * @return \Model\CapstoneProjectCategory[]|boolean an array of categories on success, false otherwise
     */
    public function getCapstoneProjectCategories() {
        try {
            $sql = 'SELECT * FROM capstone_project_category';
            $results = $this->conn->query($sql);

            $categories = array();
            foreach ($results as $row) {
                $categories[] = self::ExtractCapstoneProjectCategoryFromRow($row);
            }

            return $categories;
        } catch (\Exception $e) {
            $this->logger->error('Failed to get project categories: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches a list of types for capstone projects
     *
     * @return \Model\CapstoneProjectType[]|boolean an array of types on success, false otherwise
     */
    public function getCapstoneProjectTypes() {
        try {
            $sql = 'SELECT * FROM capstone_project_type';
            $results = $this->conn->query($sql);

            $types = array();
            foreach ($results as $row) {
                $types[] = self::ExtractCapstoneProjectTypeFromRow($row);
            }
            return $types;
        } catch (\Exception $e) {
            $this->logger->error('Failed to get project types: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches a list of focuses for capstone projects
     *
     * @return \Model\CapstoneProjectFocus[]|boolean an array of focus values on success, false otherwise
     */
    public function getCapstoneProjectFocuses() {
        try {
            $sql = 'SELECT * FROM capstone_project_focus';
            $results = $this->conn->query($sql);

            $focuses = array();
            foreach ($results as $row) {
                $focuses[] = self::ExtractCapstoneProjectFocusFromRow($row);
            }

            return $focuses;
        } catch (\Exception $e) {
            $this->logger->error('Failed to get project focuses: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches a list of compensations for capstone projects
     *
     * @return \Model\CapstoneProjectCompensation[]|boolean an array of compensations on success, false otherwise
     */
    public function getCapstoneProjectCompensations() {
        try {
            $sql = 'SELECT * FROM capstone_project_compensation';
            $results = $this->conn->query($sql);

            $comps = array();
            foreach ($results as $row) {
                $comps[] = self::ExtractCapstoneProjectCompensationFromRow($row);
            }

            return $comps;
        } catch (\Exception $e) {
            $this->logger->error('Failed to get project compensations: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches a list of NDA/IP selection options for capstone projects
     *
     * @return \Model\CapstoneProjectCompensation[]|boolean an array of NDA/IP options on success, false otherwise
     */
    public function getCapstoneProjectNdaIps() {
        try {
            $sql = 'SELECT * FROM capstone_project_nda_ip';
            $results = $this->conn->query($sql);

            $nips = array();
            foreach ($results as $row) {
                $nips[] = self::ExtractCapstoneProjectNdaIpFromRow($row);
            }

            return $nips;
        } catch (\Exception $e) {
            $this->logger->error('Failed to get project NDA/IPs: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches the images related to a project
     *
     * @param \Model\CapstoneProject $project the project whose images to fetch
     * @param boolean $setImages determines whether the function will implicity set project images to the result of
     * the query.
     * @return \Model\CapstoneProjectImage[]|boolean an array if image metadata objects on success, false otherwise
     */
    public function getCapstoneProjectImages($project, $setImages = false) {
        try {
            $sql = 'SELECT * FROM capstone_project_image WHERE cpi_cp_id = :id';
            $params = array(':id' => $project->getId());
            $results = $this->conn->query($sql, $params);
            $images = array();
            foreach ($results as $r) {
                $image = self::ExtractCapstoneProjectImageFromRow($r);
                $image->setProject($project);
                $images[] = $image;
            }

            if ($setImages) {
                $project->setImages($images);
            }

            return $images;
        } catch (\Exception $e) {
            $pid = $project->getId();
            $this->logger->error("Failed to get image metadata for project with ID '$pid':" . $e->getMessage());
            return false;
        }
    }

    /**
     * Creates a new CapstoneProject object using information from the database row
     *
     * @param mixed[] $row the row in the database from which information is to be extracted
     * @return \Model\CapstoneProject
     */
    public static function ExtractCapstoneProjectFromRow($row, $userInRow = false) {
        $project = new CapstoneProject($row['cp_id']);
        $project->setProposerId($row['cp_u_id'])
            ->setTitle($row['cp_title'])
            ->setMotivation($row['cp_motivation'])
            ->setDescription($row['cp_description'])
            ->setObjectives($row['cp_objectives'])
            ->setDateStart(new \DateTime($row['cp_date_start']))
            ->setDateEnd(new \DateTime($row['cp_date_end']))
            ->setMinQualifications($row['cp_min_qual'])
            ->setPreferredQualifications($row['cp_preferred_qual'])
            ->setCompensation(self::ExtractCapstoneProjectCompensationFromRow($row, true))
            ->setAdditionalEmails($row['cp_additional_emails'])
            ->setCategory(self::ExtractCapstoneProjectCategoryFromRow($row, true))
            ->setType(self::ExtractCapstoneProjectTypeFromRow($row, true))
            ->setFocus(self::ExtractCapstoneProjectFocusFromRow($row, true))
            ->setCop(self::ExtractCapstoneProjectCopFromRow($row, true))
            ->setNdaIp(self::ExtractCapstoneProjectNdaIpFromRow($row, true))
            ->setWebsiteLink($row['cp_website_link'])
            ->setVideoLink($row['cp_video_link'])
            ->setIsHidden($row['cp_is_hidden'] ? true : false)
            ->setProposerComments($row['cp_proposer_comments'])
            ->setStatus(self::ExtractCapstoneProjectStatusFromRow($row, true))
            ->setIsArchived($row['cp_archived'] ? true : false)
            ->setDateCreated(new \DateTime($row['cp_date_created']))
            ->setDateUpdated(new \DateTime($row['cp_date_updated']));
        if ($userInRow) {
            $project->setProposer(UsersDao::ExtractUserFromRow($row));
        }
        return $project;
    }

    /**
     * Extracts information about an image for a capstone project from a row in a database result set.
     * 
     * The resulting CapstoneProjectImage does NOT have its reference to the project it belongs to set.
     *
     * @param mixed[] $row the row in the database result
     * @return \Model\CapstoneProjectImage the image extracted from the information
     */
    public static function ExtractCapstoneProjectImageFromRow($row) {
        $image = new CapstoneProjectImage($row['cpi_id']);
        $image->setName($row['cpi_name'])
            ->setIsDefault($row['cpi_is_default'] ? true : false)
            ->setIsProvided($row['cpi_is_provided'] ? true : false)
            ->setProvidedImageName($row['cpi_provided_image_name']);
        return $image;
    }

    /**
     * Create a CapstoneProjectCategory object using information from the database row
     *
     * @param mixed[] $row the database row to extract information from
     * @param boolean $projectInRow indicates whether the project is also included in the row
     * @return \Model\CapstoneProjectCategory
     */
    public static function ExtractCapstoneProjectCategoryFromRow($row, $projectInRow = false) {
        $id = $projectInRow ? 'cp_cpc_id' : 'cpc_id';
        $name = isset($row['cpc_name']) ? $row['cpc_name'] : null;
        return new CapstoneProjectCategory($row[$id], $name);
    }

    /**
     * Create a CapstoneProjectCompensation object using information from the database row
     *
     * @param mixed[] $row the database row to extract information from
     * @param boolean $projectInRow indicates whether the project is also included in the row
     * @return \Model\CapstoneProjectCompensation
     */
    public static function ExtractCapstoneProjectCompensationFromRow($row, $projectInRow = false) {
        $id = $projectInRow ? 'cp_cpcmp_id' : 'cpcmp_id';
        $name = isset($row['cpcmp_name']) ? $row['cpcmp_name'] : null;
        return new CapstoneProjectCompensation($row[$id], $name);
    }

    /**
     * Create a CapstoneProjectCop object using information from the database row
     *
     * @param mixed[] $row the database row to extract information from
     * @param boolean $projectInRow indicates whether the project is also included in the row
     * @return \Model\CapstoneProjectCop
     */
    public static function ExtractCapstoneProjectCopFromRow($row, $projectInRow = false) {
        $id = $projectInRow ? 'cp_cpcop_id' : 'cpcop_id';
        $name = isset($row['cpcop_name']) ? $row['cpcop_name'] : null;
        return new CapstoneProjectCop($row[$id], $name);
    }

    /**
     * Create a CapstoneProjectFocus object using information from the database row
     *
     * @param mixed[] $row the database row to extract information from
     * @param boolean $projectInRow indicates whether the project is also included in the row
     * @return \Model\CapstoneProjectFocus
     */
    public static function ExtractCapstoneProjectFocusFromRow($row, $projectInRow = false) {
        $id = $projectInRow ? 'cp_cpf_id' : 'cpf_id';
        $name = isset($row['cpf_name']) ? $row['cpf_name'] : null;
        return new CapstoneProjectFocus($row[$id], $name);
    }

    /**
     * Create a CapstoneProjectNdaIp object using information from the database row
     *
     * @param mixed[] $row the database row to extract information from
     * @param boolean $projectInRow indicates whether the project is also included in the row
     * @return \Model\CapstoneProjectNDAIP
     */
    public static function ExtractCapstoneProjectNdaIpFromRow($row, $projectInRow = false) {
        $id = $projectInRow ? 'cp_cpni_id' : 'cpni_id';
        $name = isset($row['cpni_name']) ? $row['cpni_name'] : null;
        return new CapstoneProjectNDAIP($row[$id], $name);
    }

    /**
     * Create a CapstoneProjectStatus object using information from the database row
     *
     * @param mixed[] $row the database row to extract information from
     * @param boolean $projectInRow indicates whether the project is also included in the row
     * @return \Model\CapstoneProjectStatus
     */
    public static function ExtractCapstoneProjectStatusFromRow($row, $projectInRow = false) {
        $id = $projectInRow ? 'cp_cps_id' : 'cps_id';
        $name = isset( $row['cps_name']) ?  $row['cps_name'] : null;
        return new CapstoneProjectStatus($row[$id], $name);
    }

    /**
     * Create a CapstoneProjectType object using information from the database row
     *
     * @param mixed[] $row the database row to extract information from
     * @param boolean $projectInRow indicates whether the project is also included in the row
     * @return \Model\CapstoneProjectType
     */
    public static function ExtractCapstoneProjectTypeFromRow($row, $projectInRow = false) {
        $id = $projectInRow ? 'cp_cpt_id' : 'cpt_id';
        $name = isset($row['cpt_name']) ? $row['cpt_name'] : null;
        return new CapstoneProjectType($row[$id], $name);
    }
}
