<?php
class Menu {

    private $_orietation;
    private $items = array(
        "home" => "index.php",
        "about" => "about.php"
    );

    public function __construct($orientation = null){
        $this->_orientation = $orientation;
    }

    public static function horizontal_menu($menu_items = array()){

        $system_name = Config::get('system/name');
        $user = new User();
        $db = new Database();

        $user_group = $db->get("groups", array("Id","=",$user->data()->user_role));
        $user_display = ($user_group->first()->alias == "receptionist")?"receptionist":$user_group->first()->alias.($user->data()->specialisation?" <span class='text-theme-dark' >(".$user->data()->specialisation.")</span>":"");
        $output = <<<HTML
        <nav class="navbar navbar-default" style="margin-bottom: 0px;">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a href="#menu-toggle" style="color: #525b6a;" class="navbar-brand link text-theme-light" id="menu-toggle">
        <span class="glyphicon glyphicon-list"></span>
     </a>
      <a class="navbar-brand" style="color: #666; font-size: 1.5em; text-transform: uppercase;" href="index.php">{$user_display}</a>
      
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
HTML;
if(isset($_GET["page"])){
    if($_GET["page"] == "patients_view"){
    $output .= <<<HTML
    <li><input type="text" onkeyup="searchPatients(this.value)" onfocus="nofurther = true;" id="patients_search" name="patients_search" placeholder="Search here" style="margin-left: 6em; margin-top: 0.5em; width: 300px;" class="col-md-4 form-control has-feedback" ></li>
HTML;
    }
}elseif(!isset($_GET['page']) and $user->belongs_to_group("receptionist")){
    $output .= <<<HTML
    <li><input type="text" onkeyup="searchPatients(this.value)" onfocus="nofurther = true;" id="patients_search" name="patients_search" placeholder="Search here" style="margin-left: 6em; margin-top: 0.5em; width: 300px;" class="col-md-4 form-control has-feedback" ></li>
HTML;
}
        
        foreach($menu_items as $item => $link){
            $output .= "<li><a href='${link}' >".ucfirst($item)."</a></li>";
        }
        $output .= "</ul><ul class='nav navbar-nav navbar-right'>";  
        //$output .= ($user->isLoggedin())?"<li><a href='logout.php'><span class='glyphicon glyphicon-log-out'></span> Logout</a></li>":"<li><a href='login.php'><span class='glyphicon glyphicon-log-in'></span> Login</a></li>";
        if($user->belongs_to_group("administrator")){
            
            $output .= <<<HTML
                <li class="pull-right" ><a href="index.php?page=add_staff"><span class="glyphicon glyphicon-plus text-theme-dark"></span> <span class="text-theme-dark">Add user</span></a></li>
                <li class="text-theme-dark" ><a class="text-theme-dark" href="print.php?page=report&print=true"> <span class="glyphicon glyphicon-print text-theme-dark"></span> <span class="text-theme-dark">Print Full Report</span></a></li>
HTML;
                }

        if($user->belongs_to_group("receptionist")){
            
    $output .= <<<HTML
        <li><a href="index.php?page=add_patient"><span class="glyphicon glyphicon-user"></span> Add patient</a></li>
HTML;
        }

        $output .= <<<HTML
        </ul>
    </div>
  </div>
</nav>
HTML;
        return $output;
    }

    public static function verticle_menu($menu_items = array()){

        $system_name_short = Config::get('system/name_short');
        $user = new User();

        $output = <<<HTML
<ul class="sidebar-nav">
HTML;
        foreach($menu_items as $item => $link){
            $output .= "<li><a href='${link}' >".ucfirst($item)."</a></li>";
        }

        $output .= <<<HTML
    </ul>
HTML;
        return $output;
    }
}

?>