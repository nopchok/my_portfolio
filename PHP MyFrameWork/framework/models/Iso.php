<?php
require_once 'system/Model.php';
require_once 'libraries/utility.php';

class MdIso extends Model
{
	public function __construct(){
		parent::__construct();

		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	}


	public function get_document_aadata($data){
        if( $data ) $data = $data['post_data'];
        if( !isset($data['project_id']) ){
            $data['project_id'] = 0;
        }
        $columns = array(
            array("type" => "string" 		, "name" => "d.document_number"              ),
            array("type" => "string" 		, "name" => "d.document_desc"                 ),
            array("type" => "string" 		, "name" => "d.iso"                   ),
            array("type" => "string" 		, "name" => "d.iso_issued"            ),
            array("type" => "string" 		, "name" => "d.iso_issued_pct"            ),
            array("type" => "decimal" 		, "name" => "d.id"                           ),
        );
        
        $defLength = -1;
        $defOrder = 'order by d.document_number';

        $prjid = $data['project_id'];
        $table = "v_document d inner join project p on p.id = d.project_id and p.active_flag = 'Y' where p.id = $prjid and d.active_flag = 'Y'";

        $u = new Utility();
        return $u->getAaDataTable($this->db, $columns, $_POST, $table, $defLength, $defOrder);
    }

    
	public function get_project($data){
        $userid = isset($data['user_id']) ? $data['user_id'] : $_SESSION['id'];
        $q = "SELECT p.* FROM project p where p.active_flag = 'Y';";
        $q = "SELECT p.* FROM project_permission pp inner join project p on pp.project_id = p.id and p.active_flag = 'Y' where pp.user_id = $userid and pp.active_flag = 'Y' order by p.project_number;";
        // $params = array($_SESSION['id']);
        $params = array();
        
        $rows = $this->sql_exec($this->db, $params, $q);
        
		return $rows;
    }

    
    public function delete_document($data){
        $params = array($data['id']);
        $q = "UPDATE `document` set `active_flag` = 'D' where id = ?;";
        
        $rows = $this->sql_exec($this->db, $params, $q, false);

        $res = array('result'=>true, 'message'=>'');
        return $res;
    }
	public function get_document($data){
        $q = "SELECT p.* FROM document p where p.id = ?;";
        $params = array($data['id']);
        
        $rows = $this->sql_exec($this->db, $params, $q);
        
		return $rows;
    }
	public function update_document($data){
        $q = "UPDATE document set document_number = ? , document_desc = ? where id = ?;";
        $params = array($data['document_number'], $data['document_desc'], $data['id']);
        
        $rows = $this->sql_exec($this->db, $params, $q, false);
        
        $res = array('result'=>true, 'message'=>'');
        return $res;
    }
    public function insert_document($data){
        if( $data['document_number'] == '' || $data['document_desc'] == null ) return array('result'=>false, 'message'=>'Document is empty');
        
        $rows = $this->get_document_aadata(array('post_data'=>array('project_id'=>$data['project_id'])));
        $rows = $rows['data'];
        $isExist = false;
        foreach( $rows as $k => $v ){
            if( $v['document_number'] == $data['document_number'] ){
                $isExist = true;
                break;
            }
        }
        if( $isExist ) return array('result'=>false, 'message'=>"Duplicate data");

        $stmt = $this->db->prepare("INSERT INTO document (`project_id`, `document_number`, `document_desc`) VALUES (?, ?, ?);");
        $stmt->bind_param("sss", $data['project_id'], $data['document_number'], $data['document_desc']);
        $result = $stmt->execute();

        $res = array('result'=>$stmt->affected_rows > 0, 'message'=>$stmt->error);
        return $res;
    }


