<?php
require_once "core/init.php";
$errors = "";

$user = new User();
if($user->isLoggedin()){
  Redirect::to('index.php');
}

if(Input::exists())
{
    if(Token::check(Input::get('token'))){
      $validate = new Validate();
      $validation = $validate->check($_POST,
        array(
          'user_alias' => array('required' => true),
          'user_password' => array('required' => true)
        ));
        if($validate->passed()){
          //@TODO: log user in
          $user = new User();

          $remember = (Input::get('remember') === "on")?true:false;
          $login = $user->login(Input::get('user_alias'), input::get('user_password'), $remember);

          if($login){
            Redirect::to('index.php');
          }else{
            $errors = "Sorry, Login failed!";
          }
        }else{
          foreach ($validate->errors() as $error) {
            $errors .= $error." <br />";
          }
        }
    }
}

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="assets/css/main.css" />


    <script src="assets/js/jquery.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		<script src="assets/js/typed.js"></script>
		<script src="assets/js/wow.min.js"></script>
		<script src="assets/js/custom.js"></script>
</head>
<body class="bg-theme-pale" style="background-image: url('assets/images/bg-logo.png'); background-size: 100%; background-position: 0;">
  <section class="container pad-top-lg">
      
      <section class="container-fluid login-form" >
          <!--section class="bg-theme pad-lg title" >
            Sign In
          </section-->
          <section class="login-form-form pad-lg col-md-3 pull-right" >
            <form method="post" action="" name="login" class="form" style="padding: 2em; background-color: rgba(148, 206, 235, 0.23);" >

                      <?php 
                        if($errors != ""){
                          echo <<<EOF
                        <div class="alert alert-danger">
                          <strong>Somn` wrong!</strong>
                          <br />
                          <span>${errors}</span>
                        </div>
EOF;
                        }
                      ?>
                        <section class="form-group">
                          <input class="form-control" placeholder="Username" type="text" name="user_alias" id="username" autocomplete="off" >
                        </section>

                        <section class="form-group">
                          <input class="form-control" placeholder="Password" type="password" name="user_password" id="password" autocomplete="off" >
                        </section>
                        
                        <section class="checkbox">
                          <label for="remember" >
                            <input type="checkbox" name="remember" id="remember">Remember me?</input>
                          </label>
                        </section>

                        <section class="form-group">
                          <input type="hidden" name="token" value="<?php echo Token::generate(); ?>" >
                          <input class="btn bg-theme form-control" type="submit" value="Login" >
                        </section>
                      </form>
          </section>
          
          
          <section class="col-md-4 text-right pull-right" >
            <h2><?php echo "<span style='color: skyblue;' >".strtoupper(Config::get("system/name"))."</span>"; ?></h2>
          </section>
</section>
</body>
</html>
