<?php
//use Components\Page;
/**
 *  Website
 * 
 * @author Olili daniel || Achan scovia
 * @copyright 2014-2018 PidScrypt Inc
 * @package Humane Africa Mission
 * @version v1.0
 * @since 2018
 */

 /** define system required files */
 require_once '../document.php';
 require_once DOCUMENT_ROOT."/core/init.php";

/**
 * defines system current state
 * @param development
 * @param production
 * @param testing
 * PHP will display errors based on what statge of the project you are
 */

DEFINE("ENVIRONMENT","development");

if(defined("ENVIRONMENT")){
    switch (ENVIRONMENT) {
        case 'development':
            error_reporting(E_ALL);
            break;
        case "testing":
            case "production":
                error_reporting(0);
        default:
            break;
    }
}

//$page = new Page();

$app = new Router();
$app->set_fetch_page(isset($_GET['page'])?$_GET['page']:null);
$app->run();

// end system variables
?>
