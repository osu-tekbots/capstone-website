<?php

include_once '../bootstrap.php';

use DataAccess\CapstoneProjectsDao;
use DataAccess\KeywordsDao;
use Util\Security;

$projectsDao = new CapstoneProjectsDao($dbConn, $logger);
$keywordsDao = new KeywordsDao($dbConn, $logger);


$data = '';

function EscapeForCSV($value)
{
  return '"' . str_replace('"', '""', $value) . '"';
}

if(isset($_POST["exportApprovedProjects"]))
{
	$projects = $projectsDao->getAllApprovedCapstoneProjects();
	if(count($projects) > 0) {

		$data = "Proposer". "," ."Affiliation". "," ."Email". "," . "Phone". "," ."Additional Emails". "," . "Title". "," ."Focus". "," ."Description". "," ."Motivation". "," ."Category". "," ."Objectives". "," ."keyword list". "," . "min qualifications". "," ."pref qualifications". "," ."nda". "," ."website". "," ."video". "," ."special comments". "\n";
		$fileName='approvedprojects_'.date('m-d-Y_hia').'.csv';
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
			$proposerAffiliation = Security::HtmlEntitiesEncode($p->getProposer()->getAffiliation());
				
			$data .= EscapeForCSV($name). "," .EscapeForCSV($proposerAffiliation). "," .EscapeForCSV($proposerEmail). "," .EscapeForCSV($proposerPhone). "," .EscapeForCSV($additionalEmails). "," .EscapeForCSV($title). "," .EscapeForCSV($focus). "," .EscapeForCSV($description). "," .EscapeForCSV($motivation). "," .EscapeForCSV($category). "," .EscapeForCSV($objectives). "," .EscapeForCSV($keywordList). "," .EscapeForCSV($min_qualifications). "," .EscapeForCSV($pref_qualifications). "," .EscapeForCSV($nda). "," .EscapeForCSV($website). "," .EscapeForCSV($video). "," .EscapeForCSV($specialComments). "\n";

	
		}
		header('Content-Type: application/csv');
		header("Content-Disposition: attachment; filename=$fileName");
		echo $data; exit();
	}
}
else if(isset($_POST["exportAllProjects"]))
{
	$projects = $projectsDao->getCapstoneProjectsForAdmin();
	if(count($projects) > 0) {

		$data ="Status". "," ."Proposer". "," ."Affiliation". "," ."Email". "," . "Phone". "," ."Additional Emails". "," . "Title". "," ."Focus". "," ."Description". "," ."Motivation". "," ."Category". "," ."Objectives". "," ."keyword list". "," . "min qualifications". "," ."pref qualifications". "," ."nda". "," ."website". "," ."video". "," ."special comments". "\n";
		$fileName='allprojects_'.date('m-d-Y_hia').'.csv';
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
			$proposerAffiliation = Security::HtmlEntitiesEncode($p->getProposer()->getAffiliation());

				
			$data .= EscapeForCSV($status). "," .EscapeForCSV($name). "," .EscapeForCSV($proposerAffiliation). "," .EscapeForCSV($proposerEmail). "," .EscapeForCSV($proposerPhone). "," .EscapeForCSV($additionalEmails). "," .EscapeForCSV($title). "," .EscapeForCSV($focus). "," .EscapeForCSV($description). "," .EscapeForCSV($motivation). "," .EscapeForCSV($category). "," .EscapeForCSV($objectives). "," .EscapeForCSV($keywordList). "," .EscapeForCSV($min_qualifications). "," .EscapeForCSV($pref_qualifications). "," .EscapeForCSV($nda). "," .EscapeForCSV($website). "," .EscapeForCSV($video). "," .EscapeForCSV($specialComments). "\n";

	
		}
		header('Content-Type: application/csv');
		header("Content-Disposition: attachment; filename=$fileName");
		echo $data; exit();
	}
}


?>