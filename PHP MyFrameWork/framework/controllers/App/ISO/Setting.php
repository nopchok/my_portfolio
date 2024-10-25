<?php
require_once 'MyController.php';

class AppIsoSetting extends MyController
{
	public function __construct(){
		parent::__construct();

        $this->path = 'app/iso/setting';
        
        $this->check_session();
        $this->check_permission();
        
        $this->post_data = $_POST;
        
        $request_uri = $_SERVER['REQUEST_URI'];
        $parsed_url = parse_url($request_uri);
        $query_params = array();

        if (isset($parsed_url['query'])) {
            parse_str($parsed_url['query'], $query_params);
        }
        $this->get_data = $query_params;
	}

	public function index($id = 0)
	{
		$this->view->title = 'App > ISO > Setting';
		$this->view->data = $_SESSION;
		$this->view->get_data = $this->get_data;
		
		$this->view->render('framework/views/template/header.php');
		$this->view->render('framework/views/app/iso/setting.php');
		$this->view->render('framework/views/template/footer.php');
	}

}