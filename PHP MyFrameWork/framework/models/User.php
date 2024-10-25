<?php
require_once 'system/Model.php';

class User extends Model
{
	public function __construct(){
		parent::__construct();

		// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	}

	public function getDetail($id){
		$q = "SELECT u.*, r.role FROM user u left join role r on r.id = u.role_id where u.user_id = ? and u.active_flag = 'Y';";
		$stmt = $this->db->prepare($q);
		$stmt->bind_param("s", $id);
		$rows = $this->sql_exec($this->db, array($id), $q);
		
		return $rows;
	}
	
	
	public function insert_ws_user($data){
		$rows = $this->getDetail($data['user_id']);
		if( count($rows) > 0 ) return array('result'=>false, 'message'=>"Duplicate data");
		
		$ppuser = $this->getPPDevDetail($data['user_id']);
		return $this->insertUser($ppuser[0]);
	}

	public function get_ws_user($data){
		$this->load_piping_dev();

		$q = '';
		$q .= " select us.ID as USER_ID, us.USER_NAME ,emp.CODE as EMP_CODE,lkt.DISPLAY as TITLE_NAME,emp.FIRST_NAME as FIRST_NAME,emp.LAST_NAME as LAST_NAME ";
		$q .= "     ,emp.EMAIL as EMAIL, dpm.DISPLAY_NAME as DEPARTMENT_NAME , pst.DISPLAY_NAME as POSITION_NAME ,lktt.DISPLAY as EMPLOYEE_TYPE_NAME          ";
		$q .= "     ,CONCAT(emp.CODE,' - ',emp.FIRST_NAME,' ',emp.LAST_NAME) AS EMP_DISPLAY                                                                   ";
		$q .= " from user us                                                                                                                                  ";
		$q .= " inner join emp_info emp on us.emp_id = emp.id and emp.ACTIVE_FLAG = 'Y'                                                                       ";
		$q .= " left join lookup lkt on emp.TITLE_NAME_TYPE_ID = lkt.ID and lkt.GROUP = 'TITLE_NAME_TYPE' and lkt.ACTIVE_FLAG = 'Y'                           ";
		$q .= " left join position pst on emp.POSITION_ID = pst.ID and pst.ACTIVE_FLAG = 'Y'                                                                  ";
		$q .= " left join department dpm on pst.DEPARTMENT_ID = dpm.ID and dpm.ACTIVE_FLAG = 'Y'                                                              ";
		$q .= " left join lookup lktt on emp.TITLE_TYPE_ID = lktt.ID and lktt.ACTIVE_FLAG = 'Y'                                                               ";
		$q .= " where us.ACTIVE_FLAG = 'Y'                                                                                                                    ";

		$rows = $this->sql_exec($this->piping_dev, array(), $q);

		$res = array();

		foreach( $rows as $row ){
			if( $row['EMP_DISPLAY'] == '' ) continue;
			$text = mb_convert_encoding($row['EMP_DISPLAY'], "UTF-8", "UTF-8");;
			array_push( $res, array(
					'value'=> $row['USER_ID'],
					'text'=> $text
				)
			);
		}

		return $res;
	}

	public function getPPDevDetail($id){
		$this->load_piping_dev();

		$sql = "SELECT u.id, u.user_name, e.first_name, e.last_name, e.email, '3' role_id, '-' password
				FROM user u
				LEFT JOIN emp_info e on e.id = u.emp_id
				where u.id = ? and u.active_flag = 'Y' and e.active_flag = 'Y';";
		
		$rows = $this->sql_exec($this->piping_dev, array($id), $sql);
		return $rows;
	}

	public function insertUser($data){
		$rows = $this->getDetail($data['id']);
        if( count($rows) > 0 ) return array('result'=>false, 'message'=>"Duplicate data");

		$stmt = $this->db->prepare("INSERT INTO user (`user_id`, `username`, `password`, `first_name`, `last_name`, `email`, `role_id`, `active_flag`) VALUES (?,?,?,?,?,?,?,?);");
		$flag = 'Y';
		$stmt->bind_param("ssssssss", $data['id'], $data['user_name'], $data['password'], $data['first_name'], $data['last_name'], $data['email'], $data['role_id'], $flag);
		$result = $stmt->execute();
		
		$res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);

		return $res;
	}
}


// class Test {
	
// 	public $name;

// 	public function __construct() {
// 		$this->name = 'Mrinmoy Ghoshal';
// 	}

// 	public static function doWrite($name) {
// 		print ('Hello '.$name);
// 	}

// 	public function write() {
// 		print $this->name;
// 	}
// }
// Test::doWrite('Mrinmoy');