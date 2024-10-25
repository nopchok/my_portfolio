<?php
require_once 'MyController.php';
require_once 'framework/models/Admin.php';
require_once 'framework/models/Dev.php';

class AdminSection extends MyController
{
	public function __construct(){
		parent::__construct();

        $this->path = 'admin/section';
        
        $this->check_session();
        $this->check_permission();
        
        $this->post_data = $_POST;
	}

	public function index($id = 0)
	{
		$this->view->title = 'Admin > Section';
		$this->view->data = $_SESSION;
		
		$this->view->render('framework/views/template/header.php');
		$this->view->render('framework/views/admin/section.php');
		$this->view->render('framework/views/template/footer.php');
    }


	
	
	public function get_section_user_aadata()
	{
        $mdAdmin = new MdAdmin();
        $res = $mdAdmin->get_section_user_aadata($this->post_data);
		echo json_encode($res);
    }

	public function get_section_user()
	{
        $mdAdmin = new MdAdmin();
        $res = $mdAdmin->get_section_user($this->post_data);
		echo json_encode($res);
    }
	public function save_section_user()
	{
        $mdAdmin = new MdAdmin();
        $action = $this->post_data['action'];
        $res = $mdAdmin->{$action}($this->post_data);
		echo json_encode($res);
	}


}