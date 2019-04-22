<?php
/**
 * This file contains convenience functions for use when developing the website.
 */

/**
 * Dumps the variable contents onto the page inside of <pre> tags.
 *
 * @param mixed $var the variable to dump
 * @return void
 */
function dump($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}