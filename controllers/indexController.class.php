<?php
class indexController{

	public function indexAction($args){

		$v = new view("indexIndex");
		$v->assign("mesargs", $args);
	}	

	public function contactAction($args){

		
	}	

}