<?php
session_start();

$root = (isset($_SERVER['HTTPS']) ? "https://" : "http://").$_SERVER['HTTP_HOST'];
$script_name = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);



if( strpos($root, 'localhost')==-1 || strpos($root, '127.0.0.1')==-1 ) error_reporting(0);


define ('__LOCAL__', str_replace('iso_control\index.php', '', __FILE__) );

define ('__SALT__', 'Fho8G**g&0ds43syK0PKPph&^D64fi7g1k-90`j*G&7IVGD');



define ('__SITE__', $root);
define ('__ROOT__', $root.$script_name);
define ('__APP_URL__', 'iso_control');


define ('__HOSTNAME__', 'localhost');
define ('__DATABASE__', 'iso_control');

if( __SITE__ == 'http://server' ){
	define ('__USERNAME__', 'root');
	define ('__PASSWORD__', 'admin');
}else{
	define ('__USERNAME__', 'root');
	define ('__PASSWORD__', '');
}

require_once 'system/Route.php';


new Route();