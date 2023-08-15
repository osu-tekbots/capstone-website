<?php
include_once '../bootstrap.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use DataAccess\KeywordsDao;

if (!session_id()) session_start();

include_once PUBLIC_FILES . '/lib/shared/authorize.php';

$isAdmin = isset($_SESSION['userID']) && !empty($_SESSION['userID']) 
	&& isset($_SESSION['accessLevel']) && $_SESSION['accessLevel'] == 'Admin';

allowIf($isAdmin);

$keywordsDao = new KeywordsDao($dbConn, $logger);

$title = 'Admin Keywords';
$css = array(
    'assets/css/sb-admin.css'
);
include_once PUBLIC_FILES . '/modules/header.php';

?>
<br/>
<div id="page-top">

	<div id="wrapper">

		<!-- Sidebar -->
		<ul class="sidebar navbar-nav" style="min-height: calc(100vh - 231px)">
			<li class="nav-item">
				<a class="nav-link" href="pages/adminInterface.php">
					<i class="fas fa-fw fa-tachometer-alt"></i>
					<span>Dashboard</span>
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="pages/adminProject.php">
					<i class="fas fa-fw fa-chart-area"></i>
					<span>Active Projects</span></a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="pages/adminProject.php?archive">
					<i class="fas fa-fw fa-chart-area"></i>
					<span>Archived Projects</span></a>
			</li>
            <li class="nav-item">
                <a class="nav-link" href="pages/adminUser.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Active Users</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pages/adminUser.php?inactive">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Inactive Users</span></a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" href="pages/adminApplication.php">
                    <i class="fas fa-fw fa-file-invoice"></i>
                    <span>Applications</span></a>
            </li> -->
            <li class="nav-item">
                <a class="nav-link" href="pages/adminCourses.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Course Listings</span></a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="pages/adminKeywords.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Keywords</span></a>
            </li>
		</ul>

		<div id="content-wrapper" style="padding-bottom: 0">

			<div class="container-fluid" style="position: relative;">

				<!-- Breadcrumbs-->
				<ol class="breadcrumb">
					<li class="breadcrumb-item">
						<a>Dashboard</a>
					</li>
					<li class="breadcrumb-item active">Keywords</li>
				</ol>
                
                <button class="btn btn-outline-primary" style="position: relative; left: 50%; transform: translate(-50%); margin-bottom: 10px" onclick="mergeSelected()">Merge Selected Keywords</button>

                <div class="tableWrapper" style="width: fit-content; margin-left: 50%; transform: translate(-50%); height: calc(100vh - 225px); overflow: scroll">
                    <div class="tableHead" style="background: lightgreen">Approved Keywords</div>
                        <?php
                            $keywords = $keywordsDao->adminGetApprovedKeywords();

                            $countUp = 0;
                            foreach($keywords as $keyword) {
                                echo '<div id="keyword'.$keyword->getId().'" class="tableRow" style="flex-wrap: nowrap;">';
                                echo '<input name="mergeCheck" type="checkbox" style="width: 16px; height: 16px; margin-right: 5px">';
                                echo '<input id="keywordText'.$keyword->getId().'" type="text" value="'.htmlspecialchars($keyword->getName()).'">';
                                echo '<p style="width: 150px; margin: .5rem 0">'.$keywordsDao->adminGetNumberUsedIn($keyword->getId()).' Projects Use</p>';
                                echo '<button class="btn btn-outline-danger unapproveKeyword" onclick="updateApproval('.$keyword->getId().', false)">Unapprove</button>';
                                echo '<button class="btn btn-outline-warning editKeyword" onclick="editKeyword('.$keyword->getId().')">Update</button>';
                                echo '<button class="btn btn-outline-danger deleteKeyword" onclick="removeKeyword('.$keyword->getId().')">Delete</button>';
                                echo '</div>';
                                $countUp++;
                            }
                        ?>

                    <div class="tableHead" style="margin-top: 10px; border-top: 2px solid #777; top: -2px; min-height: 37px; background: #f3f38b">User-Added Keywords</div>
                        <?php
                            $keywords = $keywordsDao->adminGetUnapprovedKeywords();

                            $countUp = 0;
                            foreach($keywords as $keyword) {
                                echo '<div id="keyword'.$keyword->getId().'" class="tableRow" style="flex-wrap: nowrap;">';
                                echo '<input name="mergeCheck" type="checkbox" style="width: 16px; height: 16px; margin-right: 5px">';
                                echo '<input id="keywordText'.$keyword->getId().'" type="text" value="'.htmlspecialchars($keyword->getName()).'">';
                                echo '<p style="width: 150px; margin: .5rem 0">'.$keywordsDao->adminGetNumberUsedIn($keyword->getId()).' Projects Use</p>';
                                echo '<button class="btn btn-outline-success approveKeyword" onclick="updateApproval('.$keyword->getId().', true)">Approve</button>';
                                echo '<button class="btn btn-outline-warning editKeyword" onclick="editKeyword('.$keyword->getId().')">Update</button>';
                                echo '<button class="btn btn-outline-danger deleteKeyword" onclick="removeKeyword('.$keyword->getId().')">Delete</button>';
                                echo '</div>';
                                $countUp++;
                            }
                        ?>
                </div>
			</div>
		</div>
	</div>
</div>

