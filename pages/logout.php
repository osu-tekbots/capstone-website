<?php 
    session_start();
    unset($_SESSION['userID']);
    unset($_SESSION['accessLevel']);
    unset($_SESSION['newUser']);
    session_unset();
    session_destroy();
    header('Location: ./index.php');
?>