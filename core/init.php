<?php

    use Engine\Config;
    use Engine\Cookie;
    use Engine\Session;
    
    session_start();

    $GLOBALS["config"] = array(
        'mysql' => array(
            "host" => "127.0.0.1",
            "username" => "com_humaneafricamission",
            "password" => "123@ham.dev",
            "db" => "com_humaneafricamission"
        ), 
        'remember' => array(
            "cookie_name" => "ham_hash",
            "expiry" => 604800
        ), 
        'session' => array(
            "session_name" => "ham_user",
            "token_name" => "ham_token"
        ), 
        'system' => array(
            "name" => "Humane Africa Mission",
            "name_short" => "HAM",
            "version" => "V1.0.0"
        )
    );

    spl_autoload_register(function($class){
        require_once(DOCUMENT_ROOT."/assets/php/".strtolower($class).".class.php");
    });

    require_once(DOCUMENT_ROOT."/functions/sanitize.php");

    if(Cookie::exists(Config::get('remember/cookie_name') && !Session::exists(Config::get('session/session_name')))){
        $hash = Cookie::get(Config::get('remember/cookie_name'));
        $hashCheck = Database::getInstance()->get('users_session', array('hash', "=", $hash));

        if($hashCheck->count()){
            $user = new User($hashCheck->first()->user_id);
            $user->login();
        }
    }
?>