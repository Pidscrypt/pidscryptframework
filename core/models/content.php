<?php

if(isset($_GET["page"])){
    switch ($_GET["page"]) {
        default:
            header('HTTP/1.0 404 Not Found');
            include_once('includes/errors/404.php');
            break;
    }
}else{
    include_once("home.php");
}
?>