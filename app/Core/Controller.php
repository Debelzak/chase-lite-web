<?php
namespace App\Core;

class Controller
{
    protected function model($model, $method, $arguments = [])
    {
        $modelClass = "\App\Models\\" . $model;
        $modelInstance = new $modelClass;
        return call_user_func_array([$modelInstance, $method], [$arguments]);
    }

    protected function view($view, $arguments = [])
    {
        $file = "../app/Views/".$view.".html";
        if(file_exists($file))
        {
            require_once($file);
        }
        else
        {
            die("View do not exists");
        }
    }

    protected function hasAdminLevel($level)
    {
        if(isset($_SESSION["loggedUser"]))
        {
            $username = $_SESSION["loggedUser"]["Login"];
            $model = new \App\Models\Main();
            $authLevel = (int)$model->getAdminAuth($username);
            
            if($authLevel >= $level)
            {
                return true;
            }
        }

        return false;
    }

    public function index()
    {
        
    }
}