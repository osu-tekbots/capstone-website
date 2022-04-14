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

$title = 'Login';
include_once PUBLIC_FILES . '/modules/header.php';

?>
<section class="vh-100" style="background-color: #D73F09;">
    <form action="/auth/localAttempt.php" method="POST">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card shadow-2-strong" style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center">
                            <h3 class="mb-5">Local Sign in</h3>
                            <div class="form-outline mb-4 form-group">
                            <label class="form-label" for="localEmail">Email</label>
                                <input type="email" name="localEmail" id="localEmail" class="form-control form-control-lg" />
                            </div>
                            <div class="form-outline mb-4 form-group">
                            <label class="form-label" for="localPassword">Password</label>
                                <input type="password" name="localPassword" id="localPassword" class="form-control form-control-lg" />
                            </div>
                            <button class="btn btn-dark btn-lg btn-block" type="submit">Login</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
<?php
include_once PUBLIC_FILES . '/modules/footer.php';
?>
