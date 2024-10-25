<?php

require_once 'View.php';

class Controller
{
	public function __construct()
	{
		$this->view = new View();
	}
}