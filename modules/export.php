<?php

include_once '../bootstrap.php';

use DataAccess\CapstoneProjectsDao;
use DataAccess\KeywordsDao;
use Util\Security;

$projectsDao = new CapstoneProjectsDao($dbConn, $logger);
$keywordsDao = new KeywordsDao($dbConn, $logger);

$output = '';
if(isset($_POST["exportProjects"]))
{
	$projects = $projectsDao->getBrowsableCapstoneProjects();
	if(count($projects) > 0) {
		$count = 1;
		$output .= '
		<table class="table" bordered="1">  
		<tr>  
			<th>#</th>  
			<th>Proposer</th>  
			<th>Proposer Email</th>
			<th>Proposer Phone</th>  
			<th>Additional Emails</th>
			<th>Title</th>
			<th>Focus</th>
			<th>Description</th>
			<th>Motivation</th>
			<th>Objectives/Deliverables</th>
			<th>Keywords</th>
			<th>Minimum Qualifications</th>
			<th>Preferred Qualifications</th>
			<th>NDA/IP Status</th>
			<th>Website</th>
			<th>Video</th>
			<th>Special Comments</th>

	
		</tr>
		';
		foreach ($projects as $p) {
			$proposer = $p->getProposerId();
			$pid = $p->getId();
			$preexistingKeywords = $keywordsDao->getKeywordsForEntity($pid);
			$keywordList = '';
			$keywordTotal = count($preexistingKeywords);
			$currentTotal = 1;
			if($preexistingKeywords){
				foreach ($preexistingKeywords as $k) {
					if (trim($k->getName()) != '') {
						$keywordList .= $k->getName();
						if ($currentTotal != $keywordTotal){
							$keywordList .= ', ';
						}
					}
					$currentTotal++;
				}
			}

			$title = Security::HtmlEntitiesEncode($p->getTitle());
			$status = $p->getStatus()->getName();
			$type = $p->getType()->getName();
			$focus = $p->getFocus()->getName();
			$year = $p->getDateCreated()->format('Y');
			$website = $p->getWebsiteLink();
			$video = $p->getVideoLink();
			$start_by = $p->getDateStart()->format('F j, Y');
			$complete_by = $p->getDateEnd()->format('F j, Y');
			$pref_qualifications = Security::HtmlEntitiesEncode($p->getPreferredQualifications());
			$min_qualifications = Security::HtmlEntitiesEncode($p->getMinQualifications());
			$motivation = Security::HtmlEntitiesEncode($p->getMotivation());
			$description = Security::HtmlEntitiesEncode($p->getDescription());
			$objectives = Security::HtmlEntitiesEncode($p->getObjectives());
			$nda = $p->getNdaIp()->getName();
			$compensation = $p->getCompensation()->getName();
			$images = $p->getImages();
			$is_hidden = $p->getIsHidden();
			$category = $p->getCategory()->getName();
			$comments = Security::HtmlEntitiesEncode($p->getProposerComments());
			$name = Security::HtmlEntitiesEncode($p->getProposer()->getFirstName()) 
				. ' ' 
				. Security::HtmlEntitiesEncode($p->getProposer()->getLastName());
			$specialComments = Security::HtmlEntitiesEncode($p->getProposerComments());
			$additionalEmails = Security::HtmlEntitiesEncode($p->getAdditionalEmails());
			$proposerEmail = Security::HtmlEntitiesEncode($p->getProposer()->getEmail());
			$proposerPhone = Security::HtmlEntitiesEncode($p->getProposer()->getPhone());
				
			$output .= "
			<tr>  
				<td>$count</td>  
				<td>$name</td>  
				<td>$proposerEmail</td>  
				<td>$proposerPhone</td>  
				<td>$additionalEmails</td>
				<td>$title</td>
				<td>$focus</td>
				<td>$description</td>
				<td>$motivation</td>
				<td>$objectives</td>
				<td>$keywordList</td>
				<td>$min_qualifications</td>
				<td>$pref_qualifications</td>
				<td>$nda</td>
				<td>$website</td>
				<td>$video</td>
				<td>$specialComments</td>
			</tr>
			";
			$count++;
		}
		$output .= '</table>';
		header('Content-Type: application/xls; charset=utf-8');
		header('Content-Disposition: attachment; filename=projects.xls');
		header("Pragma: no-cache");
        header("Expires: 0");
		echo $output;
	}
}


?>