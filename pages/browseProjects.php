<?php
include_once '../bootstrap.php';

use DataAccess\CapstoneProjectsDao;
use DataAccess\KeywordsDao;

$title = 'Browse Projects';
include_once PUBLIC_FILES . '/modules/header.php';
include_once PUBLIC_FILES . '/modules/cards.php';

$dao = new CapstoneProjectsDao($dbConn, $logger);
$keywordsDao = new KeywordsDao($dbConn, $logger);

$projects = $dao->getBrowsableCapstoneProjects();

?> 
<br /><br />
<div class="container-fluid">
    <h1>Browse Projects</h1>
    <div class="row">
        <div class="col-sm-3">
            <h2>Search and Filter</h2>
            <div class="row">
                <div class="col-sm-12">
                    <input class="form-control" id="filterInput" type="text" placeholder="Search..." />
                    <br />
                    <button type="button" style="float:right;" class="btn btn-outline-secondary">Search</button>
                    <br /><br />
<!-- CHECKBOX HIDE IF PROJECTS REQUIRE NDA NOT FUNCTIONING
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="NDAFilterCheckBox" />
                        <label for="NDAFilterCheckBox">Hide projects that require an NDA/IP</label>
                    </div>
-->
                    <div class="form-group">
                        <label for="projectTypeFilterSelect">Filter by Keyword</label>
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

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="projectTypeFilterSelect">Filter by Project Type</label>
                        <select class="form-control" id="projectTypeFilterSelect" onchange="filterSelectChanged(this)">
                            <option></option>
                            <option>Capstone</option>
                            <option>Internship</option>
                            <option>Long Term</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="yearFilterSelect">Filter by Year</label>
                        <select class="form-control" id="yearFilterSelect" onchange="filterSelectChanged(this)">
                            <option></option>
                            <!-- Per user design, allow the user to filter through the last 5 years. -->
                            <option><?php echo date('Y'); ?></option>
                            <option><?php echo date('Y') - 1; ?></option>
                            <option><?php echo date('Y') - 2; ?></option>
                            <option><?php echo date('Y') - 3; ?></option>
                            <option><?php echo date('Y') - 4; ?></option>
                            <option><?php echo date('Y') - 5; echo ' and earlier'; ?></option>
                        </select>
                    </div>
                </div>

                <div class="col-sm-6">
                    Sort By...
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
                    <div class="custom-control custom-radio">
                        <input
                            type="radio"
                            id="sortDateDescRadio"
                            value="sortDateDesc"
                            name="sortRadio"
                            class="custom-control-input"
                        />
                        <label class="custom-control-label" for="sortDateDescRadio">Date (Recent)</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input
                            type="radio"
                            id="sortDateAscRadio"
                            value="sortDateAsc"
                            name="sortRadio"
                            class="custom-control-input"
                        />
                        <label class="custom-control-label" for="sortDateAscRadio">Date (Oldest)</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-9 scroll jumbotron capstoneJumbotron">
            <div class="card-columns capstoneCardColumns" id="projectCardGroup">
                <?php
					// Render the cards to browser here
					renderProjectCardGroup($projects, $keywordsDao, true);
					?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

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
      $("#filterInput").keydown(function(){
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
