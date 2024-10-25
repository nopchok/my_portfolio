<?php
require_once 'Controller.php';

class ControllerError extends Controller
{
	public function __construct(){
		parent::__construct();
	}

	public function index()
	{
		$this->view->message = "404";
		$this->view->render('ControllerError_page.php');
	}
}