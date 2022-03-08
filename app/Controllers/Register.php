<?php
namespace App\Controllers;

use App\Core\Controller;
use ReCaptcha\ReCaptcha;

class Register extends Controller
{
    public function index()
    {
        if(isset($_SESSION["loggedUser"]))
        {
            header("Location: ".URL."/myaccount");
        }

        $arguments = [];

        //If sended form
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $arguments["gotoContainer"] = true;
            
            if(RECAPTCHA_ENABLE) {
                $reCaptcha = new ReCaptcha(RECAPTCHA_SECRET);
                $response = null;
                $response = $reCaptcha->verify($_POST["g-recaptcha-response"], $_SERVER["REMOTE_ADDR"]);
            }

            $form = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            if(!ALLOW_NEW_USERS)
            {
                $arguments["alert"] = ["title" => "Registration not allowed", "message" => "New users registration are currently disabled", "type" => "warning"];
                return $this->view("Register/index", $arguments);
            }
            
            //Verify reCAPTCHA
            if(RECAPTCHA_ENABLE && ($response == null || !$response->isSuccess()))
            {
                $arguments["alert"] = ["title" => "Registration failed", "message" => "reCAPTCHA verification failed", "type" => "error"];
                return $this->view("Register/index", $arguments);
            }

            //Verify terms of use
            if($form["terms"] != "on")
            {
                $arguments["alert"] = ["title" => "Registration failed", "message" => "You must accept the terms of use", "type" => "error"];
                return $this->view("Register/index", $arguments);
            }

            //Verify username format
            if(!preg_match("/^[0-9a-zA-Z]{5,24}$/", $form["login"]))
            {
                $arguments["alert"] = ["title" => "Registration failed", "message" => "The User ID must have only letters and numbers between 5 and 24 characters", "type" => "error"];
                return $this->view("Register/index", $arguments);
            }

            //Verify username in use
            $args = array("username" => $form["login"]);
            $model = $this->model("User", "getInfo", $args);
            $return = json_decode($model);
            if(!empty($return))
            {
                $arguments["alert"] = ["title" => "Registration failed", "message" => "The entered User ID is already being used", "type" => "error"];
                return $this->view("Register/index", $arguments);
            }

            //Verify password format
            if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\W]).{8,24}$/", $form["password"]))
            {
                $arguments["alert"] = ["title" => "Registration failed", "message" => "Invalid password format", "type" => "error"];
                return $this->view("Register/index", $arguments);
            }

            //Verify password confirmation
            if ($form["password"] != $form["confirm_password"])
            {
                $arguments["alert"] = ["title" => "Registration failed", "message" => "The password confirmation doesn't match", "type" => "error"];
                return $this->view("Register/index", $arguments);
            }

            //Verify email format
            if (!preg_match("/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/", $form["email"]))
            {
                $arguments["alert"] = ["title" => "Registration failed", "message" => "You entered an invalid email format", "type" => "error"];
                return $this->view("Register/index", $arguments);
            }

            //Verify email in use
            $args = array("email" => $form["email"]);
            $model = $this->model("User", "checkEmailInUse", $args);
            $emailInUse = json_decode($model);

            if ($emailInUse)
            {
                $arguments["alert"] = ["title" => "Registration failed", "message" => "The entered email is already being used", "type" => "error"];
                return $this->view("Register/index", $arguments);
            }

            //Verify gender format
            if ($form["gender"] != 0 && $form["gender"] != 1)
            {
                $arguments["alert"] = ["title" => "Registration failed", "message" => "You entered an invalid gender", "type" => "error"];
                return $this->view("Register/index", $arguments);
            }

            //Successful registration
            $args = [
                "username" => $form["login"],
                "password" => $form["password"],
                "email" => $form["email"],
                "gender" => $form["gender"]
            ];

            $model = $this->model("User", "insert", $args);
            $success = json_decode($model);

            if($success)
            {
                return $this->view("Register/success", $arguments);
            }
            else
            {
                $arguments["alert"] = ["title" => "Registration failed", "message" => "Unknown error", "type" => "error"];
                return $this->view("Register/index", $arguments);
            }
        }
        else
        {
            return $this->view("Register/index", $arguments);
        }
    }
}