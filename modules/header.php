<?php
/**
 * This header module should be included in all PHP files under the `pages/` directory. It includes all the necessary
 * JavaScript and CSS files and creates the header navigation bar.
 * 
 * Before including the header file, you can specify a `$js` or `$css` variable to add additional JavaScript files
 * and CSS stylesheets to be included when the page loads in the browser. These additional files will be included
 * **after** the default scripts and styles already included in the header.
 */
include_once PUBLIC_FILES . '/modules/button.php';

if (!session_id()) {
    session_start();
}

$baseUrl = $configManager->getBaseUrl();

$title = isset($title) ? $title : 'Senior Design Capstone | OSU';

// JavaScript to include in the page. If you provide a JS reference as an associative array, the keys are the
// atributes of the <script> tag. If it is a string, the string is assumed to be the src.
if (!isset($js)) {
    $js = array();
}
$js = array_merge( 
    // Scripts to use on all pages
    array(
        'assets/js/jquery-3.3.1.min.js',
        'assets/js/popper.min.js',
        'assets/js/bootstrap.min.js',
        'assets/js/moment.min.js',
        'assets/js/tempusdominus-bootstrap-4.min.js',
        'assets/js/jquery-ui.js',
        'assets/js/platform.js',
        'assets/js/slick.min.js',
        'assets/js/jquery.canvasjs.min.js',
        'assets/js/image-picker.min.js',
        'assets/shared/js/api.js',
        'assets/shared/js/snackbar.js'
    ), $js
);

// CSS to include in the page. If you provide a CSS reference as an associative array, the keys are the
// atributes of the <link> tag. If it is a string, the string is assumed to be the href.
if (!isset($css)) {
    $css = array();
}
$css = array_merge(
    array(
        // Stylesheets to use on all pages
        array(
            'href' => 'https://use.fontawesome.com/releases/v5.7.1/css/all.css',
            'integrity' => 'sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr',
            'crossorigin' => 'anonymous'
        ),
        'assets/css/bootstrap.min.css',
        'assets/css/tempusdominus-bootstrap-4.min.css',
        'assets/css/slick.css',
        'assets/css/slick-theme.css',
        'assets/css/jquery-ui.css',
        'assets/css/image-picker.css',
        'assets/css/capstone.css',
        'assets/shared/css/snackbar.css',
        array(
            'media' => 'screen and (max-width: 768px)', 
            'href' => 'assets/css/capstoneMobile.css'
        ),
    ),
    $css
);

$loggedIn = isset($_SESSION['userID']) && !empty($_SESSION['userID']);

// Setup the buttons to use in the header
// All users
$buttons = array(
    'Browse Projects' => 'pages/browseProjects.php'
);
// Signed in users
if ($loggedIn) {
    //Proposer or Admin only
	if (isset($_SESSION['accessLevel']) && ($_SESSION['accessLevel'] == 'Proposer') || ($_SESSION['accessLevel'] == 'Admin') {
        $buttons['My Projects'] = 'pages/myProjects.php';
    }
	
	//All user types can view these pages
    $buttons['My Applications'] = 'pages/myApplications.php';
    $buttons['My Profile'] = 'pages/myProfile.php';\
    
    // Admin only
    if (isset($_SESSION['accessLevel']) && $_SESSION['accessLevel'] == 'Admin') {
        $buttons['Admin'] = 'pages/adminInterface.php';
    }
}

// All users
$buttons['Info'] = 'pages/info.php';
if ($loggedIn) {
    $buttons['Logout'] = 'pages/login.php?provider=logout';
} else {
    $buttons['Login'] = 'pages/login.php';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <base href="<?php echo $baseUrl ?>" />
    <title><?php echo $title; ?></title>

    <?php
    // Include the JavaScript files
    foreach ($js as $script) {
        if (!is_array($script)) {
            echo "<script type=\"text/javascript\" src=\"$script\"></script>";
        } else {
            $link = '<script type="text/javascript" ';
            foreach ($script as $attr => $value) {
                $link .= $attr . '="' . $value . '" ';
            }
            $link .= '></script>';
            echo $link;
        }
    }

    // Include the CSS Stylesheets
    foreach ($css as $style) {
        if (!is_array($style)) {
            echo "<link rel=\"stylesheet\" href=\"$style\" />";
        } else {
            $link = '<link rel="stylesheet" ';
            foreach ($style as $attr => $value) {
                $link .= $attr . '="' . $value . '" ';
            }
            $link .= '/>';
            echo $link;
        }
    } ?>

</head>
<body>
    <header>
        <nav class="navbar navbar-light navbarColor fixed-top navbarBrowser">
            <a class="navbar-brand" href=""><h2 class="websiteTitle">Senior Design Capstone</h2></a>
            <form class="form-inline">
                <?php 
                foreach ($buttons as $title => $link) {
                    echo createLinkButton($link, $title);
                }
                ?>
            </form>
            
        </nav>
    </header>
    <main>