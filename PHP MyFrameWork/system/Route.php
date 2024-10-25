<?php

class Route
{
	public function __construct() 
	{
		try{
			// 1. router
			$url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
			$url = ltrim(rtrim($url, '/'), '/');
		}catch(Exception $e) {
			// echo __SITE__;
			exit();
		}
		$tokens = explode('/',$url);
		// echo '<pre>';
		// print_r($_SERVER);
		// print_r($tokens);
		// echo '</pre>';

		$actionName = '';
		if( count($_POST) > 0 || $_SERVER['REQUEST_METHOD'] == 'POST' ){
			$actionName = array_pop($tokens);
		}


		$start = 0;
		// 2. Dispatcher
		if( count($tokens) == 0 ){
			$tokens = explode('/',$url.'/index');
			$actionName = '';
		}
		
		$controller = array_slice( $tokens, $start+1 );
		$controller = array_map('ucfirst', $controller);
		$controllerName = implode($controller);
		$controller = 'framework/controllers/' . implode('/', $controller) . '.php';
		// echo $controller;
		// print_r($_SESSION);
		// exit();

		if (file_exists($controller)) {
			require_once($controller);
		}else{
			require_once('system/ControllerError.php');
			$controllerName = 'ControllerError';
		}

		$controller = new $controllerName;
		if( $actionName == '' ){
			$controller->index();
		}else{
			$controller->{$actionName}();
		}

		/*
		$controllerName = ucfirst($tokens[$start+1]);
		if (file_exists('controllers/'.$controllerName.'.php')) {
			require_once('controllers/'.$controllerName.'.php');
			$controller = new $controllerName;
			if (isset($tokens[$start+2])) {
				$actionName = $tokens[$start+2];
				if(isset($tokens[$start+3])) {
					$controller->{$actionName}($tokens[$start+3]);	
				} else {
					$controller->{$actionName}();
				}
			} else {
				// default action
				$controller->index();
			}
		}else{
			require_once('system/ControllerError.php');
			$controllerName = 'ControllerError';
			$controller = new $controllerName;
			$controller->index();
		}
		*/
	}
}