    public function insert_iso_lot($data){
        $params = array($data['document_id'], $data['title'], $data['date_issue']);
        $q = "INSERT INTO `iso_lot` (`document_id`, `title`, `date_issue`) values (?,?,?);";
        
        $rows = $this->sql_exec($this->db, $params, $q, false);

        $res = array('result'=>true, 'message'=>'');
        return $res;
    }
    public function update_iso_lot($data){
        $params = array($data['title'], $data['date_issue'], $data['id']);
        $q = "UPDATE `iso_lot` set `title` = ? , `date_issue` = ? where id = ?;";
        
        $rows = $this->sql_exec($this->db, $params, $q, false);

        $res = array('result'=>true, 'message'=>'');
        return $res;
    }
    public function delete_iso_lot($data){
        $params = array($data['id']);
        $q = "UPDATE `iso_lot` set `active_flag` = 'D' where id = ?;";
        
        $rows = $this->sql_exec($this->db, $params, $q, false);

        $res = array('result'=>true, 'message'=>'');
        return $res;
    }
    
	public function get_iso_lot($data){
        $q = "SELECT l.* from iso_lot l where l.document_id = ? and l.active_flag = 'Y';";
        $params = array($data['document_id']);
        
        $rows = $this->sql_exec($this->db, $params, $q);
        
		return $rows;
    }
	public function get_iso_dwg($data){
        $q = "SELECT p.id, p.line_id, p.line_num, p.pid, p.area, p.total_sheet FROM iso_dwg p where p.document_id = ? and p.active_flag = 'Y' order by p.line_id;";
        $params = array($data['document_id']);
        
        $rows = $this->sql_exec($this->db, $params, $q);
        
		return $rows;
    }
    



    

    public function get_iso_revision($data){
        $q = " SELECT * FROM iso_control.iso_revision where project_id = ?;  ";
        $params = array($data['project_id']);
        
        $rows = $this->sql_exec($this->db, $params, $q);
        
		return $rows;
    }
    public function save_iso_revision($data){
        foreach( $data as $v ){
            $q = 'update iso_revision set name = ? where id = ?;';
            $params = array($v['name'], $v['id']);
            $this->sql_exec($this->db, $params, $q, false);
        }

        $res = array('result'=>true, 'message'=>'');
		return $res;
    }

    public function get_cover_id($data){
        $q = '';
		$q .= " select p.id project_id, d.id document_id                ";
		$q .= " from project p                                ";
		$q .= " inner join document d on d.project_id = p.id  and p.active_flag = 'Y'  ";
		$q .= " where concat(p.project_number, ' - ', p.project_desc) = ? and d.document_number = ? and d.active_flag = 'Y';  ";
        $params = array($data['project'], $data['document']);
        
        $rows = $this->sql_exec($this->db, $params, $q);
        
		return $rows;
    }

    
    public function get_cover_document_all($data){
        
        $q = '';
		$q .= " select d.id, d.document_number, d.project_id  ";
		$q .= " from project p                                ";
		$q .= " left join document d on d.project_id = p.id and d.active_flag = 'Y'   ";
		$q .= " where concat(p.project_number, ' - ', p.project_desc) = ? and p.active_flag = 'Y' order by d.document_number;  ";
        $params = array($data['project']);
        
        $rows = $this->sql_exec($this->db, $params, $q);
        
		return $rows;
    }
    public function get_cover_project_all($data){
        $err = array(
            array('id'=>'','project_number'=>'', 'project_desc'=>'Please download new version -')
        );
        if( !isset($data['version']) ){
            return $err;
        }
        $rows = $this->sql_exec($this->db, array($data['version']), "select count(id) c from version where active_flag = 'Y' and version = ?;");
        if( count($rows) == 0 ) return $err;

        $q = '';
        if( isset($data['username']) ){
            $data['username'] .= '%';
            $q = " select id from user where email like ?;  ";
            $params = array($data['username']);
            $rows = $this->sql_exec($this->db, $params, $q);
            $data['user_id'] = count($rows) == 1 ? $rows[0]['id'] : 0;
            return $this->get_project($data);
        }

		$q .= " select p.id, p.project_number, p.project_desc              ";
		$q .= " from project p                                             ";
		$q .= " where p.active_flag = 'Y' order by p.project_number        ";
        $params = array();
        $rows = $this->sql_exec($this->db, $params, $q);
        
		return $rows;
    }



