<?php
namespace App\Controllers;

use App\Core\Controller;

class Login extends Controller 
{
    public function index($arguments = [])
    {
        $arguments["urlRedirect"] = URL;
        return $this->view("Login/index", $arguments);
    }

    public function auth()
    {
        $form = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $urlRedirect = (isset($form["urlRedirect"])) ? $form["urlRedirect"] : URL;

        if(empty($form["login"]) || empty($form["password"])) {
            echo json_encode(["code" => "-1","type" => "warning","message" => "You must fill in all fields"]);
            return;
        }

        $arguments = array("username" => $form["login"]);
        $model = $this->model("User", "getInfo", $arguments);
        $return = json_decode($model);

        if(isset($return->Login) && $return->Login == $form["login"] && $return->passwd == md5($form["password"])) {
            $_SESSION["loggedUser"] = (array)$return;
            echo json_encode(["code" => "0", "type" => "success", "message" => "Logged in successfully, redirecting...", "url" => $urlRedirect]);
        } else {
            echo json_encode(["code" => "-2", "type" => "warning", "message" => "Invalid username or password"]);
        }
    }
}