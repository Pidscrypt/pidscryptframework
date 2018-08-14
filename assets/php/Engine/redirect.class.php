<?php
    namespace Engine;
    
    class Redirect {
        public static function to($location){

            if(is_numeric($location)){
                header('HTTP/1.0 404 Not Found');
                include(DOCUMENT_ROOT.'/includes/errors/404.html');
                exit();
            }
            header('location: '.$location);
            exit();
        }
    }
?>