    public function get_cover_iso_all($data){
        set_time_limit(300);
        $iso_all = array();
        $data['get_cancel'] = 'get';
        $document_all = $this->get_cover_document_all($data);
        foreach( $document_all as $k => $document ){
            $data['document_id'] = $document['id'];
            $data['project_id'] = $document['project_id'];
            $iso_all = array_merge($iso_all, $this->get_iso_dwg_issue($data) );
        }
        return $iso_all;
    }
    public function get_cover_iso($data){
        $data['get_cancel'] = 'get';
        return $this->get_iso_dwg_issue($data);
    }
    public function get_cover_header($data){
        $q = '';
		$q .= " select p.project_number, p.project_desc                    ";
		$q .= " , d.document_number, d.document_desc                       ";
		$q .= " , l.title, l.date_issue                                    ";
		$q .= " from project p                                             ";
		$q .= " left join document d on p.id = d.project_id and d.id = ?  ";
		$q .= " left join iso_lot l on l.id = (select max(id) from iso_lot where document_id = ? and active_flag = 'Y') ";
		$q .= " where p.id = ?                                             ";
        $params = array($data['document_id'], $data['document_id'], $data['project_id']);
        
        $rows = $this->sql_exec($this->db, $params, $q);
        
		return $rows;
    }

	public function get_iso_dwg_issue($data){
        // $q = "SELECT iso.* FROM (SELECT @set_document_id:=? p) param, v_iso_dwg_issue iso";

        // $this->db->query("START TRANSACTION");

        $q = "select @set_document_id:= ?";
        $this->sql_exec($this->db, array($data['document_id']), $q);
        $q = "select @set_iso_lot_id:=if(max(id) is null, 0, max(id)) m from iso_lot where active_flag = 'Y' and document_id = ?";
        $this->sql_exec($this->db, array($data['document_id']), $q);
        $q = "select @set_project_id:= ?";
        $this->sql_exec($this->db, array($data['project_id']), $q);

        $q = '';
        $q .= " select z.*, ss.COL1, ss.COL2, ss.COL3, ss.COL4, ss.COL5, r.next_iso_revision_id, rn.name as CURRENT_ISO_REVISION                     ";
        $q .= " , doc.document_number                                                                                                                 ";
        // $q .= " from (select @set_document_id:= ?) xx,                                                                                               ";
        // $q .= " (select @set_iso_lot_id:=if(max(id) is null, 0, max(id)) m from iso_lot where active_flag = 'Y' and document_id = ?) yy,             ";
        // $q .= " (select @set_project_id:= ?) zz,                                                                                                     ";
        $q .= " from                                                                                                                                 ";
        $q .= " (                                                                                                                                    ";
        $q .= "     select iso.*, '' is_cancel, '".$data['document_id']."' as document_id from v_iso_dwg_issue iso                                   ";
        $q .= "     union all                                                                                                                        ";
        $q .= "     select ii.id                                                                                                                     ";
        $q .= "     , ifnull(concat(dd.id, '_', c.sheet_no), '') temp_id                                                                             ";
        $q .= "     , ifnull(dd.id, '') iso_dwg_id                                                                                                   ";
        $q .= "     , dd.line_id, dd.line_num, dd.area, dd.pid                                                                                       ";
        $q .= "     , ii.sheet_total total_sheet, c.sheet_no sheet                                                                                   ";
        $q .= "     , ii.sheet_no issue_sheet, ii.sheet_total issue_sheet_total, ii.line_id issue_line_id, ii.line_num issue_line_num                ";
        $q .= "     , ii.area issue_area, ii.pid issue_pid, ii.ISO_LOT_ID, ii.ISO_REVISION_ID, null, ii.ISO_LOT_ID, null is_issue                    ";
        $q .= "     , ii.remark issue_remark, 'Y' is_cancel                                                                                          ";
        $q .= "     , '".$data['document_id']."' as document_id                                                                                      ";
        $q .= "     from iso_dwg dd                                                                                                                  ";
        $q .= "     inner join v_iso_dwg_issue_cancel_sheet c on dd.id = c.iso_dwg_id                                                                ";
        $q .= "     inner join iso_dwg_issue ii on ii.id = c.max_id                                                                                  ";
        $q .= " ) z                                                                                                                                  ";
        $q .= " left join v_iso_revision_with_next r on r.ID_FOR_NEXT = z.lasted_iso_revision_id                                                     ";
        $q .= " left join iso_revision rn on rn.id = z.lasted_iso_revision_id                                                                        ";
        $q .= " left join                                                                                                                            ";
        $q .= " (                                                                                                                                    ";
        $q .= " 	select ii.iso_dwg_id, ii.sheet_no, d.document_number                                                                             ";
        $q .= " 	, if( CHAR_LENGTH(group_concat(l.date_issue)) - CHAR_LENGTH(REPLACE(group_concat(l.date_issue), ',', '')) + 1 < 1                ";
        $q .= " 		, NULL                                                                                                                       ";
        $q .= " 		, SUBSTRING_INDEX(SUBSTRING_INDEX(group_concat(l.date_issue ORDER BY l.id), ',', 1), ',', -1) ) COL1                         ";
        $q .= " 	, if( CHAR_LENGTH(group_concat(l.date_issue)) - CHAR_LENGTH(REPLACE(group_concat(l.date_issue), ',', '')) + 1 < 2                ";
        $q .= " 		, NULL                                                                                                                       ";
        $q .= " 		, SUBSTRING_INDEX(SUBSTRING_INDEX(group_concat(l.date_issue ORDER BY l.id), ',', 2), ',', -1) ) COL2                         ";
        $q .= " 	, if( CHAR_LENGTH(group_concat(l.date_issue)) - CHAR_LENGTH(REPLACE(group_concat(l.date_issue), ',', '')) + 1 < 3                ";
        $q .= " 		, NULL                                                                                                                       ";
        $q .= " 		, SUBSTRING_INDEX(SUBSTRING_INDEX(group_concat(l.date_issue ORDER BY l.id), ',', 3), ',', -1) ) COL3                         ";
        $q .= " 	, if( CHAR_LENGTH(group_concat(l.date_issue)) - CHAR_LENGTH(REPLACE(group_concat(l.date_issue), ',', '')) + 1 < 4                ";
        $q .= " 		, NULL                                                                                                                       ";
        $q .= " 		, SUBSTRING_INDEX(SUBSTRING_INDEX(group_concat(l.date_issue ORDER BY l.id), ',', 4), ',', -1) ) COL4                         ";
        $q .= " 	, if( CHAR_LENGTH(group_concat(l.date_issue)) - CHAR_LENGTH(REPLACE(group_concat(l.date_issue), ',', '')) + 1 < 5                ";
        $q .= " 		, NULL                                                                                                                       ";
        $q .= " 		, SUBSTRING_INDEX(SUBSTRING_INDEX(group_concat(l.date_issue ORDER BY l.id), ',', 5), ',', -1) ) COL5                         ";
        $q .= " 	from iso_dwg i                                                                                                                   ";
        $q .= " 	inner join document d on d.id = i.document_id                                                                                    ";
        $q .= " 	inner join iso_dwg_issue ii on ii.iso_dwg_id = i.id                                                                              ";
        $q .= " 	inner join iso_lot l on l.id = ii.iso_lot_id and l.active_flag = 'Y'                                                             ";
        $q .= " 	left join iso_revision r on r.id = ii.iso_revision_id                                                                            ";
        $q .= " 	where d.id = ?                                                                                                                   ";
        $q .= " 	group by ii.iso_dwg_id, ii.sheet_no                                                                                              ";
        $q .= " ) ss on ss.iso_dwg_id = z.iso_dwg_id and ss.sheet_no = z.sheet                                                                       ";
        $q .= " left join document doc on doc.id = z.document_id                                                                                     ";
        $q .= " where 1 = 1                                                                                                                          ";

        if( !isset($data['get_cancel']) ){
            $q .= " and z.is_cancel = '' ";
        }
        
        $q .= " order by z.line_id, z.sheet;                                                                                                         ";

        // if( $_SESSION['id'] == 2 ){
        //     echo $q;
        //     exit();
        // }

        if( !isset($data['iso_lot_id']) ) $data['iso_lot_id'] = 0;
        // $params = array($data['document_id'], $data['iso_lot_id']);
        $params = array($data['document_id']);
        
        $rows = $this->sql_exec($this->db, $params, $q);
        
		return $rows;
    }

