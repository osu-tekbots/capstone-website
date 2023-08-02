<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
include_once '../bootstrap.php';

//function __autoload($class_name) {
//		$file = str_replace('\\', DIRECTORY_SEPARATOR, $class_name);      
//		require_once(dirname(__FILE__) . '/./includes/'.$file.'.php');
//	}
	
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use DataAccess\CapstoneProjectsDao;
use DataAccess\KeywordsDao;
use DataAccess\CategoriesDao;
use Util\Security;

$projectsDao = new CapstoneProjectsDao($dbConn, $logger);
$keywordsDao = new KeywordsDao($dbConn, $logger);
$categoriesDao = new CategoriesDao($dbConn, $logger);

$data = '';

function EscapeForCSV($value)
{
  return '"' . str_replace('"', '""', $value) . '"';
}

if(isset($_POST["exportApprovedProjects"]))
{
	$projects = $projectsDao->getAllApprovedCapstoneProjects();
	if(count($projects) > 0) {

		$data = "Proposer". "," ."Affiliation". "," ."Email". "," . "Phone". "," ."Additional Emails". "," . "Title". "," ."Focus". "," ."Description". "," ."Motivation". "," ."Objectives". "," ."keyword list". "," ."category list". "," . "min qualifications". "," ."pref qualifications". "," ."nda". "," ."number groups". "," ."website". "," ."video". "," ."special comments". "\n";
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
			$preexistingCategories = $categoriesDao->getCategoriesForEntity($pid);
			$categoryList = '';
			$categoryTotal = count($preexistingCategories);
			$currentTotal = 1;
			if($preexistingCategories){
				foreach ($preexistingCategories as $c) {
					if (trim($c->getName()) != '') {
						$categoryList .= $c->getName();
						if ($currentTotal != $categoryTotal){
							$categoryList .= ', ';
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
			$numberGroups = $p->getNumberGroups();
			$compensation = $p->getCompensation()->getName();
			$images = $p->getImages();
			$is_hidden = $p->getIsHidden();
			$comments = Security::HtmlEntitiesEncode($p->getProposerComments());
			$name = Security::HtmlEntitiesEncode($p->getProposer()->getFirstName()) 
				. ' ' 
				. Security::HtmlEntitiesEncode($p->getProposer()->getLastName());
			$specialComments = Security::HtmlEntitiesEncode($p->getProposerComments());
			$additionalEmails = Security::HtmlEntitiesEncode($p->getAdditionalEmails());
			$proposerEmail = Security::HtmlEntitiesEncode($p->getProposer()->getEmail());
			$proposerPhone = Security::HtmlEntitiesEncode($p->getProposer()->getPhone());
			$proposerAffiliation = Security::HtmlEntitiesEncode($p->getProposer()->getAffiliation());
				
			$data .= EscapeForCSV($name). "," .EscapeForCSV($proposerAffiliation). "," .EscapeForCSV($proposerEmail). "," .EscapeForCSV($proposerPhone). "," .EscapeForCSV($additionalEmails). "," .EscapeForCSV($title). "," .EscapeForCSV($focus). "," .EscapeForCSV($description). "," .EscapeForCSV($motivation). "," .EscapeForCSV($objectives). "," .EscapeForCSV($keywordList). "," .EscapeForCSV($categoryList). "," .EscapeForCSV($min_qualifications). "," .EscapeForCSV($pref_qualifications). "," .EscapeForCSV($nda). "," .EscapeForCSV($numberGroups). "," .EscapeForCSV($website). "," .EscapeForCSV($video). "," .EscapeForCSV($specialComments). "\n";

	
		}
		header('Content-Type: application/csv');
		header("Content-Disposition: attachment; filename=$fileName");
		echo $data; exit();
	}
}
else if(isset($_REQUEST["exportAllProjects"]))
{
	/*
	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	
	$filename = 'AllProjects.xlsx';
	
	$sheet->setCellValue('A1', "Date");
	$sheet->setCellValue('B1', "Status");
	$sheet->setCellValue('C1', "Proposer");
	$sheet->setCellValue('D1', "Affiliation");
	$sheet->setCellValue('E1', "Email");
	$sheet->setCellValue('F1', "Phone");
	$sheet->setCellValue('G1', "Additional Emails");
	$sheet->setCellValue('H1', "Title");
	$sheet->setCellValue('I1', "Focus");
	$sheet->setCellValue('J1', "Description");
	$sheet->setCellValue('K1', "Motivation");
	$sheet->setCellValue('M1', "Objectives");
	$sheet->setCellValue('N1', "Keywords");
	$sheet->setCellValue('O1', "Min Qualifications");
	$sheet->setCellValue('P1', "Preferred Qualifications");
	$sheet->setCellValue('Q1', "NDA");
	$sheet->setCellValue('R1', "Number of Groups");
	$sheet->setCellValue('S1', "Website");
	$sheet->setCellValue('T1', "Video");
	$sheet->setCellValue('U1', "Special Comments");
	$sheet->setCellValue('V1', "Sponsored");
	
	$projects = $projectsDao->getCapstoneProjectsForAdmin();
	$i = 2;
	foreach ($projects AS $p){
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
						$keywordList .= '; ';
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
		$numberGroups = $p->getNumberGroups();
		$compensation = $p->getCompensation()->getName();
		$images = $p->getImages();
		$is_hidden = $p->getIsHidden();
		$comments = Security::HtmlEntitiesEncode($p->getProposerComments());
		$name = Security::HtmlEntitiesEncode($p->getProposer()->getFirstName()) 
			. ' ' 
			. Security::HtmlEntitiesEncode($p->getProposer()->getLastName());
		$specialComments = Security::HtmlEntitiesEncode($p->getProposerComments());
		$additionalEmails = Security::HtmlEntitiesEncode($p->getAdditionalEmails());
		$proposerEmail = Security::HtmlEntitiesEncode($p->getProposer()->getEmail());
		$proposerPhone = Security::HtmlEntitiesEncode($p->getProposer()->getPhone());
		$proposerAffiliation = Security::HtmlEntitiesEncode($p->getProposer()->getAffiliation());
		$date_created = $p->getDateCreated()->format('F j, Y');
		$sponsored = ($p->getIsSponsored() ? 'Yes' : 'No' );
			
		$sheet->setCellValue('A'.$i, $date_create);
		$sheet->setCellValue('B'.$i, $status);
		$sheet->setCellValue('C'.$i, $name);
		$sheet->setCellValue('D'.$i, $proposerAffiliation);
		$sheet->setCellValue('E'.$i, $proposerEmail);
		$sheet->setCellValue('F'.$i, $proposerPhone);
		$sheet->setCellValue('G'.$i, $additionalEmails);
		$sheet->setCellValue('H'.$i, $title);
		$sheet->setCellValue('I'.$i, $focus);
		$sheet->setCellValue('J'.$i, $description);
		$sheet->setCellValue('K'.$i, $motivation);
		$sheet->setCellValue('M'.$i, $objectives);
		$sheet->setCellValue('N'.$i, $keywordList);
		$sheet->setCellValue('O'.$i, $min_qualifications);
		$sheet->setCellValue('P'.$i, $pref_qualifications);
		$sheet->setCellValue('Q'.$i, $nda);
		$sheet->setCellValue('R'.$i, $numberGroups);
		$sheet->setCellValue('S'.$i, $website);
		$sheet->setCellValue('T'.$i, $video);
		$sheet->setCellValue('U'.$i, $specialComments);
		$sheet->setCellValue('V'.$i, $sponsored);
		
		$i++;
	}
		
	$writer = new Xlsx($spreadsheet);
	//$writer->save('./uploads/hello world.xlsx');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.$filename.'"');
	header('Cache-Control: max-age=0');
	$writer->save('php://output');
	exit();
	*/
	
	$projects = $projectsDao->getCapstoneProjectsForAdmin();
	if(count($projects) > 0) {
		$data ="Date". "," . "Status". "," ."Proposer". "," ."Affiliation". "," ."Email". "," . "Phone". "," ."Additional Emails". "," . "Title". "," ."Focus". "," ."Description". "," ."Motivation". "," ."Objectives". "," ."keyword list". "," ."category list". "," . "min qualifications". "," ."pref qualifications". "," ."nda". ",". "number groups" ."," ."website". "," ."video". "," ."special comments"."," ."Sponsored". "\n";
		$fileName='allprojects_'.date('m-d-Y_hia').'.csv';
		foreach ($projects as $p) {
			if ($p->getIsArchived() == 0){
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
				
				$preexistingCategories = $categoriesDao->getCategoriesForEntity($pid);
				$categoryList = '';
				$categoryTotal = count($preexistingCategories);
				$currentTotal = 1;
				if($preexistingCategories){
					foreach ($preexistingCategories as $c) {
						if (trim($c->getName()) != '') {
							$categoryList .= $c->getName();
							if ($currentTotal != $categoryTotal){
								$categoryList .= ', ';
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
				$numberGroups = $p->getNumberGroups();
				$compensation = $p->getCompensation()->getName();
				$images = $p->getImages();
				$is_hidden = $p->getIsHidden();
				$comments = Security::HtmlEntitiesEncode($p->getProposerComments());
				$name = Security::HtmlEntitiesEncode($p->getProposer()->getFirstName()) 
					. ' ' 
					. Security::HtmlEntitiesEncode($p->getProposer()->getLastName());
				$specialComments = Security::HtmlEntitiesEncode($p->getProposerComments());
				$additionalEmails = Security::HtmlEntitiesEncode($p->getAdditionalEmails());
				$proposerEmail = Security::HtmlEntitiesEncode($p->getProposer()->getEmail());
				$proposerPhone = Security::HtmlEntitiesEncode($p->getProposer()->getPhone());
				$proposerAffiliation = Security::HtmlEntitiesEncode($p->getProposer()->getAffiliation());
				$date_created = $p->getDateCreated()->format('F j, Y');
				$sponsored = ($p->getIsSponsored() ? 'Yes' : 'No' );

					
				$data .= EscapeForCSV($date_created). "," . EscapeForCSV($status). "," .EscapeForCSV($name). "," .EscapeForCSV($proposerAffiliation). "," .EscapeForCSV($proposerEmail). "," .EscapeForCSV($proposerPhone). "," .EscapeForCSV($additionalEmails). "," .EscapeForCSV($title). "," .EscapeForCSV($focus). "," .EscapeForCSV($description). "," .EscapeForCSV($motivation). "," .EscapeForCSV($objectives). "," .EscapeForCSV($keywordList). "," .EscapeForCSV($categoryList). "," .EscapeForCSV($min_qualifications). "," .EscapeForCSV($pref_qualifications). "," .EscapeForCSV($nda). "," .EscapeForCSV($numberGroups). "," .EscapeForCSV($website). "," .EscapeForCSV($video). "," .EscapeForCSV($specialComments). "," .EscapeForCSV($sponsored). "\n";
			}
		}
		header('Content-Type: application/csv');
		header("Content-Disposition: attachment; filename=$fileName");
		echo $data; 
		exit();
	}
	
}
else if(isset($_POST["exportCreatedProjects"]))
{
	$projects = $projectsDao->getCreatedCapstoneProjects();
	if(count($projects) > 0) {

		$data = "Proposer". "," ."Affiliation". "," ."Email". "," . "Phone". "," ."Additional Emails". "," . "Title". "," ."Focus". "," ."Description". "," ."Motivation". "," ."Objectives". "," ."keyword list". "," ."category list". "," ."min qualifications". "," ."pref qualifications". "," ."nda". "," ."number groups". "," ."website". "," ."video". "," ."special comments". "\n";
		$fileName='nonsubmittedprojects_'.date('m-d-Y_hia').'.csv';
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

			$preexistingCategories = $categoriesDao->getCategoriesForEntity($pid);
			$categoryList = '';
			$categoryTotal = count($preexistingCategories);
			$currentTotal = 1;
			if($preexistingCategories){
				foreach ($preexistingCategories as $c) {
					if (trim($c->getName()) != '') {
						$categoryList .= $c->getName();
						if ($currentTotal != $categoryTotal){
							$categoryList .= ', ';
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
			$numberGroups = $p->getNumberGroups();
			$compensation = $p->getCompensation()->getName();
			$images = $p->getImages();
			$is_hidden = $p->getIsHidden();
			$comments = Security::HtmlEntitiesEncode($p->getProposerComments());
			$name = Security::HtmlEntitiesEncode($p->getProposer()->getFirstName()) 
				. ' ' 
				. Security::HtmlEntitiesEncode($p->getProposer()->getLastName());
			$specialComments = Security::HtmlEntitiesEncode($p->getProposerComments());
			$additionalEmails = Security::HtmlEntitiesEncode($p->getAdditionalEmails());
			$proposerEmail = Security::HtmlEntitiesEncode($p->getProposer()->getEmail());
			$proposerPhone = Security::HtmlEntitiesEncode($p->getProposer()->getPhone());
			$proposerAffiliation = Security::HtmlEntitiesEncode($p->getProposer()->getAffiliation());
				
			$data .= EscapeForCSV($name). "," .EscapeForCSV($proposerAffiliation). "," .EscapeForCSV($proposerEmail). "," .EscapeForCSV($proposerPhone). "," .EscapeForCSV($additionalEmails). "," .EscapeForCSV($title). "," .EscapeForCSV($focus). "," .EscapeForCSV($description). "," .EscapeForCSV($motivation). "," .EscapeForCSV($objectives). "," .EscapeForCSV($keywordList). "," .EscapeForCSV($categoryList). "," .EscapeForCSV($min_qualifications). "," .EscapeForCSV($pref_qualifications). "," .EscapeForCSV($nda). "," .EscapeForCSV($numberGroups). "," .EscapeForCSV($website). "," .EscapeForCSV($video). "," .EscapeForCSV($specialComments). "\n";

	
		}
		header('Content-Type: application/csv');
		header("Content-Disposition: attachment; filename=$fileName");
		echo $data; exit();
	}
}
else if(isset($_POST["exportPendingProjects"]))
{
	$projects = $projectsDao->getPendingCapstoneProjects();
	if(count($projects) > 0) {

		$data = "Proposer". "," ."Affiliation". "," ."Email". "," . "Phone". "," ."Additional Emails". "," . "Title". "," ."Focus". "," ."Description". "," ."Motivation". "," ."Objectives". "," ."keyword list". "," ."category list". "," . "min qualifications". "," ."pref qualifications". "," ."nda". "," ."number groups". "," ."website". "," ."video". "," ."special comments". "\n";
		$fileName='pendingapprovalprojects_'.date('m-d-Y_hia').'.csv';
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

			$preexistingCategories = $categoriesDao->getCategoriesForEntity($pid);
			$categoryList = '';
			$categoryTotal = count($preexistingCategories);
			$currentTotal = 1;
			if($preexistingCategories){
				foreach ($preexistingCategories as $c) {
					if (trim($c->getName()) != '') {
						$categoryList .= $c->getName();
						if ($currentTotal != $categoryTotal){
							$categoryList .= ', ';
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
			$numberGroups = $p->getNumberGroups();
			$compensation = $p->getCompensation()->getName();
			$images = $p->getImages();
			$is_hidden = $p->getIsHidden();
			$comments = Security::HtmlEntitiesEncode($p->getProposerComments());
			$name = Security::HtmlEntitiesEncode($p->getProposer()->getFirstName()) 
				. ' ' 
				. Security::HtmlEntitiesEncode($p->getProposer()->getLastName());
			$specialComments = Security::HtmlEntitiesEncode($p->getProposerComments());
			$additionalEmails = Security::HtmlEntitiesEncode($p->getAdditionalEmails());
			$proposerEmail = Security::HtmlEntitiesEncode($p->getProposer()->getEmail());
			$proposerPhone = Security::HtmlEntitiesEncode($p->getProposer()->getPhone());
			$proposerAffiliation = Security::HtmlEntitiesEncode($p->getProposer()->getAffiliation());
				
			$data .= EscapeForCSV($name). "," .EscapeForCSV($proposerAffiliation). "," .EscapeForCSV($proposerEmail). "," .EscapeForCSV($proposerPhone). "," .EscapeForCSV($additionalEmails). "," .EscapeForCSV($title). "," .EscapeForCSV($focus). "," .EscapeForCSV($description). "," .EscapeForCSV($motivation). "," .EscapeForCSV($objectives). "," .EscapeForCSV($keywordList). "," .EscapeForCSV($categoryList). "," .EscapeForCSV($min_qualifications). "," .EscapeForCSV($pref_qualifications). "," .EscapeForCSV($nda). "," .EscapeForCSV($numberGroups). "," .EscapeForCSV($website). "," .EscapeForCSV($video). "," .EscapeForCSV($specialComments). "\n";

	
		}
		header('Content-Type: application/csv');
		header("Content-Disposition: attachment; filename=$fileName");
		echo $data; exit();
	}
} else 
	echo "Something went wrong!";



?>