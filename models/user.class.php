<?php
class user extends model{
	
	protected $name;
	protected $surname;

	public function __construct(){
		parent::__construct();
	}

	public function setName($name){
		$this->name=$name;
	}

	public function setSurname($surname){
		$this->surname=$surname;	
	}

}