    public function add_blank_revision($data){
        $iso_lot_id = 0;

        try{
            // $this->db->begin_transaction();
            $this->db->query("START TRANSACTION");

            foreach( $data['data'] as $k => $v ){
                $q = 'insert into iso_dwg_issue (iso_dwg_id, iso_lot_id, iso_revision_id, sheet_no, sheet_total) values (?,?,?,?,?);';
                $params =  array(
                    $v['iso_dwg_id'],
                    $iso_lot_id,
                    $v['next_iso_revision_id'],
                    $v['sheet'],
                    $v['total_sheet']
                );
                $this->sql_exec($this->db, $params, $q, false);
            }
            $this->db->commit();
            
            $res = array('result'=>true, 'message'=>'');
            return $res;
        }catch (Exception $e) {
            $this->db->rollback();
            
            $res = array('result'=>false, 'message'=>'Error');
            return $res;
        }
    }
    public function delete_blank_revision($data){
        $iso_lot_id = 0;

        try{
            // $this->db->begin_transaction();
            $this->db->query("START TRANSACTION");

            foreach( $data['data'] as $k => $v ){
                $q = 'delete from iso_dwg_issue where iso_dwg_id = ? and iso_lot_id = ? and iso_revision_id = ? and sheet_no = ? and sheet_total = ?;';
                $params =  array(
                    $v['iso_dwg_id'],
                    $iso_lot_id,
                    $v['lasted_iso_revision_id'],
                    $v['sheet'],
                    $v['total_sheet']
                );
                $this->sql_exec($this->db, $params, $q, false);
            }
            $this->db->commit();
            
            $res = array('result'=>true, 'message'=>'');
            return $res;
        }catch (Exception $e) {
            $this->db->rollback();
            
            $res = array('result'=>false, 'message'=>'Error');
            return $res;
        }
    }

