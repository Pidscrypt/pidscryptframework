<?php
	use Exception\Exception;
    namespace Engine;

/**
 * created by PidScrypt Ug on 6/02/2018 in the back of the house
 */

/**
 * @title          Add a User Class
 *
 * @author         Olili Daniel <olilidaniel48@gmail.com>
 * @copyright      (c) 2018, PidScrypt. All Rights Reserved.
 * @license        null
 * @package
 */
class Database
{
    //attributes
    private $db_host = "";
    private $db_user = "";
    private $db_pass = "";
  	private $db_name = "";
  	private static $_instance = null;
  	private $_pdo,
  			$_query,
  			$_error = false,
  			$_result,
  			$_count = 0;


    /**
     * database constructor // gets called whenever database is initiated
     * @param String $host      Defines the server hostname
     * @param String $user      Database username
     * @param String $password  Database user password
     * @param String $database  Database name
     */
    public function __construct() {

		try{
			//$this->_pdo = new PDO("mysql:host=".Config::get('mysql/host').";dbname=".Config::get('mysql/db'),Config::get('mysql/username'),Config::get('mysql/password'));

		}catch(PDOException $PDOEx){
			echo $PDOEx->getMessage();
		}
    }

	// methods

	public static function getInstance(){
		if(!isset(self::$_instance)){
			self::$_instance = new Database();
		}
		return self::$_instance;
	}

	public function query($sql, $params = array(),$and_params = null){
		$this->_error = false;
		if($this->_query = $this->_pdo->prepare($sql)){
			
			$x = 1;
			if(count($params)){
				foreach($params as $param){
					$this->_query->bindValue($x, $param);
					$x++;
				}
			}
			if(count($and_params)){
				foreach($and_params as $param){
					$this->_query->bindValue($x, $param);
					$x++;
				}
			}
			if($this->_query->execute()){
				$this->_result = $this->_query->fetchAll(PDO::FETCH_OBJ);
				$this->_count = $this->_query->rowCount();
			}else{
				$this->_error = true;
			}
		}
		return $this;
	}

	private function action($action, $table, $where = array(),$and = null,$lower_limit = null){
		
		$limit = null;

		if(count($where) === 3){
			$operators = array('=','>','<','>=','<=','!=');

			$field = 	$where[0];
			$operator =	$where[1];
			$value 	= 	$where[2];
			if($and){
				$and_field = 	$and[0];
				$and_operator =	$and[1];
				$and_value 	= 	$and[2];
		}

		if($lower_limit){
			$limit = "LIMIT {$lower_limit},8";
		}
			

			if(in_array($operator, $operators)){
				$sql = "{$action} FROM {$table} WHERE {$field} {$operator} ? {$limit}";
				if($and){
					$sql = "{$action} FROM {$table} WHERE {$field} {$operator} ? AND {$and_field} {$and_operator} ? {$limit}";
					
					if(!$this->query($sql, array($value),array($and_value))->error()){
						return $this;
					}
				}

				if(!$this->query($sql, array($value))->error()){
					return $this;
				}
			}
		}

		return false;
	}

	public function search($table, $field, $input,$sec_field = null, $sec_input = null,$third_field = null,$third_input = null){		
		
		if($third_field != null){
			$sql = "SELECT * FROM {$table} WHERE {$field} LIKE CONCAT('%',?,'%') OR {$sec_field} LIKE CONCAT('%',?,'%') OR {$third_field} LIKE CONCAT('%',?,'%')";
			if(!$this->query($sql, array($input,$sec_input,$third_input))->error()){
				return $this;
			}
		}
		elseif(($sec_field != null) && ($third_field == null)){
			$sql = "SELECT * FROM {$table} WHERE {$field} LIKE CONCAT('%',?,'%') OR {$sec_field} LIKE CONCAT('%',?,'%')";
			if(!$this->query($sql, array($input,$sec_input))->error()){
				return $this;
			}
		}else{
			$sql = "SELECT * FROM {$table} WHERE {$field} LIKE CONCAT('%',?,'%')";
			if(!$this->query($sql, array($input))->error()){
				return $this;
			}
		}
		
		//die($sql);
		if(!$this->query($sql, array($input,$sec_input,$third_input))->error()){
			return $this;
		}
	}

	public function get($table, $where, $and = null, $lower_limit = null){
		if($and){
			return $this->action("SELECT *",$table,$where,$and,$lower_limit);
		}
		return $this->action("SELECT *", $table, $where,null,$lower_limit);
	}

	public function getCount($table,$where,$and = null){
		if($and){
			return $this->action("SELECT COUNT(*) as count ",$table,$where,$and);
		}
		return $this->action("SELECT COUNT(*) as count ",$table,$where);
	}

	public function delete($table, $where){
		return $this->action("DELETE ", $table, $where);
	}

	public function insert($table, $fields = array()){
		if(count($fields)){
			$keys = array_keys($fields);
			$values = '';
			$x = 1;

			foreach ($fields as $field) {
				$values .= "?";
				if($x < count($fields))
				{
					$values .= ", ";
				}
				$x++;
			}
			$sql = "INSERT INTO ".$table." (`".implode("`,`", $keys)."`) VALUES  ({$values})";
			
			if(!$this->query($sql, $fields)->error()){
				return true;
			}
			else{
				echo "could not insert data";
			}
		}
		return false;
	}

	public function update($table, $id_column, $id, $fields, $attendant_for_queues = null){
		$set = '';
		$x = 1;

		foreach($fields as $name => $value){
			$set .= "{$name} = ?";
			if($x < count($fields)){
				$set .= ", ";
			}
			$x++;
		}

	if($attendant_for_queues != null){
		$sql = "UPDATE {$table} SET {$set} WHERE {$id_column} = {$id} AND queue_atendant_group = '{$attendant_for_queues}'";
	}else{
		$sql = "UPDATE {$table} SET {$set} WHERE {$id_column} = {$id}";
	}

		if(!$this->query($sql, $fields)->error()){
			return true;
		}

		return false;
	}
/*
	public function tabulate($table, $where = array(), $and = null){
		$table = $this->getInstance()->action("SELECT * ",$table,$where,$and);
		
		$output = <<<HTML
		<table class="table" >
			<thead>

			</thead>
		</table>
HTML;

		if(count($table->results())){
			foreach($table->results() as $items){
				$vars = get_object_vars($items);
				//var_dump($vars);
				foreach($vars as $i => $v){
					$table_heads
					$table_vals .= $items->$i;
				}
			};
		}else{
			echo "no items to list from table [".ucwords($table)."]";
		}

		echo $output;
	}*/

	public function results(){
		return $this->_result;
	}

	public function first(){
		return @$this->results()[0];
	}

	public function count(){
		return $this->_count;
	}

	protected function error(){
		return $this->_error;
	}

    // properties
}

?>
