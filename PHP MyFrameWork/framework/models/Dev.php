<?php
require_once 'system/Model.php';
require_once 'libraries/utility.php';

class MdDev extends Model
{
	public function __construct(){
		parent::__construct();

		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	}


	public function get_menu_aadata($data){
        $columns = array(
            array("type" => "string" 		, "name" => "m.text"              ),
            array("type" => "string" 		, "name" => "m.parent_id"  , 'search'=>'p.text'     ),
            array("type" => "string" 		, "name" => "m.url"               ),
            array("type" => "string" 		, "name" => "m.icon_class"        ),
            array("type" => "decimal" 		, "name" => "m.sequence"          ),
            array("type" => "decimal" 		, "name" => "m.id"                ),
            array("type" => "string" 		, "name" => "p.text"       , "as" => 'parent'       ), // for search
        );
        
        $defLength = -1;
        $defOrder = 'order by m.sequence';

        $table = "menu m left join menu p on p.id = m.parent_id where m.active_flag = 'Y'";

        $u = new Utility();
        return $u->getAaDataTable($this->db, $columns, $_POST, $table, $defLength, $defOrder);
    }


	public function get_menu($data){
        if( isset($data['id']) && isset($data['role_id']) ){
            $q = "SELECT m.*, p.text parent, pm.permission FROM menu m left join menu p on p.id = m.parent_id left join (select if(permission is null, 'N', permission) permission, menu_id from permission where role_id = ?) pm on pm.menu_id = m.id where m.id = ?;";
            $params = array($data['role_id'], $data['id']);
        }else if( isset($data['role_id']) ){
            $q = "SELECT m.*, p.text parent, pm.permission FROM menu m left join menu p on p.id = m.parent_id left join (select if(permission is null, 'N', permission) permission, menu_id from permission where role_id = ?) pm on pm.menu_id = m.id where m.active_flag = 'Y' and m.sequence >= 0 order by m.sequence;";
            $params = array($data['role_id']);
        }else if( isset($data['url']) ){
            $q = "SELECT m.*, p.text parent FROM menu m left join menu p on p.id = m.parent_id where m.url = ? and m.active_flag = 'Y' order by m.sequence;";
            $params = array($data['url']);
        }else{
            $q = "SELECT m.*, p.text parent FROM menu m left join menu p on p.id = m.parent_id where m.active_flag = 'Y' order by m.sequence;";
            $params = array();
        }
        
        $rows = $this->sql_exec($this->db, $params, $q);
        
		return $rows;
    }
    
    public function delete_menu($data){
        $stmt = $this->db->prepare("UPDATE menu set active_flag = 'N' where id = ?;");
		$stmt->bind_param("i", $data['id']);
        $result = $stmt->execute();
        
        $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        
        return $res;
    }
    
    public function update_menu($data){
        if( $data['text'] == '' || $data['text'] == null ) return array('result'=>false, 'message'=>'text is empty');

        $stmt = $this->db->prepare("UPDATE menu set `text`=?, parent_id=?, url=?, icon_class=?, sequence=? where id = ?;");
		$stmt->bind_param("ssssii", $data['text'], $data['parent_id'], $data['url'], $data['icon_class'], $data['sequence'], $data['id']);
        $result = $stmt->execute();
        
        $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        
        return $res;
    }
    
    public function insert_menu($data){
        if( $data['text'] == '' || $data['text'] == null ) return array('result'=>false, 'message'=>'text is empty');
        
        if( $data['url'] != '' ){
            $rows = $this->get_menu(array('url'=>$data['url']));
            if( count($rows) > 0 ) return array('result'=>false, 'message'=>"Duplicate URL");
        }
        
        $stmt = $this->db->prepare("INSERT INTO menu (`text`,`icon_class`,`url`,`parent_id`,`sequence`) VALUES (?,?,?,?,?);");
		$stmt->bind_param("ssssi", $data['text'], $data['icon_class'], $data['url'], $data['parent_id'], $data['sequence']);
        $result = $stmt->execute();
        
        $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        
        return $res;
    }












    public function get_role_aadata($data){
        $columns = array(
            array("type" => "string" 		, "name" => "r.role"              ),
            array("type" => "string" 		, "name" => "r.id"                ),
        );
        
        $defLength = -1;
        $defOrder = 'order by r.role';

        $table = "role r where r.active_flag = 'Y'";

        $u = new Utility();
        return $u->getAaDataTable($this->db, $columns, $_POST, $table, $defLength, $defOrder);
    }
	public function get_role($data){
        $params = array();
        if( isset($data['id']) ){
            $q = "SELECT * FROM role where id = ? and active_flag = 'Y';";
            $params = array($data['id']);
        }else if( isset($data['role']) ){
            $q = "SELECT * FROM role where role = ? and active_flag = 'Y';";
            $params = array($data['role']);
        }else{
            $q = "SELECT * FROM role where active_flag = 'Y' order by role;";
        }
        
		$rows = $this->sql_exec($this->db, $params, $q);
		return $rows;
    }
    