    public function update_iso_issue($data){
        $iso_lot_id = $data['iso_lot_id'];
        
        try{
            // $this->db->begin_transaction();
            $this->db->query("START TRANSACTION");
            
            foreach( $data['data'] as $k => $v ){
                if( $v['id'] == null || $v['id'] == '' ){
                    $q = 'insert into iso_dwg_issue (iso_dwg_id, iso_lot_id, iso_revision_id, line_id, line_num, sheet_no, sheet_total, remark, pid, area) values (?,?,?,?,?,?,?,?,?,?);';
                    $params =  array(
                        $v['iso_dwg_id'],
                        $iso_lot_id,
                        $v['next_iso_revision_id'],
                        $v['line_id'],
                        $v['line_num'],
                        $v['sheet'],
                        $v['total_sheet'],
                        $v['issue_remark'],
                        $v['pid'],
                        $v['area']
                    );
                    $this->sql_exec($this->db, $params, $q, false);
                }else{
                    if( $v['is_issue'] == '' ){
                        $q = 'delete from iso_dwg_issue where id = ?;';
                        $params =  array($v['id']);
                        $this->sql_exec($this->db, $params, $q, false);
                    }else{
                        $params =  array(
                            $v['line_id'],
                            $v['line_num'],
                            $v['sheet'],
                            $v['total_sheet'],
                            $v['issue_remark'],
                            $v['pid'],
                            $v['area'],
                            $v['id']
                        );
                        $q = 'update iso_dwg_issue set line_id = ?, line_num = ?, sheet_no = ?, sheet_total = ?, remark = ?, pid = ?, area = ? where id = ?;';
                        $this->sql_exec($this->db, $params, $q, false);
                    }
                }
            }
            // echo $q;
            // print_r( $update_p );
            // print_r( $insert_p );
            // print_r( $insert_p );

            // $q = "UPDATE `iso_control`.`iso_dwg_issue` SET `REMARK`='sss' WHERE `id`='1';";
            // $q = "insert into iso_dwg_issue (id, line_id, line_num, sheet_no, sheet_total, remark, pid, area) values (1,'?','?','?','?','?','?','?');";
            // $q = "insert into iso_dwg_issue (iso_dwg_id, iso_lot_id, iso_revision_id, line_id, line_num, sheet_no, sheet_total, remark, pid, area) values (1,2,3,'?','?',1,1,'?','?','?');";
            // $this->db->query( $q );
            
            $this->db->commit();
            
            $res = array('result'=>true, 'message'=>'');
            return $res;
        }catch (Exception $e) {
            $this->db->rollback();
            
            $res = array('result'=>false, 'message'=>'Error');
            return $res;
        }
    }

