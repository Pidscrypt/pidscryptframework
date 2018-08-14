<?php 

use Components\Page;
use Engine\Config;
use Engine\Template;

class Router {
    private $item = 'page';
    protected $output = null;
    protected $system_title;
    protected $page_title = null;
    protected $fetch_page;

    public function __construct(){
        $this->system_title = Config::get("system/name");
    }

    public function run(){ 
        $master_layout = new Template(DOCUMENT_ROOT."/views/master_layout.tpl");
        
        $master_layout->set("title", ($this->page_title != null)?$this->page_title." | ".$this->get_system_name():$this->get_system_name());
        $master_layout->set("system_logo",$this->get_system_name());
        $master_layout->set("content", $this->render(new Page($this->fetch_page)));

        
	    echo $master_layout->output();
        
    }

    private function render($page){
        //$db = new Database();
        try{
            return $page->getPageContent();
        }catch(Exception $ex){

        }
        //get page type
        return false;
    }

    public function get_system_name(){
        return $this->system_title;
    }

    public function set_fetch_page($name){
        $this->fetch_page = $name;
    }
}

?>