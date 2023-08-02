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

<section class="vh-100" style="background-color: #D73F09;">
    <form action="./pages/forgotPasswordAttempt.php" method="POST">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card shadow-2-strong" style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center" style="background-color: #D1D1D1">
                            <h3 class="mb-5">Please enter email:</h3>
                            <div class="form-outline mb-4">
                                <label class="form-label" for="userEmail">Email</label>
                                <input type="email" name="userEmail" id="userEmail" class="form-control form-control-lg" />
                            </div>
							<button class="btn btn-dark btn-lg btn-block" type="submit">Submit</button>
							<h3>If you are a new user, enter the information below as well</h3>
							<div class="form-outline mb-4">
                                <label class="form-label" for="userFirst">First Name</label>
                                <input type="text" name="userFirst" id="userFirst" class="form-control form-control-lg" />
                            </div>
							<div class="form-outline mb-4">
                                <label class="form-label" for="userLast">Last Name</label>
                                <input type="text" name="userLast" id="userLast" class="form-control form-control-lg" />
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

<?php include_once PUBLIC_FILES . '/modules/footer.php'; ?>
