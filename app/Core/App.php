<?php
namespace App\Core;

class App
{
    public static $currentController;

    private $controller = "Home";
    private $method = "index";
    private $arguments = [];

    public function __construct()
    {
        $url = $this->getUrl();

        if(isset($url[0]))
        {
            if(file_exists("../app/Controllers/" . ucwords($url[0]) . ".php"))
            {
                $this->controller = ucfirst($url[0]);
                unset($url[0]);
            }
        }

        $controllerClass = "\App\Controllers\\" . $this->controller;
        $controllerInstance = new $controllerClass;
        
        if(isset($url[1]))
        {
            if(method_exists($controllerInstance, $url[1]))
            {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        
        $this->arguments = $url ? array_values($url) : [];
        App::$currentController = $this->controller;
        call_user_func_array([$controllerInstance, $this->method], [$this->arguments]);
    }

    public function getUrl()
    {
        $url = filter_input(INPUT_GET, "url", FILTER_SANITIZE_URL);
        if(isset($url))
        {
            $url = explode("/", $url);
            return $url;
        }
        return null;
    }
}
