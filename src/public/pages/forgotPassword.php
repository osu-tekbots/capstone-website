<?php
include_once '../bootstrap.php';

if(!isset($_SESSION)) {
    session_start();
}

$isLoggedIn = isset($_SESSION['userID']) && !empty($_SESSION['userID']);
if ($isLoggedIn) {
    // Redirect to their profile page
    $redirect = $configManager->getBaseUrl() . 'pages/myProfile.php';
    echo "<script>window.location.replace('$redirect');</script>";
    die();
}

$title = 'Forgot Password';
include_once PUBLIC_FILES . '/modules/header.php';

?>

<div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">
            <div class="card shadow-2-strong" style="border-radius: 1rem;">
                <div class="card-body p-5 text-center" style="background-color: #D1D1D1">

                <h3 class="mb-5">Please enter email:</h3>

                <div class="form-outline mb-4">
                    <input type="email" id="email" class="form-control form-control-lg" />
                    <label class="form-label" for="email">Email</label>
                </div>
                    <button class="btn btn-dark btn-lg btn-block" id="submitPasswordResetBtn" type="submit">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script defer type="text/javascript">
/**
 * Event handler for a click event on the 'Submit' button.
 */
function onSubmitPassowrdResetClick() {

    let email = document.getElementById("email");
    // generate code and timeout
    // let resetCode = ???
    // let timeOut = time + 15min?
    let body = {
        action: 'passwordReset'
    };

    body[userEmail] = email; //wut
    // body[resetCode] = code;
    // body[timeOut] = timeOut;

    api.post('/users.php', body).then(res => {
        snackbar(res.message, 'success');
    }).catch(err => {
        snackbar(err.message, 'error');
    });
}
$('#submitPasswordResetBtn').on('click', onSubmitPassowrdResetClick);

</script>

<?php include_once PUBLIC_FILES . '/modules/footer.php'; ?>
