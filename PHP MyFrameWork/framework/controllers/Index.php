<?php
require_once 'MyController.php';
require_once 'framework/models/User.php';
require_once 'framework/models/Dev.php';

class Index extends MyController
{
	public function __construct(){
		parent::__construct();
		
        $this->post_data = $_POST;
	}

	public function index($id = 0)
	{
		// login
		if( count($_SESSION) > 0 ){
			header('Location: app/iso');
		}else if( count($_POST) > 0 ){
			$user = new User();

			$USER_ID = $_POST['USER_ID'];
			$s = $user->getDetail($USER_ID);
			
			if( count($s) == 1 ){
				$_SESSION = $s[0];
			}else{
				$pp = $user->getPPDevDetail($USER_ID);
				if( count($pp) == 1 ){
					$res = $user->insertUser($pp[0]);
					$_SESSION = $pp[0];
				}
			}
			// print_r($_SESSION);
			// exit();

			header('Location: app/iso');
		}else{
			header('Location: '.$_SERVER['HTTP_ORIGIN']);
		}
		exit();

		// $user = new User();
		// echo $user->getAge();

		// $this->view->message = 'Hello World from Index controller index action!' . $id;
		
		// $this->view->render('views/template/header.php');
		// $this->view->render('views/index/index.php');
		// $this->view->render('views/template/footer.php');
	}

	public function init_database(){
		if($_SESSION['role_id'] != 1 ) exit();
		
		$q = file_get_contents("system/DatabaseInit.sql");
		$db = Db::getInstance();
		
		$result = $db->multi_query($q);
		$res = array('result'=>$result);
		
		echo json_encode($res);
	}


	public function get_menu(){
        $md = new MdDev();
		$res = $md->get_menu(array('role_id'=>$_SESSION['role_id']));
		
		$out = array();
		foreach( $res as $k => $v ){
			if( $v['permission'] != 'N' && $v['permission'] != null ) array_push($out, $v);
		}
		echo json_encode($out);
	}
}