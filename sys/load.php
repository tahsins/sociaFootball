<?php 
class Load{	
	
	private static $instance;
	
	public function __construct(){
		self::$instance =& $this;	
	}
	
	public static function instance(){
		if (!self::$instance){			
			self::$instance = new Input();
		}
		return self::$instance;
	}
	
	public function view($file_name, $data = null){
		if( is_array( $data ) ){
			extract( $data );
		}
		include('../views/'.$file_name);
	}
	
	public function model($file_name, $data = null, $class_name = '', $extension = '.php'){
		$file = '../models/'.$file_name . $extension;
		if( file_exists( $file ) && is_file( $file ) ){
			if( $class_name == '' )
				$class_name = $file_name ;
				
			include( $file );
			if( ! class_exists( $class_name ) )
				return false;
				
			$class_name = new $class_name();
			if( is_array( $data ) && count( $data ) > 0 ){
				foreach( $data as $key => $val ){
					$class_name->$key = $val;
				}
			}
			return $class_name;
		}
		return false;
	}
}
?>