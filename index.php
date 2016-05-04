<?php 

define('APPLICATION_PATH',realpath(dirname(__FILE__)));


function includeCore($class){
    if(file_exists("core/".$class.".class.php")){
        include "core/".$class.".class.php";
    }
}

function includeModels($class){
    if(file_exists("models/".$class.".class.php")){
        include "models/".$class.".class.php";
    }
}

spl_autoload_register("includeCore");
spl_autoload_register("includeModels");

$route = routing:: getRouting();



//Est ce qu'il y a un fichier pour le controller
$name_controller = $route["c"]."Controller";
$path_controller = "controllers/".$name_controller.".class.php";

try{
    if(file_exists($path_controller))
    {
        include $path_controller;
        $c = new $name_controller;
        //Vérifier que dans ma class il y ai une méthode
        //du nom de $action
        $name_action = $route["a"]."Action";
        if(method_exists($c, $name_action)){
            $c->$name_action($route["args"]);
        }else{
            throw new Exception("L'action n'existe pas.");
        }
    }else
    {
        throw new Exception("Le controller n'existe pas.");
    }
}catch(Exception $e){
    $v = new view("404");
    $v->assign("erreur", $e->getMessage());
    
}










