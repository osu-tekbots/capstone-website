<?php

/**
 * Redirects the user to the specified URL if the condition is not true
 *
 * @param boolean $condition the condition to check
 * @param string $url the URL to redirect to if the condition fails
 * @return void
 */
function allowIf($condition, $failUrl = 'index.php') {
    if(!$condition) {
        echo "<script>window.location.replace('$failUrl');</script>";
        die();
    }
}