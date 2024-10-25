<?php
require_once 'MyController.php';
require_once 'framework/models/Dev.php';

class DevRole extends MyController
{
	public function __construct(){
		parent::__construct();

        $this->path = 'dev/role';
        
        $this->check_session();
        $this->check_permission();
        
        $this->post_data = $_POST;
	}

	public function index($id = 0)
	{
		$this->view->title = 'Dev > Role';
		$this->view->data = $_SESSION;
		
		$this->view->render('framework/views/template/header.php');
		$this->view->render('framework/views/dev/role.php');
		$this->view->render('framework/views/template/footer.php');
    }

	public function get_role_aadata()
	{
        $md = new MdDev();
        $res = $md->get_role_aadata($this->post_data);
		echo json_encode($res);
    }
	public function get_permission_aadata()
	{
        $md = new MdDev();
        $res = $md->get_permission_aadata($this->post_data);
		echo json_encode($res);
	}
	



	public function get_role()
	{
        $md = new MdDev();
        $res = $md->get_role($this->post_data);
		echo json_encode($res);
    }
	public function save_role()
	{
        $md = new MdDev();
        $action = $this->post_data['action'];
        $res = $md->{$action}($this->post_data);
		echo json_encode($res);
	}
	
	

	public function get_permission()
	{
        $md = new MdDev();
        $res = $md->get_permission($this->post_data);
		echo json_encode($res);
    }
	public function save_permission()
	{
        $md = new MdDev();
        $action = $this->post_data['action'];
        $res = $md->{$action}($this->post_data);
		echo json_encode($res);
	}
	
	
    
	public function get_menu(){
        $md = new MdDev();
		$res = $md->get_menu(null);
		echo json_encode($res);
	}

}