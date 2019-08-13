/**
 * Fetches the project ID from the HTML
 * @returns {string}
 */
function getProjectId() {
    return $('#projectId').val();
}

/**
 * Handler for when the project category is selected by the admin. The result of the select will automatically
 * update the category of the project in the database.
 */
function onProjectCategorySelect() {
    projectCategorySelect = $('#projectCategorySelect').val();
    projectID = getProjectId();

    let body = {
        action: 'updateCategory',
        projectId: projectID,
        categoryId: projectCategorySelect
    };

    api.post('/projects.php', body)
        .then(res => {
            snackbar(res.message, 'success');
        })
        .catch(err => {
            snackbar(err.message, 'error');
        });
}
$('#projectCategorySelect').change(onProjectCategorySelect);

/**
 * Handler for click event on the 'Approve Project' button for admin project views.
 */
function onProjectApprove() {
    let projectId = getProjectId();

    let body = {
        action: 'approveProject',
        projectId
    };

    api.post('/projects.php', body)
        .then(res => {
            snackbar(res.message, 'success');
        })
        .catch(err => {
            snackbar(err.message, 'error');
        });
}
$('#adminApproveProjectBtn').on('click', onProjectApprove);

/**
 * Handler for click event on the 'Reject Project' button for admin project views.
 */
function onProjectReject() {
    let reason = prompt('Reason for rejection', 'Text');
    if (!reason) return;

    let body = {
        action: 'rejectProject',
        projectId: getProjectId(),
        reason
    };

    api.post('/projects.php', body)
        .then(res => {
            snackbar(res.message, 'success');
        })
        .catch(err => {
            snackbar(err.message, 'error');
        });
}
$('#adminUnapproveProjectBtn').on('click', onProjectReject);

/**
 * Event handler for publishing a project (making it publically viewable)
 */
function onMakeProjectPublic() {
    let body = {
        action: 'publishProject',
        id: getProjectId()
    };

    api.post('/projects.php', body)
        .then(res => {
            snackbar(res.message, 'success');
            $('#adminViewProjectBtn').show();
        })
        .catch(err => {
            snackbar(err.message, 'error');
        });
}
$('#adminMakeProjectNotPrivateBtn').on('click', onMakeProjectPublic);

/**
 * Event handler to unpublishing a project (making it not viewable to the public)
 */
function onMakeProjectPrivate() {
    let body = {
        action: 'unpublishProject',
        id: getProjectId()
    };

    api.post('/projects.php', body)
        .then(res => {
            snackbar(res.message, 'success');
            $('#adminViewProjectBtn').hide();
        })
        .catch(err => {
            snackbar(err.message, 'error');
        });
}
$('#adminMakeProjectPrivateBtn').on('click', onMakeProjectPrivate);

/**
 * Event handler to unpublishing a project (making it not viewable to the public)
 */
function onArchiveProject() {
    let body = {
        action: 'archiveProject',
        id: getProjectId()
    };

    api.post('/projects.php', body)
        .then(res => {
            snackbar(res.message, 'success');
        })
        .catch(err => {
            snackbar(err.message, 'error');
        });
}
$('#adminMakeProjectArchivedBtn').on('click', onArchiveProject);
