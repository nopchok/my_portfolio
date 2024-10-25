<?php

require_once 'system/Database.php';

class Model
{
	public function __construct($localhost = NULL, $dbname = NULL, $username = NULL, $password = NULL)
	{
		$this->db = Db::getInstance($localhost, $dbname, $username, $password);
	}

	public function load_piping_dev(){
		$this->piping_dev = Db::getInstance(__HOSTNAME__, 'piping_dev', __USERNAME__, __PASSWORD__);
	}

	public function stmt_bind_assoc(&$stmt, &$bound_assoc) {
		$metadata = $stmt->result_metadata();
	    $fields = array();
	    $bound_assoc = array();
	
	    $fields[] = $stmt;
	
	    while($field = $metadata->fetch_field()) {
	        $fields[] = &$bound_assoc[$field->name];
	    }    
	    call_user_func_array("mysqli_stmt_bind_result", $fields);
	}
	public function stmt_get_result($stmt){
		$result = array();
		$this->stmt_bind_assoc($stmt, $row);
		while ($stmt->fetch()) {
			$c = $this->array_copy($row);
			$result[] = $c;
		}
		return $result;
	}
	function array_copy( array $array ) {
		$result = array();
		foreach( $array as $key => $val ) {
			if( is_array( $val ) ) {
				$result[$key] = arrayCopy( $val );
			} elseif ( is_object( $val ) ) {
				$result[$key] = clone $val;
			} else {
				$result[$key] = $val;
			}
		}
		return $result;
	}

	public function sql_exec($db, $binding, $sql, $get_result=true){
		$stmt = $db->prepare($sql);
        
        if( count($binding) > 0 ){
            $tmp = array(str_repeat("s", count($binding)));
			foreach($binding as $key => $value) $tmp[$key+1] = &$binding[$key];
			$a = array($stmt, 'bind_param');
            call_user_func_array($a, $tmp );
		}

		if( !$stmt ){
			return null;
		}else{
			$stmt->execute();
		}
		
		if( false ){ //cannot use in PHP 5
			$result = $stmt->get_result();
			$rows = $result->fetch_all(MYSQLI_ASSOC);
		}else if( $get_result ){
			$rows = $this->stmt_get_result($stmt);
		}else{
			$rows = array();
		}
		$stmt->close();
		return $rows;
	}
}