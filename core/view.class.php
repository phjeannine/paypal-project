<?php
class view{

	private $layout;
	private $view;
	private $data;

	public function __construct($view, $layout = "layout"){
		try{
			//Est ce que le layout existe ???
			$layout_path = APPLICATION_PATH."/views/".$layout.".php";
			if(file_exists($layout_path)){
				$this->layout=$layout_path;
			}else{
				throw new Exception("Layout inconnu");
			}
			//Est ce que le layout existe ???
			$view_path = APPLICATION_PATH."/views/".$view.".php";
			if(file_exists($view_path)){
				$this->view=$view_path;
			}else{
				throw new Exception("vue inconnue");
			}
			//Est ce que la vue existe ???
		}catch(Exception $e){
			echo "Erreur : ".$e->getMessage();
		}		
	}

	public function assign($cle, $valeur){
		$this->data[$cle]=$valeur;
	}

	public function __destruct(){
		extract($this->data);
		include $this->layout;
		die();
	}



}