	public function insert_iso_dwg($data){
        $insert_v = array();
        $params = array();
        $document_id = $data['document_id'];

        foreach( $data['data'] as $k => $v ){
            array_push( $insert_v, '(?,?,?,?,?,?)' );
            $total_sheet = $v[5];// == 0 ? 1 : $v[5];
            $params = array_merge( $params , array($document_id, $v[1], $v[2], $v[3], $v[4], $total_sheet) );
        }
        
        $q = "INSERT INTO `iso_dwg` (`document_id`, `line_id`, `line_num`, `pid`, `area`, `total_sheet`) values " . implode(',', $insert_v) . ';';
        
        $rows = $this->sql_exec($this->db, $params, $q, false);

        $res = array('result'=>true, 'message'=>'');
        return $res;
    }

    
	public function update_iso_dwg($data){
        $params = array();
        $document_id = $data['document_id'];

        $when = '';
        $where = array();
        $line_id = array();
        $line_num = array();
        $total_sheet = array();
        $pid = array();
        $area = array();
        $where_ = array();
        
        $active_flag = array();
        $when_active_flag = '';
        foreach( $data['data'] as $k => $v ){
            array_push( $where, '?' );

            $tmp = $v;
            unset($tmp['id']);
            if( implode('', $tmp) == '' ){
                $when_active_flag .= ' when ? then ? ';
                $active_flag = array_merge( $active_flag , array($v['id'], 'D') );
            }else{
                $when .= ' when ? then ? ';
                $line_id = array_merge( $line_id , array($v['id'], $v['line_id']) );
                $line_num = array_merge( $line_num , array($v['id'], $v['line_num']) );
                $total_sheet_n = $v['total_sheet'];// == 0 ? 1 : $v['total_sheet'];
                $total_sheet = array_merge( $total_sheet , array($v['id'], $total_sheet_n) );
                $pid = array_merge( $pid , array($v['id'], $v['pid']) );
                $area = array_merge( $area , array($v['id'], $v['area']) );
            }
            $where_ = array_merge( $where_ , array($v['id']) );
        }
        
        $set = array();
        if( $when != '' ){
            array_push( $set, "line_id = case id $when else line_id end" );
            array_push( $set, "line_num = case id $when else line_num end" );
            array_push( $set, "total_sheet = case id $when else total_sheet end" );
            array_push( $set, "pid = case id $when else pid end" );
            array_push( $set, "area = case id $when else area end" );
        }
        if( $when_active_flag != '' ){
            array_push( $set, "active_flag = case id $when_active_flag else active_flag end" );
        }

        $params = array_merge($line_id, $line_num, $total_sheet, $pid, $area, $active_flag, $where_);
        
        $q = "UPDATE iso_dwg set " . implode(',',$set) . " WHERE id in (". implode(',',$where) .");";
        // echo '<pre>';
        // echo $q;
        // print_r( $params );
        // return;

        $rows = $this->sql_exec($this->db, $params, $q, false);

        $res = array('result'=>true, 'message'=>'');
        return $res;
    }
}

