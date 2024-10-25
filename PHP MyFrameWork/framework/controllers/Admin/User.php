<?php
require_once 'MyController.php';
require_once 'libraries/utility.php';
require_once 'framework/models/Admin.php';

class AdminUser extends MyController
{
	public function __construct(){
		parent::__construct();

        $this->path = 'admin/user';
        
        $this->check_session();
        $this->check_permission();
        
        $this->post_data = $_POST;
	}

	public function index($id = 0)
	{
		$this->view->title = 'Admin > User';
		$this->view->data = $_SESSION;
		
		$this->view->render('framework/views/template/header.php');
		$this->view->render('framework/views/admin/user.php');
		$this->view->render('framework/views/template/footer.php');
	}
	
	public function insert_ws_user()
	{
		require_once 'framework/models/User.php';
        $MdUser = new User();
        $res = $MdUser->insert_ws_user($this->post_data);
		echo json_encode($res);
    }
	public function get_ws_user()
	{
		require_once 'framework/models/User.php';
        $MdUser = new User();
        $res = $MdUser->get_ws_user($this->post_data);
		echo json_encode($res);
    }

	public function get_user()
	{
        $mdAdmin = new MdAdmin();
        $res = $mdAdmin->get_user($this->post_data);
		echo json_encode($res);
    }
	public function save_user()
	{
        $mdAdmin = new MdAdmin();
        $action = $this->post_data['action'];
        $res = $mdAdmin->{$action}($this->post_data);
		echo json_encode($res);
	}
	
	
	public function get_user_aadata()
	{
        $md = new MdAdmin();
        $res = $md->get_user_aadata($this->post_data);
		echo json_encode($res);
    }
}