    public function delete_role($data){
        if( $data['id'] <= 3 ){
            return array('result'=>false, 'message'=>'cannot delete main role');
        }
        $stmt = $this->db->prepare("UPDATE role set active_flag = 'N' where id = ?;");
		$stmt->bind_param("i", $data['id']);
        $result = $stmt->execute();
        
        $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        
        return $res;
    }
    
    public function update_role($data){
        if( $data['role'] == '' || $data['role'] == null ) return array('result'=>false, 'message'=>'role is empty');

        $stmt = $this->db->prepare("UPDATE role set `role`=? where id = ?;");
		$stmt->bind_param("si", $data['role'], $data['id']);
        $result = $stmt->execute();
        
        $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        
        return $res;
    }
    
    public function insert_role($data){
        if( $data['role'] == '' || $data['role'] == null ) return array('result'=>false, 'message'=>'role is empty');
        
		$rows = $this->get_role(array('role'=>$data['role']));
        if( count($rows) > 0 ) return array('result'=>false, 'message'=>"Duplicate data");

        $stmt = $this->db->prepare("INSERT INTO role (`role`) VALUES (?);");
        $stmt->bind_param("s", $data['role']);
        $result = $stmt->execute();

        $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        return $res;
    }


    public function get_permission_aadata($data){
        $searchPermission = "case if( p.permission is null, 'N', p.permission ) when 'V' then 'View' when 'E' then 'Edit' else 'None' end";
        $columns = array(
            array("type" => "string" 		, "name" => "m.id"       ,'as'=>'menu_id'     ,'search'=>'m.text'    ),
            array("type" => "string" 		, "name" => "r.id"       ,'as'=>'role_id'     ,'search'=>'r.role'    ),
            array("type" => "string" 		, "name" => "if( p.permission is null, 'N', p.permission )"     , 'as'=>'permission_id'   ,'search'=>$searchPermission      ),
            array("type" => "string" 		, "name" => "p.id"                ),
        );
        
        $defLength = -1;
        $defOrder = 'order by r.role, m.sequence, m.text';

        $table = "menu m left join role r on r.id > 0 and r.active_flag = 'Y' left join permission p on p.role_id = r.id and p.menu_id = m.id and p.active_flag = 'Y' where m.active_flag = 'Y'";

        $u = new Utility();
        return $u->getAaDataTable($this->db, $columns, $_POST, $table, $defLength, $defOrder);

    }
    public function get_permission($data){
        $q = '';
        $q .= "select @permission := if( p.permission is null, 'N', p.permission ) permission_id  ";
        $q .= ", m.id menu_id, r.id role_id, p.id                                                                                ";
        $q .= "from menu m                                                                                                                                     ";
        $q .= "left join role r on r.id > 0 and r.active_flag = 'Y'                                                                                            ";
        $q .= "left join permission p on p.role_id = r.id and p.menu_id = m.id and p.active_flag = 'Y'                                                         ";
        $q .= "where m.active_flag = 'Y'                                                                                                                       ";
        if( isset($data['url']) ) $q .= ' and m.url = ' . json_encode($data['url']) . ' ';
        if( isset($data['role_id']) ) $q .= ' and r.id = ' . json_encode($data['role_id']) . ' ';
        $q .= "order by r.id, m.sequence, m.id;                                                                                                                ";
        
        $rows = $this->sql_exec($this->db, array(), $q);
        
		return $rows;
    }
    
    public function insert_permission($data){
        $q = "INSERT INTO permission (menu_id, role_id, permission) VALUES (?, ?, ?);";
        $stmt = $this->db->prepare($q);
        $stmt->bind_param("sss", $data['menu_id'], $data['role_id'], $data['permission_id']);
        
        $result = $stmt->execute();

        $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        
        return $res;
    }
    
    public function update_permission($data){
        if( $data['permission_id'] == '' || $data['permission_id'] == null ) return array('result'=>false, 'message'=>'permission is empty');

        $q = "UPDATE permission set permission = ? where id=?;";
        $stmt = $this->db->prepare($q);
        $stmt->bind_param("ss", $data['permission_id'], $data['id']);
        
        $result = $stmt->execute();
        
        $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        
        return $res;
    }
}

