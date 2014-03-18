<?php 
class Database{
	
	private $dbConfig = array();
	private $dbLink = null;
	private static $db;
	
	public function __construct(){
		
		//$this->dbConnect();
		$this->setConfig();
		$this->dbConnect();
	}
	
	public static function instance(){
		
		if (!self::$db){
			self::$db = new Database();
			
		}
		return self::$db;
	}
	
	public function dbConnect(){
		$this->dbLink = mysql_connect( $this->dbConfig['host'], $this->dbConfig['user'], $this->dbConfig['password'] );
		if( $this->dbLink ){
			mysql_select_db( $this->dbConfig['db_name'] );
			mysql_query("set names 'utf8'");
			
		}
	}
	
	public function dbClose(){
		if( $this->dbLink != null ){			
			mysql_close(  $this->dbLink );
		}
	}
	
	private function setConfig( $data = array() ){
		$config['host'] = 'localhost';
		$config['user'] = 'xxx';
		$config['password'] = 'xxx';
		$config['db_name'] = 'xxx';
		
		foreach( $data as $key => $val){
			$config[$key] = $val;
		}
		
		$this->dbConfig = $config;
		return $this->dbConfig;
	}
	
	public function getFetchRow($select = '*', $table = '', $where = '', $order_by = '', $joins = array()){
		$query = 'SELECT ' . $select ;
		if( $table != '' )
			$query .= " FROM $table ";
			
		if( count( $joins ) > 0 ){
			foreach( $joins as $join){
				$query .= $join['type'] . ' ' . $join['table'];
				$query .= ' ON ' . $join['where'] . ' ';
			}
		}
		
		if( $where != '' )
			$query .= " WHERE $where ";
			
		if( $order_by != '' )
			$query .= " ORDER BY $order_by ";
		
		if( empty( $query ) )
			return false;
		
		$result = mysql_query($query);
		if( ! $result )
			return false;
			
		$data = mysql_fetch_row( $result );
		if( $data )
			return $data;
		else
			return false;
	}
	
	public function getFetchAll($select = '*', $table = '', $where = '', $order_by = '', $joins = array()){
		$query = 'SELECT ' . $select ;
		if( $table != '' )
			$query .= " FROM $table ";
			
		if( count( $joins ) > 0 ){
			foreach( $joins as $join){
				$query .= $join['type'] . ' ' . $join['table'];
				$query .= ' ON ' . $join['where'] . ' ';
			}
		}
		
		if( $where != '' )
			$query .= " WHERE $where ";
			
		if( $order_by != '' )
			$query .= " ORDER BY $order_by ";
		
		if( empty( $query ) )
			return false;
			
		$result = mysql_query($query);
		if( ! $result )
			return false;
		
		$data = array();
		while($row = mysql_fetch_object( $result )){
			$data[] = $row;
		}

		return $data;
	}
	
	public function getFetchArray($select = '*', $table = '', $where = '', $order_by = '', $joins = array()){
		$query = 'SELECT ' . $select ;
		if( $table != '' )
			$query .= " FROM $table ";
			
		if( count( $joins ) > 0 ){
			foreach( $joins as $join){
				$query .= $join['type'] . ' ' . $join['table'];
				$query .= ' ON ' . $join['where'] . ' ';
			}
		}
		
		if( $where != '' )
			$query .= " WHERE $where ";
			
		if( $order_by != '' )
			$query .= " ORDER BY $order_by ";
		
		if( empty( $query ) )
			return false;
			
		$result = mysql_query($query);
		if( ! $result )
			return false;
		
		$data = array();
		while($row = mysql_fetch_array( $result )){
			$data[] = $row;
		}

		return $data;
	}
	
	public function execute($query){
		if( empty( $query ) )
			return false;
			
		if (mysql_query($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
	}
	
	public function insert($table, $fields = array()){
		
		if( $table == '' )
			return false;
			
		if( !(is_array( $fields ) && count($fields) > 0) )
			return false;
			
		$query = "INSERT INTO $table (%s) VALUES (%s)";
		$fieldKey = '';
		$fieldVal = '';
		
		foreach($fields as $key => $val){
			$fieldKey .= $key . ',';
			$fieldVal .= "'$val',";
		}
		$fieldKey = rtrim($fieldKey, ",");
		$fieldVal = rtrim($fieldVal, ",");
		
		$query = sprintf($query, $fieldKey, $fieldVal );
			
		if (mysql_query($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
	}
	
	public function update($table, $fields = array(), $where = ''){

		if( $table == '' )
			return false;
			
		if( !(is_array( $fields ) && count($fields) > 0) )
			return false;
			
		$query = "UPDATE $table SET %s";
		$values = '';
		
		foreach($fields as $key => $val){
			$values .= "$key = '$val',";
		}
		$values = rtrim($values, ",");
		
		$query = sprintf($query, $values);
		
		if( $where != '' )
			$query .= " WHERE $where";			
			
		if (mysql_query($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
	}
	
	public function getInsertId(){
		return mysql_insert_id();
	}

}
?>