<?php
namespace App\Controllers;

use \App\Core\App;
use \App\Core\Controller;

class Admin extends Controller
{
    public function index($arguments = [])
    {
        if(!$this->hasAdminLevel(10))
        {
            return header("Location: ".URL."");
        }

        $controller = "Dashboard";
        $method = "index";

        if(!empty($arguments[0]))
        {
            if(file_exists("../app/Controllers/Admin/" . ucwords($arguments[0]) . ".php"))
            {
                $controller = ucfirst($arguments[0]);
                unset($arguments[0]);
            }
        }

        //if(!isset($_SESSION["adminAuth"]))
        //{
        //    $controller = "Auth";
        //    $method = "index";
        //}

        $controllerClass = "\App\Controllers\Admin\\" . $controller;
        $controllerInstance = new $controllerClass;
        
        if(isset($arguments[1]))
        {
            if(method_exists($controllerInstance, $arguments[1]))
            {
                $method = $arguments[1];
                unset($arguments[1]);
            }
        }

        
        $arguments = $arguments ? array_values($arguments) : [];
        App::$currentController = $controller;
        call_user_func_array([$controllerInstance, $method], [$arguments]);
    }
}