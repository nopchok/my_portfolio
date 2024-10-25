<?php
require_once 'MyController.php';

class Test extends MyController
{
	public function __construct(){
		parent::__construct();

        $this->path = 'test';
        
        $this->check_session();
        $this->check_permission();
	}

	public function index($id = 0)
	{
		$this->view->title = 'Test';
		$this->view->data = $_SESSION;
		
		$this->view->render('framework/views/template/header.php');
		$this->view->render('framework/views/test/index.php');
		$this->view->render('framework/views/template/footer.php');
	}
}