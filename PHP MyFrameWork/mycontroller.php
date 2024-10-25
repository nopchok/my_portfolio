<?php
require_once 'system/Controller.php';

class MyController extends Controller
{
	public function __construct(){
        parent::__construct();
    }
    
    public function check_session(){
        if(count($_SESSION)==0){
			header('Location: http://'.$_SERVER['HTTP_HOST']);
			exit();
        }
    }
    
    public function check_permission(){
        require_once 'framework/models/Dev.php';
        $mdDev = new MdDev();
        $rows = $mdDev->get_permission( array('url'=>$this->path, 'role_id'=>$_SESSION['role_id']) );
        
        if( count($rows) == 0 ){
            echo 'Unauthorize';
            exit();
        }else if( count($rows) == 1 ){
            if( $rows[0]['permission_id'] == 'N'){
                echo 'Unauthorize';
                exit();
            }else{
                $this->permission_id = $rows[0]['permission_id'];
                $_SESSION['permission_id'] = $rows[0]['permission_id'];
            }
        }else{
            echo 'Error controller';
            exit();
        }
    }
}