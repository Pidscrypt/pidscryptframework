<?php
    class User{
        private $_db;
        private $_data;
        private $_session_name;
        private $_cookie_name;
        private $_isLoggedIn;

        public function get_user_name(){
            return ucfirst($this->data()->user_alias);
        }

        public function __construct($user = null){
            $this->_db = Database::getInstance();

            $this->_session_name = Config::get('session/session_name');
            $this->_cookie_name = Config::get('remember/cookie_name');

            if(!$user){
                if(Session::exists($this->_session_name)){
                    $user = Session::get($this->_session_name);
                    
                    if($this->find($user)){
                        $this->_isLoggedIn = true;
                    }else{
                        //@TODO process logout
                    }
                }
            }else{
                $this->find($user);
            }
        }

        public function create($fields = array()){
            if(!$this->_db->insert('users',$fields)){
                throw new Exception('There was a problem creating an accout.');
            }
        }

        public function delete($fields = array()){
            if(!$this->_db->delete('users',$fields)){
                throw new Exception('There was a problem deleting accout.');
            }
        }

        public function has_permission($permission){
            /**
             *@TODO check for user permissions
             */
            $group = $this->_db->get('groups', array('id', '=', $this->data()->user_role));
            
            if($group->count()){
                $permissions = json_decode($group->first()->permissions, true);
               
                if(@$permissions[$permission] == true){
                    return true;
                }
            }

            return false;
        }

        public function belongs_to_group($group){
            $result = $this->_db->get('groups',array('id','=',$this->data()->user_role));
            if($result->count()){
                if($result->first()->alias == $group){
                    return true;
                }
            }

            return false;
        }

        public function data(){
            return $this->_data;
        }

        public function find($user = null){
            if($user){
                $field = (is_numeric($user))?'user_id':'user_alias';
                $data = $this->_db->get('users', array($field, '=', $user));
                //exit(var_dump($data));
                if($data->count()){
                    $this->_data = $data->first();
                    return true;
                }
            }
            return false;
        }
        public function login($username = null, $password = null, $remember = false){
      
          
        if(!$username && !$password && $this->exists()){
            Session::put($this->_session_name, $this->data()->user_Id);
        }else{
            $user = $this->find($username);
            if($user){
                if($this->data()->user_password === Hash::make($password, $this->data()->salt)){
                    Session::put($this->_session_name, $this->data()->user_Id);
                    
                    if($remember){
                        $hash = Hash::unique();
                        $hashCheck = $this->_db->get('users_session', array('user_id','=',$this->data()->user_Id));

                        if(!$hashCheck->count()){
                            $this->_db->insert('users_session',array(
                                'user_id' => $this->data()->user_Id,
                                'hash' => $hash
                            ));
                        }else{
                            $hash = $hashCheck->first()->hash;
                        }

                        Cookie::put($this->_cookie_name, $hash, Config::get('remember/expiry'));
                    }

                    return true;
                }
            }
        }

          return false;
        }

        public function exists(){
            return (!empty($this->data()))?true:false;
        }

        public function logout(){

            $this->_db->delete('users_session', array("user_id", "=", $this->data()->user_Id));
            Session::delete($this->_session_name);
            Cookie::delete($this->_cookie_name);
        }

        public function isLoggedIn(){
            return $this->_isLoggedIn;
        }

        public function update($fields = array(), $user_id = null){

            if(!$user_id && $this->isLoggedIn()){
                $user_id = $this->data()->user_Id;
            }

            try{
                if(!$this->_db->update('users','user_Id', $user_id, $fields)){
                    throw new Exception('there was a problem updating user');
                }
            }catch(Exception $ex){
                die($ex->getMessage());
            }
        }

        public function userImage(){
            if($this->data()->user_pic){
                // return user image from MyISAM db engine tables
                //die(var_dump($this->data()->user_pic.".jpg"));
                return $this->data()->user_pic.".jpg";
            }
            if($this->data()->gender == "male"){
                $gender = "_man";
            }else{
                $gender = "_woman";
            }
            if($this->data()->user_role == 2){
                $role = "default_0";
                return $role.".png";
            }else if($this->data()->user_role == 3){
                $role = "doctor";
            }else if($this->data()->user_role == 4){
                $role = "nurse";
            }else if($this->data()->user_role == 5){
                $role = "pharm";
            }
            if($this->belongs_to_group("administrator")){
                return "admin_icon.jpg";
            }
            return "{$role}{$gender}.png";
        }

        public function tabulate($title, $where = array()){
            if(count($where)){
                $user = $this->_db->get('users', $where);
            }else{
                $user = $this->_db->query("SELECT * FROM users ORDER BY user_fname LIMIT 0,4");
            }
            //Table::draw($title, $table);
            if(!$user->count()){
                echo "<div class='container'><span>>No {$title} yet.</s></div>";
            }else{
                
            echo "<div class='col-md-12 table-header' ><div class='col-md-3' ><span style='font-weight: 700; font-size: 1.5em;'>".$title."</span></div><div class='col-md-3'><input type='text' class='form-control form-control-sm' placeholder='Search'  name='users' /></div><div class='col-md-3 pull-right' ><ul class='pager col-md-12' style='margin-top: 0px; margin-bottom: 0px;' ><li><a href='#' ><span class='glyphicon glyphicon-menu-left' ></span>Previous</a></li><li><a href='#'>Next<span class='glyphicon glyphicon-menu-right' ></span></a></li></ul></div></div>";
            $output = <<<HTML
HTML;
                $title = ($title != "Users")?trim($title,"s"):"";
                foreach($user->results()  as $user){
                    $user_name = ucwords($user->user_fname." ".$user->user_lname);
                    $user_pic  = new User($user->user_Id);
                    $db = new Database();
                    $user_group = $db->get("groups", array("Id","=",$user->user_role));
                    $action = $this->belongs_to_group("administrator")?"<div class='col-md-1 col-sm-1' ><a class='link text-theme-dark' href='index.php?page=profile&user={$user->user_alias}' name='view' id='view'>View</a> </div>":"";
                    $output .= "<div class='container-fluid card-stark' style='max-height: 430px;'  ><img class='col-md-1 col-sm-1 img img-circle' src='assets/images/users/{$user_pic->userImage()}' /><div class='col-md-3 col-sm-3 text-theme-dark' ><div style='font-weight: 600;' >{$user_name}</div><div><span>{$title}</span></div><div><i class='badge bg-theme-light' >{$user_group->first()->alias}</i></div></div>{$action}<div class='col-md-3 col-sm-3' ><div>E-Mail: <span class='text-theme-dark' >".$user->user_email."</span></div><div>Mobile: <span class='text-theme-dark'>".$user->contact."</span></div></div><div class='col-md-2 col-sm-2' >".$user->reg_date."</div><div class='col-md-2' ><a href='?page=profile_update&id={$user->user_Id}' onclick='javscript:void(0)' class='btn bg-theme btn-sm' style='margin-left: 0.3em;' ><i class='glyphicon glyphicon-edit' ></i> Edit</a><a href='?page=profile_update&delete={$user->user_Id}' onclick='javascript:void(0)' class='btn btn-danger btn-sm' style='margin-left: 0.3em;' ><i class='glyphicon glyphicon-remove' ></i> Delete</a></div></div>";
                }

                echo $output;
            }
        }
    }
?>
