<?php 
class Controller{
	
	private static $instance;
	
	public function __construct(){
		self::$instance =& $this;
		
		// yuklu classlari kullanilabilir hale getir yukle
		foreach( is_loaded() as $variable => $class ){
			$this->$variable =  load_class($class);						
		}
	}
	
	public static function &get_instance()
	{
		return self::$instance;
	}
	
	public function get_db(){
		$this->db = Database::instance();
		return $this->db;
	}
}
?>