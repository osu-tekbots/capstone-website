<?php

if (isset($_REQUEST['action']) && $_REQUEST['action'] != ''){
	$action = mysqli_real_escape_string($mysqli, $_REQUEST['action']);
} 

if (($action == 'adminRequired')) {
    echo("sup");
    exit();
}



























?>