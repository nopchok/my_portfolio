<?php
require_once 'MyController.php';

class Signout extends MyController
{
	public function __construct(){
		parent::__construct();
	}

	public function index($id = 0)
	{
		session_unset();
		session_destroy();
		session_write_close();
		setcookie(session_name(),'',0,'/');
		session_regenerate_id(true);
		header('Location: '.__SITE__);
	}
}