<!-- <template id="modifyApproved">
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 5px;">
        <button class="btn btn-outline-warning editKeyword">Edit</button>
        <button class="btn btn-outline-danger unapproveKeyword">Unapprove</button>
        <button class="btn btn-outline-danger deleteKeyword">Delete</button>
    </div>
</template>

<template id="modifyUnapproved">
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 5px;">
        <button class="btn btn-outline-success approveKeyword">Approve</button>
        <button class="btn btn-outline-warning editKeyword">Edit</button>
        <button class="btn btn-outline-danger deleteKeyword">Delete</button>
    </div>
</template> -->

<script>

// function showModificationApproved(keywordId) {
//     let appendDiv = document.getElementById('keyword'+keywordId);
//     let modDiv = document.getElementById('mod'+keywordId);

//     try {
//         if(!modDiv) {
//             let template = document.getElementById('modifyApproved');
//             let clone = template.content.cloneNode(true);
        
//             clone.firstElementChild.id = 'mod'+keywordId;
//             clone.querySelector('.unapproveKeyword').addEventListener('click', () => updateApproval(keywordId, false));
//             clone.querySelector('.editKeyword').addEventListener('click', () => editKeyword(keywordId));
//             clone.querySelector('.deleteKeyword').addEventListener('click', () => removeKeyword(keywordId));
//             appendDiv.appendChild(clone);
//         } else {
//             appendDiv.removeChild(modDiv);
//         }
//     } catch (err) {
//         console.error(err);
//     }
// }

// function showModificationUnapproved(keywordId) {
//     let appendDiv = document.getElementById('keyword'+keywordId);
//     let modDiv = document.getElementById('mod'+keywordId);

//     try {
//         if(!modDiv) {
//             let template = document.getElementById('modifyUnapproved');
//             let clone = template.content.cloneNode(true);
        
//             clone.firstElementChild.id = 'mod'+keywordId;
//             clone.querySelector('.approveKeyword').addEventListener('click', () => updateApproval(keywordId, true));
//             clone.querySelector('.editKeyword').addEventListener('click', () => editKeyword(keywordId));
//             clone.querySelector('.deleteKeyword').addEventListener('click', () => removeKeyword(keywordId));
//             appendDiv.appendChild(clone);
//         } else {
//             appendDiv.removeChild(modDiv);
//         }
//     } catch (err) {
//         console.error(err);
//     }
// }

function mergeSelected() {
    // Getting selected boxes credit: https://stackoverflow.com/a/46015793/21684315
    let selectedBoxes = document.querySelectorAll('input[type=checkbox][name=mergeCheck]:checked');

    if(selectedBoxes.length < 2) return;
    
    let keywordIds = [];
    let listKeywords = '';

    selectedBoxes.forEach(box => {
        let id = box.parentElement.id.substr(7);
        keywordIds.push(id);
        listKeywords += '\n- ' + document.getElementById('keywordText'+id).value;
    });


    if(confirm(`Are you sure you want to merge ${keywordIds.length} keywords?${listKeywords}`)) {
        let body = {
            action: 'mergeKeywords',
            keywordIds: keywordIds
        }

        api.post('/keywords.php', body).then(res => {
                // Reload in case the edit should move the keyword
                setTimeout(() => {location.reload()}, 3000);
                snackbar(res.message, 'success');
            })
            .catch(err => {
                snackbar(err.message, 'error');
            });
    }
}

function updateApproval(keywordId, approved) {
    let keywordText = document.getElementById('keywordText'+keywordId).value;

    let confirmation = 'Are you sure you want to ' + (approved ? 'approve' : 'unapprove') + ' this keyword ('+keywordText+')? ';
    confirmation += (approved ? 
        'Approved keywords show up in keyword autocomplete when editing projects and in the filter options for browsing projects.' : 
        'Unapproved keywords do not show up in keyword autocomplete when editing projects or in the filter options for browsing projects.');

    if(confirm(confirmation)) {
        let body = {
            action: 'updateApproval',
            keywordId: keywordId,
            approved: approved
        };

        api.post('/keywords.php', body).then(res => {
                // Move keyword to correct section
                setTimeout(() => {location.reload()}, 3000);
                snackbar(res.message, 'success');
            })
            .catch(err => {
                snackbar(err.message, 'error');
            });
    }
}

function editKeyword(keywordId) {
    let keywordText = document.getElementById('keywordText'+keywordId).value;

    if(!keywordText) return;

    let body = {
        action: 'editKeyword',
        keywordId: keywordId,
        keywordText: keywordText
    };

    api.post('/keywords.php', body).then(res => {
            // Reload in case the edit should move the keyword
            setTimeout(() => {location.reload()}, 3000);
            snackbar(res.message, 'success');
        })
        .catch(err => {
            snackbar(err.message, 'error');
        });
}

function removeKeyword(keywordId) {
    let keywordText = document.getElementById('keywordText'+keywordId).value;
    
    if(confirm('Are you sure you want to delete this keyword ('+keywordText+')? This keyword will be permanently removed from all projects.')) {
        let body = {
            action: 'removeKeyword',
            keywordId: keywordId
        };
    
        api.post('/keywords.php', body).then(res => {
                document.getElementById('keyword'+keywordId).remove();
                snackbar(res.message, 'success');
            })
            .catch(err => {
                snackbar(err.message, 'error');
            });
    }
}

</script>

<?php 
include_once PUBLIC_FILES . '/modules/footer.php' ; 
?>