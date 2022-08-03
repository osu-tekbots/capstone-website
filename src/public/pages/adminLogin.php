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
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card shadow-2-strong" style="border-radius: 1rem;">
          <div class="card-body p-5 text-center">

            <h3 class="mb-5">Administrator Sign in</h3>

            <div class="form-outline mb-4">
              <input type="email" id="adminEmail" class="form-control form-control-lg" />
              <label class="form-label" for="adminEmail">Email</label>
            </div>
            <div class="form-outline mb-4">
              <input type="password" id="adminPassword" class="form-control form-control-lg" />
              <label class="form-label" for="adminPassword">Password</label>
            </div>
            <button class="btn btn-dark btn-lg btn-block" type="submit">Login</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php
include_once PUBLIC_FILES . '/modules/footer.php';
?>
