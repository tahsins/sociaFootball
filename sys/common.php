<?php 

// ortak fonksiyonlar

function load_class($class_name, $file_path = '../helper', $file_name = '' ){
	static $classes = array();
	
	
	if( isset( $classes[$class_name] ) )
		return $classes[$class_name];
	
	
	if( $file_name != '' )
		$file = $file_path . '/' . $file_name;
	else
		$file = $file_path . '/' . $class_name . '.php';
		
		
		
	if( file_exists( $file ) && is_file( $file ) ){
		if( $class_name == '' )
			$class_name = $file_name ;
			
		
		if( ! class_exists( $class_name ) ){
			include( $file );

			$classes[$class_name] = new $class_name();		
			
			// add loaded class
			is_loaded($class_name);
			
			return $classes[$class_name];
		}


		
	}
}

/**
* yuklu siniflar
*
* @access public
* @return array
*/
function is_loaded($class = '')
{
	static $_is_loaded = array();

	if ($class != '')
	{
		$_is_loaded[strtolower($class)] = $class;
	}

	return $_is_loaded;
}
?>