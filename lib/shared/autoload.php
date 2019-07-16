<?php
// Define an autoloader for custom PHP classes so we don't have to
// include their files manually. The autoloader should check for the
// repository specific classes and shared classes.
spl_autoload_register(function ($className) {
    $phpFile = str_replace('\\', '/', $className) . '.php';
    $local = PUBLIC_FILES . '/lib/classes/' . $phpFile;
    $shared = PUBLIC_FILES . '/lib/shared/classes/' . $phpFile;
    if(file_exists($local)) {
        include $local;
        return true;
    }
    if(file_exists($shared)) {
        include $shared;
        return true;
    }
    return false;
});