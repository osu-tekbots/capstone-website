<?php
include_once '../bootstrap.php';

use DataAccess\CapstoneProjectsDao;
use DataAccess\KeywordsDao;
use DataAccess\CategoriesDao;

$title = 'Browse Projects';
include_once PUBLIC_FILES . '/modules/header.php';
include_once PUBLIC_FILES . '/modules/cards.php';

$dao = new CapstoneProjectsDao($dbConn, $logger);
$keywordsDao = new KeywordsDao($dbConn, $logger);
$categoriesDao = new CategoriesDao($dbConn, $logger);

// if (isset($_REQUEST['category']))
// 	$projects = $dao->getBrowsableCapstoneProjectsByCategory($_REQUEST['category']);
// else
$projects = $dao->getBrowsableCapstoneProjects();


$categories = $categoriesDao->getAllCategories();
$types = $dao->getCapstoneProjectTypes();

?> 
<br /><br />
<div class="container-fluid">
    <h1>Browse Projects</h1>
    <nav class="navigation">
        <ul style="margin-bottom: 10px">
            <div class="col-sm-2">
                <br>
                <input class="form-control" id="filterInput" type="text" placeholder="Search..." />
                <br />
                <!-- <button type="button" style="float:right;" class="btn btn-outline-secondary">Search</button> -->
            </div>
            <div class="col-sm-2">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="NDAFilterCheckBox" onchange="toggleNDA();"/>
                    <label for="NDAFilterCheckBox">Hide projects that require NDA/IP agreements</label>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label for="keywordFilterSelect">Filter by Keyword</label>
                    <select class="form-control" id="keywordFilterSelect" onchange="filterSelectChanged(this)">
                        <option></option>
                        <?php
                        //Generate content for dropdown list.
                        $availableKeywords = $keywordsDao->getAllKeywords();
                        foreach ($availableKeywords as $k) {
                            echo '<option>' . $k->getName() . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label for="projectTypeFilterSelect">Filter by Course</label>
                    <select class="form-control" id="projectTypeFilterSelect" onchange="filterSelectChanged(this)">
                        <option></option>
                        <?php 
                        $categories = $categoriesDao->getAllCategories();
                        foreach ($categories as $c) {
                            echo '<option>' . $c->getName() . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

                <?php /*echo '<div class="form-group">
                    <label for="yearFilterSelect">Filter by Year</label>
                    <select class="form-control" id="yearFilterSelect" onchange="filterSelectChanged(this)">
                        <option></option>
                        <option>'. date('Y') . '</option>
                        <option>'. (date('Y') - 1) . '</option>
                        <option>'. (date('Y') - 2) . '</option>
                        <option>'. (date('Y') - 3) . '</option>
                        <option>'. (date('Y') - 4) . '</option>
                        <option>'. (date('Y') - 5) . ' and earlier</option>
                    </select>
                </div>';*/
            ?>
            

                <div class="col-sm-6">
                    Sort By...
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="custom-control custom-radio">
                                <input
                                    type="radio"
                                    id="sortTitleAscRadio"
                                    value="sortTitleAsc"
                                    name="sortRadio"
                                    class="custom-control-input"
                                />
                                <label class="custom-control-label" for="sortTitleAscRadio">Title (A..Z)</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input
                                    type="radio"
                                    id="sortTitleDescRadio"
                                    value="sortTitleDesc"
                                    name="sortRadio"
                                    class="custom-control-input"
                                />
                                <label class="custom-control-label" for="sortTitleDescRadio">Title (Z..A)</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="custom-control custom-radio">
                                <input
                                    type="radio"
                                    id="sortDateDescRadio"
                                    value="sortDateDesc"
                                    name="sortRadio"
                                    class="custom-control-input"
                                />
                                <label class="custom-control-label" for="sortDateDescRadio">Newest</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input
                                    type="radio"
                                    id="sortDateAscRadio"
                                    value="sortDateAsc"
                                    name="sortRadio"
                                    class="custom-control-input"
                                />
                                <label class="custom-control-label" for="sortDateAscRadio">Oldest</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </ul>

        <div class="col-sm-12 scroll jumbotron capstoneJumbotron">
            <div class="masonry" id="projectCardGroup">
                <?php
					// Render the cards to browser here
					renderProjectCardGroup($projects, $keywordsDao, $categoriesDao, true);
					?>
            </div>

       </div> 
    </nav>
</div>
<script type="text/javascript">

function toggleNDA(){
	
	var nonstockItems = document.getElementsByClassName('reqNDA');
	var checkBox = document.getElementById("NDAFilterCheckBox");
	
	if (checkBox.checked == true){
		for (var i = 0; i < nonstockItems.length; i ++) {
			nonstockItems[i].style.display = 'none';
		}
	} else {
		for (var i = 0; i < nonstockItems.length; i ++) {
			nonstockItems[i].style.display = '';
		}
	} 
		
}


    /*********************************************************************************
    * Function Name: strstr()
    * Description: Mimics strstr() php function that searches for the first occurence
    * of a string (needle) in another string (haystack).
    *********************************************************************************/
    function strstr(haystack, needle, bool) {
        var pos = 0;
        haystack += '';
        pos = haystack.toLowerCase().indexOf((needle + '').toLowerCase());
        if (pos == -1) {
            return false;
        } else {
            if (bool) {
                return haystack.substr(0, pos);
            } else {
                return haystack.slice(pos);
            }
        }
    }

    $(document).ready(function(){

      //As each letter is typed in filterInput, filtering of cards will occur.
      //For drop down lists, like filtering by key word, filterInput is programmically
      //filled and keydown behavior is explicitly called.
      $("#filterInput").on("keyup", function(){
    	var value = $(this).val().toLowerCase();

    	for(var i = 0; i < <?php echo $numCardsCreated; ?>; i++){
    		if($("#projectCard" + i).text().toLowerCase().indexOf(value) > -1){
    			$("#projectCard" + i).show();
    		}
    		else{
    			$("#projectCard" + i).hide();
    		}
    	}
      });

      //Fixme: Future Implementation, allow checkbox to be checked and user to
      //still filter additional options.
      $("#NDAFilterCheckBox").change(function(){
    	 if($(this).is(":checked")){
    		for(var i = 0; i < <?php echo $numCardsCreated; ?>; i++){
    			//-1 is returned by indexOf(String) if the String parameter passed in
    			//does not exist anywhere within the text. Otherwise, its index would
    			//be returned.
    			if($("#projectCard" + i).text().toLowerCase().indexOf("nda") > -1){
    				$("#projectCard" + i).hide();
    			}
    		}
    	 }
    	 else{
    		for(var i = 0; i < <?php echo $numCardsCreated; ?>; i++){
    			$("#projectCard" + i).show();
    		}
    	 }
      });
      

      //Performs sorting functionality based on which radio button is chosen.
    	$('input[name="sortRadio"]').change(function() {
    		switch ($(this).val()) {
    			case "sortTitleAsc":
    				var mylist = $('#projectCardGroup');
    				var listitems = mylist.children('div').get();
    				listitems.sort(function(a, b) {
    				   return $(a).text().toUpperCase().localeCompare($(b).text().toUpperCase());
    				});

    				$.each(listitems, function(index, item) {
    				   mylist.append(item);
    				});
    				break;
    			case "sortTitleDesc":
    				var mylist = $('#projectCardGroup');
    				var listitems = mylist.children('div').get();
    				listitems.sort(function(a, b) {
    				   return $(b).text().toUpperCase().localeCompare($(a).text().toUpperCase());
    				});

    				$.each(listitems, function(index, item) {
    				   mylist.append(item);
    				});
    				break;
    			case "sortDateAsc":
                    var mylist = $('#projectCardGroup');
    				var listitems = mylist.children('div').get();
    				listitems.sort(function(a, b) {
    				   return strstr($(a).text(), "Last Updated:").toUpperCase().localeCompare(strstr($(b).text(), "Last Updated:").toUpperCase());
    				});

    				$.each(listitems, function(index, item) {
    				   mylist.append(item);
    				});
    				break;
    			case "sortDateDesc":
    				var mylist = $('#projectCardGroup');
    				var listitems = mylist.children('div').get();
    				listitems.sort(function(a, b) {
    				   return strstr($(b).text(), "Last Updated:").toUpperCase().localeCompare(strstr($(a).text(), "Last Updated:").toUpperCase());
    				});

    				$.each(listitems, function(index, item) {
    				   mylist.append(item);
    				});
    				break;
    		};
    	});

        $("#sortTitleAscRadio").prop("checked", true);

    });

    function filterSelectChanged(filterObject){
    	var value = filterObject.value;
    	$("#filterInput").val(value);

    	//Manually trigger keydown to mimic keydown function feature.
    	//Attempted to programmically toggleProjectCard, but ran into
    	//logical bug 2/26/19.
        var e = jQuery.Event("keydown");
        e.which = 77;
        $("#filterInput").trigger(e);
    }
</script>

<?php
include_once PUBLIC_FILES . '/modules/footer.php';
?>
