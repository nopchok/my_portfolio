<?php
require_once 'MyController.php';
require_once 'framework/models/Dev.php';

class DevMenu extends MyController
{
	public function __construct(){
		parent::__construct();

        $this->path = 'dev/menu';
        
        $this->check_session();
        $this->check_permission();

        $this->post_data = $_POST;
	}

	public function index($id = 0)
	{
		$this->view->title = 'Dev > Menu';
		$this->view->data = $_SESSION;
		
		$this->view->render('framework/views/template/header.php');
		$this->view->render('framework/views/dev/menu.php');
		$this->view->render('framework/views/template/footer.php');
    }

	public function get_menu_aadata()
	{
        $md = new MdDev();
        $res = $md->get_menu_aadata($this->post_data);
		echo json_encode($res);
    }
	public function get_menu()
	{
        $md = new MdDev();
        $res = $md->get_menu($this->post_data);
		echo json_encode($res);
    }
	public function save_menu()
	{
        $md = new MdDev();
        $action = $this->post_data['action'];
        $res = $md->{$action}($this->post_data);
		echo json_encode($res);
    }
}