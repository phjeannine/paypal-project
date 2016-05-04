<?php
class userController{

	public function indexAction($args){

		$v = new view("userIndex");
		$v->assign("mesargs", $args);
	}


	public function insertAction($args){

		$user= new user();
		$user->setName($args["name"]);
		$user->setSurname($args["surname"]);
		$user->save();

		$v = new view("userIndex");
		$v->assign("mesargs", $args);
	}


}