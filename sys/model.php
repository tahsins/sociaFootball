<?php 
class Model{
	protected $db;

	public function __construct(){
		$this->db = Database::instance();		
	}
	
	public function get_db(){
		$this->db = Database::instance();
		return $this->db;
	}
}
?>