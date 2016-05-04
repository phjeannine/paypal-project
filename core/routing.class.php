<?php 
class routing{


	public static function getRouting()
	{
		$uri = $_SERVER['REQUEST_URI'];
		$array_uri = explode("/", trim($uri, "/"));
		$controller = empty($array_uri[0])?"index":$array_uri[0];
		unset($array_uri[0]);
		$action = empty($array_uri[1])?"index":$array_uri[1];
		unset($array_uri[1]);
		$args = array_merge($array_uri, $_POST);

		return ["c" => $controller, "a" => $action, "args" => $args];
	}


}