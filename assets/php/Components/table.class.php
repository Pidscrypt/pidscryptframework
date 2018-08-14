<?php 
namespace Components;

use Engine\Template;
use Errors\Error;

class Table {

    private $layout = null;
    private $heading = null;
    private $rows = null;
    private $content = null;
    private $table = null;

    public function __construct($heading = null, $content = null){
        $this->heading = $heading;
        if(!$content){
            $error = new Error("800","Table Broken", $heading?"Could not create table \"".$heading."\" because it has no content.":('\"no name\" because it has no content.'));
            $this->table = $error->getErrorPage();
        }else{

            foreach ($content as $item) {
                $row = new Template(DOCUMENT_ROOT."/views/table_row.tpl");
                
                foreach ($item as $key => $value) {
                    $row->set($key, $value);
                }
                $itmesTemplates[] = $row;
            }

            $this->rows = Template::merge($itmesTemplates);

            $table =  new Template(DOCUMENT_ROOT."/views/table.tpl");
            $table->set("heading",$this->getTableHeading());
            $table->set("content",$this->getRows());
            $this->table = $table->output();
        }
    }

    private function buildTableRows(){

    }

    public function render(){
        return $this->table;
    }

    public function getTableHeading(){
        return $this->heading;
    }

    protected function getRows(){
        return $this->rows;
    }
}
?>