<?php
require_once 'MyController.php';
require_once 'libraries/utility.php';
require_once 'framework/models/Admin.php';

class AdminProject extends MyController
{
	public function __construct(){
		parent::__construct();

        $this->path = 'admin/project';
        
        $this->check_session();
        $this->check_permission();
        
        $this->post_data = $_POST;
	}

	public function index($id = 0)
	{
		$this->view->title = 'Admin > Project';
		$this->view->data = $_SESSION;
		
		$this->view->render('framework/views/template/header.php');
		$this->view->render('framework/views/admin/project.php');
		$this->view->render('framework/views/template/footer.php');
	}
	
	
	public function save_project_permission()
	{
        $mdAdmin = new MdAdmin();
        $action = $this->post_data['action'];
        $res = $mdAdmin->{$action}($this->post_data);
		echo json_encode($res);
	}
	
	
	public function get_all_user()
	{
        $mdAdmin = new MdAdmin();
        $res = $mdAdmin->get_all_user($this->post_data);
		echo json_encode($res);
    }
	public function get_project_permission_aadata()
	{
        $mdAdmin = new MdAdmin();
        $res = $mdAdmin->get_project_permission_aadata($this->post_data);
		echo json_encode($res);
    }

	public function get_project_aadata()
	{
        $mdAdmin = new MdAdmin();
        $res = $mdAdmin->get_project_aadata($this->post_data);
		echo json_encode($res);
    }
	
	public function get_project()
	{
        $mdAdmin = new MdAdmin();
        $res = $mdAdmin->get_project($this->post_data);
		echo json_encode($res);
    }
	public function save_project()
	{
        $mdAdmin = new MdAdmin();
        $action = $this->post_data['action'];
        $res = $mdAdmin->{$action}($this->post_data);
		echo json_encode($res);
	}
}