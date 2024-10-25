<?php
require_once 'system/Model.php';

class MdAdmin extends Model
{
	public function __construct(){
		parent::__construct();

		// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    }
    




	public function get_team_user($data){
		$params = array();
        if( isset($data['id']) ){
            $q = "SELECT * FROM team_user where id = ? and active_flag = 'Y';";
            $params = array($data['id']);
        }else if( isset($data['team_id']) && isset($data['user_id']) && isset($data['project_id']) ){
            $q = "SELECT tu.* FROM team_user tu inner join user u on u.id = tu.user_id and u.active_flag = 'Y' inner join team t on t.active_flag = 'Y' and t.id = tu.team_id where t.project_id = ? and tu.user_id = ? and tu.active_flag = 'Y';";
            $params = array($data['project_id'], $data['user_id']);
        }else if( isset($data['project_id']) ){
            $q = "SELECT tu.* FROM team_user tu inner join user u on u.id = tu.user_id and u.active_flag = 'Y' inner join team t on t.active_flag = 'Y' and t.id = tu.team_id where t.project_id = ? and tu.active_flag = 'Y';";
            $params = array($data['project_id']);
        }
        
		$rows = $this->sql_exec($this->db, $params, $q);
		return $rows;
    }
    
    public function delete_team_user($data){
        $stmt = $this->db->prepare("UPDATE team_user set active_flag = 'N' where id = ?;");
		$stmt->bind_param("i", $data['id']);
        $result = $stmt->execute();
        
        $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        
        return $res;
    }
    
    public function update_team_user($data){
        if( $data['user_id'] == '' || $data['user_id'] == null ) return array('result'=>false, 'message'=>'User is empty');
        if( $data['team_id'] == 0 ) return array('result'=>false, 'message'=>'Team is empty');

        $stmt = $this->db->prepare("UPDATE team_user set `team_id`=?, `user_id`=?, `lead_flag`=? where id = ?;");
		$stmt->bind_param("sssi", $data['team_id'], $data['user_id'], $data['lead_flag'], $data['id']);
        $result = $stmt->execute();
        
        $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        
        return $res;
    }
    
    public function insert_team_user($data){
        if( $data['user_id'] == '' || $data['user_id'] == null ) return array('result'=>false, 'message'=>'User is empty');
        if( $data['team_id'] == 0 ) return array('result'=>false, 'message'=>'Team is empty');
        
		$rows = $this->get_team_user($data);
        if( count($rows) > 0 ) return array('result'=>false, 'message'=>"Duplicate data");

        $stmt = $this->db->prepare("INSERT INTO team_user (`team_id`, `user_id`, `lead_flag`) VALUES (?, ?, ?);");
        $stmt->bind_param("sss", $data['team_id'], $data['user_id'], $data['lead_flag']);
        $result = $stmt->execute();

        $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        return $res;
    }













	public function get_team($data){
        $params = array();
        if( isset($data['id']) ){
            $q = "SELECT * FROM team where id = ? and active_flag = 'Y';";
            $params = array($data['id']);
        }else if( isset($data['team']) && isset($data['project_id']) ){
            $q = "SELECT * FROM team where project_id = ? and team = ? and active_flag = 'Y';";
            $params = array($data['project_id'], $data['team']);
        }else if( isset($data['project_id']) ){
            $q = "SELECT * FROM team where project_id = ? and active_flag = 'Y';";
            $params = array($data['project_id']);
        }
        
		$rows = $this->sql_exec($this->db, $params, $q);
		return $rows;
    }
    
    public function delete_team($data){
        $stmt = $this->db->prepare("UPDATE team set active_flag = 'N' where id = ?;");
		$stmt->bind_param("i", $data['id']);
        $result = $stmt->execute();
        
        $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        
        return $res;
    }
    
    public function update_team($data){
        $stmt = $this->db->prepare("UPDATE team set `project_id`=?, `team`=? where id = ?;");
		$stmt->bind_param("ssi", $data['project_id'], $data['team'], $data['id']);
        $result = $stmt->execute();
        
        $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        
        return $res;
    }
    
    public function insert_team($data){
        if( $data['team'] == '' || $data['team'] == null ) return array('result'=>false, 'message'=>'team is empty');
        
		$rows = $this->get_team($data);
        if( count($rows) > 0 ) return array('result'=>false, 'message'=>"Duplicate data");

        $stmt = $this->db->prepare("INSERT INTO team (`project_id`, `team`) VALUES (?, ?);");
        $stmt->bind_param("ss", $data['project_id'], $data['team']);
        $result = $stmt->execute();

        $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        return $res;
    }





    
    public function get_project_permission_aadata($data){
        $id = $data['post_data']['project_id'];

        $columns = array(
            array("type" => "string" 		, "name" => "u.email"            ),
            array("type" => "string" 		, "name" => "p.id"               ),
        );
        
        $defLength = -1;
        $defOrder = 'order by u.email';

        $table = "project_permission p left join user u on u.id = p.user_id and u.active_flag = 'Y' where p.project_id = $id and p.active_flag = 'Y'";
        
        $u = new Utility();
        return $u->getAaDataTable($this->db, $columns, $_POST, $table, $defLength, $defOrder);
    }
    
    public function get_project_aadata($id){
        $columns = array(
            array("type" => "string" 		, "name" => "p.project_number"              ),
            array("type" => "string" 		, "name" => "p.project_desc"               ),
            array("type" => "string" 		, "name" => "p.id"               ),
        );
        
        $defLength = -1;
        $defOrder = 'order by p.project_number';

        $table = "project p where p.active_flag = 'Y'";

        $u = new Utility();
        return $u->getAaDataTable($this->db, $columns, $_POST, $table, $defLength, $defOrder);
    }
	public function get_project($data){
        $params = array();
        if( isset($data['id']) ){
            $q = "SELECT * FROM project where id = ? and active_flag = 'Y';";
            $params = array($data['id']);
        }else if( isset($data['project']) ){
            $q = "SELECT * FROM project where project = ? and active_flag = 'Y';";
            $params = array($data['project']);
        }else{
            $q = "SELECT * FROM project where active_flag = 'Y' order by id desc;";
        }
        
		$rows = $this->sql_exec($this->db, $params, $q);
		return $rows;
    }
    
    public function delete_project($data){
        $stmt = $this->db->prepare("UPDATE project set active_flag = 'N' where id = ?;");
		$stmt->bind_param("i", $data['id']);
        $result = $stmt->execute();
        
        $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        
        return $res;
    }
    
    public function update_project($data){
        $res = array('result'=>false, 'message'=>'Error');

        if( isset($data['id']) ){
            $stmt = $this->db->prepare("UPDATE project set `project_number`=?, `project_desc`=? where id = ?;");
            $stmt->bind_param("ssi", $data['project_number'], $data['project_desc'], $data['id']);
            $result = $stmt->execute();

            $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        }

        return $res;
    }
    
    public function insert_project($data){
        $stmt = $this->db->prepare("INSERT INTO project (`project_number`, `project_desc`) VALUES (?, ?);");
        $stmt->bind_param("ss", $data['project_number'], $data['project_desc']);
        $result = $stmt->execute();

        $projects = $this->get_project(array());
        $project = $projects[ 0 ];
        $id = $project['id'];
        $q = "CALL `iso_control`.`SAVE_ISO_REVISION_FIRST`(?);";
        $this->sql_exec($this->db, array($id), $q, false);
        
        $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        return $res;
    }



    
    public function delete_project_permission($data){
        $stmt = $this->db->prepare("UPDATE project_permission set active_flag = 'D' where id = ?;");
        $stmt->bind_param("i", $data['id']);
        $result = $stmt->execute();

        $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        return $res;
    }

    public function insert_project_permission($data){
        $stmt = $this->db->prepare("INSERT INTO project_permission (`project_id`, `user_id`, `active_flag`) VALUES (?, ?, 'Y');");
        $stmt->bind_param("ss", $data['project_id'], $data['user_id']);
        $result = $stmt->execute();

        $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        return $res;
    }


    
	public function get_all_user($data){
		$q = '';
		$q .= " select us.ID ";
		$q .= "     ,CONCAT(us.USERNAME,' - ',us.FIRST_NAME,' ',us.LAST_NAME) AS EMP_DISPLAY   ";
		$q .= " from user us                                                                     ";
		$q .= " where us.ACTIVE_FLAG = 'Y'                                                       ";

		$rows = $this->sql_exec($this->db, array(), $q);

		$res = array();

		foreach( $rows as $row ){
			if( $row['EMP_DISPLAY'] == '' || $row['ID'] == '1' ) continue;
			$text = mb_convert_encoding($row['EMP_DISPLAY'], "UTF-8", "UTF-8");;
			array_push( $res, array(
					'value'=> $row['ID'],
					'text'=> $text
				)
			);
		}

		return $res;
	}
    public function get_user_aadata($id){
        $columns = array(
            array("type" => "string" 		, "name" => "u.username"              ),
            array("type" => "string" 		, "name" => "u.first_name"               ),
            array("type" => "string" 		, "name" => "u.last_name"               ),
            array("type" => "string" 		, "name" => "u.email"               ),
            array("type" => "string" 		, "name" => "u.role_id"      ,'search'=>'r.role'         ),
            array("type" => "string" 		, "name" => "u.id"               ),
        );
        
        $defLength = -1;
        $defOrder = 'order by u.email';

        $table = "user u left join role r on r.id = u.role_id where u.active_flag = 'Y'";

        $u = new Utility();
        return $u->getAaDataTable($this->db, $columns, $_POST, $table, $defLength, $defOrder);
    }
	public function get_user($id){
        $q = "SELECT u.* FROM user u where u.active_flag = 'Y';";
        
		$rows = $this->sql_exec($this->db, array(), $q);
		return $rows;
	}
    
	public function update_user($data){
        if( $data['role_id'] == '' || $data['role_id'] == null ) return array('result'=>false, 'message'=>'role is empty');

        $q = "UPDATE user set role_id = ?, first_name = ?, last_name = ?, email = ? where id=?;";
        $stmt = $this->db->prepare($q);
        $stmt->bind_param("sssss", $data['role_id'], $data['first_name'], $data['last_name'], $data['email'], $data['id']);
        
        $result = $stmt->execute();
        
        $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        
        return $res;
    }
    
	public function delete_user($data){
        $q = "UPDATE user set active_flag = 'D' where id=?;";
        $stmt = $this->db->prepare($q);
        $stmt->bind_param("s", $data['id']);
        
        $result = $stmt->execute();
        
        $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        
        
        return $res;
    }
    



    

}

