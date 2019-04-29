/*
 * Javascript functionality for the editProject page. This script is 'deferred', so the following code
 * won't execute until the page has finished parsing.
 */

/**
 * Fetches the project ID from the HTML
 * @returns {string}
 */
function getProjectId() {
    return $('#id').val();
}

/**
 * Serializes the form and returns a JSON object with the keys being the values of the `name` attribute.
 * @returns {object}
 */
function getProjectFormDataAsJson() {
    let form = document.getElementById('formProject');
    let data = new FormData(form);

    let json = {
        title: $('#projectTitleInput').val()
    };
    for (const [key, value] of data.entries()) {
        json[key] = value;
    }

    // json.keywords = json.keywords
    //         .replace(/<span class="badge badge-light keywordBadge">/g, "")
    //         .replace(/ <i class="fas fa-times-circle"><\/i><\/span>/g, ", ");

    return json;
}

//
// Special element format initialization
//
// datetimepicker is a function from the TempusDominus library and is the GUI
// that allows users to select the date time of the StartBy/EndBy inputs.
// Link to documentation: https://tempusdominus.github.io/bootstrap-4/
$('#startbydate').datetimepicker({
    format: 'L'
});
$('#endbydate').datetimepicker({
    format: 'L'
});
// Instantiates all tool tips.
$('[data-toggle="tooltip"]').tooltip();



$('#keywordsInput').on('change', function() {
    key = $('#keywordsInput').val();
    //Add user-generated keyword into the keywordsDiv.
    $('#keywordsDiv').append(
        '<span class="badge badge-light keywordBadge">' + key + ' <i class="fas fa-times-circle"></i></span>'
    );
    $('#keywordsInput').val('');
});

//Remove keywords when clicked.
$('body').on('click', '.keywordBadge', function(e) {
    this.remove();
});

/**
 * Updates the layout of the page if the project type is selected. This is
 * because certain text boxes appear for certain types and not others
 */
function updateEditProjectLayout() {
    // If it is a capstone project (enum of 1)
    if ($('#projectTypeSelect').val() == 1) {
        $('#dateDiv').hide();
        $('#ndaDiv').show();
        $('#numberGroupsDesiredDiv').show();
        $('#compensationDiv').hide();
    } else {
        $('#dateDiv').show();
        $('#ndaDiv').hide();
        $('#numberGroupsDesiredDiv').hide();
        $('#compensationDiv').show();
    }
}
$('#projectTypeSelect').change(updateEditProjectLayout);
updateEditProjectLayout();

/**
 * Uploads a newly selected image to the server. This function will be invoked when a change is detected in the
 * 'Upload Image' file input on the edit project page.
 */
function uploadProjectImage() {
    let data = new FormData();
    data.append('action', 'uploadImage');
    data.append('id', getProjectId());
    data.append('image', $('#imgInp').prop('files')[0]);

    api.post('/upload.php', data, true).then(res => {
        // TODO: display newly uploaded image in image picker
    }).catch(err => {
        snackbar(err.message, 'error');
    });
}
$('#imgInp').on('change', uploadProjectImage);

/**
 * Sets the selected image as the default image for the project. On ever select, the default value will be
 * updated on the server.
 * @param {string} imageId the ID of the selected image
 */
function onProjectImageSelected(imageId) {
    let body = {
        action: 'defaultImageSelected',
        imageId,
        projectId: getProjectId()
    };

    api.post('/projects.php', body)
        .then(res => {
            $('#nameOfImageInput').val(res.content.name);
            $('#img-upload').attr('src', 'images/' + imageId);
        })
        .catch(err => {
            snackbar(err.message, 'error');
        });
}
$('.image-picker').on('change', function() {
    onProjectImageSelected($(this).val());
});

//Generates the save icon animation.
function createSaveIcon() {
    loaderDivText = `
    <div class="loaderdiv">
        <span class="save-icon">
            <span class="loader"></span>
            <span class="loader"></span>
            <span class="loader"></span>
        </span>
    </div>`;
    $('#cssloader').html(loaderDivText);
}

/**
 * Handler for a user click on the 'Save Project Draft' button. It will use AJAX to save the project in the
 * database. The project title must not be empty.
 */
function onSaveProjectDraftClick() {
    let body = getProjectFormDataAsJson();

    if (body.title == '') {
        return snackbar('Please provide a project title', 'error');
    }

    body.action = 'saveProject';

    api.post('/projects.php', body)
        .then(res => {
            snackbar(res.message, 'success');
        })
        .catch(err => {
            snackbar(err.message, 'error');
        });
}
$('#saveProjectDraftBtn').on('click', onSaveProjectDraftClick);

/**
 * Handler for a user click on the 'Submit for Approval' button. This will verify all required input fields of
 * the form are filled out and then send a request to the server via AJAX to update the status of the application.
 */
function onSubmitForApprovalClick() {
    let project = getProjectFormDataAsJson();

    // Validate the form
    if (project.title == '') {
        return snackbar('Please provide a project title', 'error');
    } else if (project.typeId == 1 && project.ndaIpId == '') {
        return snackbar('Please select an NDA/IP option', 'error');
    } else if (project.description == '') {
        return snackbar('Please provide a project description', 'error');
    } else if (project.motivation == '') {
        return snackbar('Please provide input for a project motivation', 'error');
    } else if (project.objectives == '') {
        return snackbar('Please provide input for the objectives/deliverables', 'error');
    }

    // Validation completed. Make the request.
    let body = {
        action: 'submitForApproval',
        id: project.id
    };
    api.post('/projects.php', body)
        .then(res => {
            snackbar(res.message, 'success');
        })
        .catch(err => {
            snackbar(err.message, 'error');
        });
}
$('#submitForApprovalBtn').on('click', onSubmitForApprovalClick);

$('#keywordsInput').autocomplete({
    source: availableTags
});