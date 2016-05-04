<?php
class model{

	private $pdo;
	private $table;

	public function __construct(){
		try{
			$this->pdo = new PDO("mysql:dbname=mabdd;host=localhost","root","root");
			$this->table = get_called_class();
		}catch(Exception $e){
			die();
		}
	}

	public function save(){
		$data = get_object_vars($this);
		unset($data["pdo"]);
		unset($data["table"]);
		foreach ($data as $key => $value) {
			$sql_columns[]= ":".$key;
		}
		$request = $this->pdo->prepare('INSERT INTO '.$this->table.'
			('.implode(",", array_keys($data)).')
		 	VALUES ('.implode(',', $sql_columns).')');

		$success = $request->execute($data);
	}

}