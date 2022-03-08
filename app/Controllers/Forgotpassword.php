<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Mailer;
use ReCaptcha\ReCaptcha;

class Forgotpassword extends Controller
{
    public function index($arguments = [])
    {
        $this->view("Forgotpassword/index");
    }

    public function send()
    {
        $form = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        if(empty($form["username"]) || empty($form["email"]) || (RECAPTCHA_ENABLE && empty($form["g-recaptcha-response"])))
        {
            echo json_encode(["code" => "-3", "message" => "You must fill in all fields", "type" => "warning"]);
            return;
        }

        if(RECAPTCHA_ENABLE)
        {
            $reCaptcha = new ReCaptcha(RECAPTCHA_SECRET);
            $response = null;
            $response = $reCaptcha->verify($_POST["g-recaptcha-response"], $_SERVER["REMOTE_ADDR"]);

            if($response == null || !$response->isSuccess())
            {
                echo json_encode(["code" => "-1", "message" => "reCAPTCHA verification failed", "type" => "error"]);
                return;
            }
        }

        $args = [
            "email" => $form["email"],
            "username" => $form["username"]
        ];
        $model = $this->model("User", "searchAccountByEmailAndUser", $args);
        $accountFound = json_decode($model);
        
        if(!$accountFound)
        {
            echo json_encode(["code" => "-2", "message" => "The account could not be found", "type" => "error"]);
            return;
        }

        $username = $form["username"];
        $token = gen_uuid();
        $model = new \App\Models\Main();
        $model->insertAccountRecoveryToken($token, $username);
        $token_link = URL . "/forgotpassword/token/" . $token;
        
        $mailer = new Mailer();
        $destination = $form["email"];
        $subject = "Account Recovery";
        $body = file_get_contents("../app/Views/Forgotpassword/EmailTemplate.html");
        $body = str_replace("{USERNAME}", $username, $body);
        $body = str_replace("{TOKEN_LINK}", $token_link, $body);

        if($mailer->sendMail($destination, $subject, $body))
        {
            echo json_encode(["code" => "0", "message" => "A message was sent to your registered email", "type" => "success"]);
            return;
        } else {
            echo json_encode(["code" => "-99", "message" => "The email could not be sent, please try again later", "type" => "error"]);
            return;
        }
    }

    public function token($arguments)
    {
        //Change password with token from account recovery
        if(isset($arguments[0]))
        {
            $return["gotoContainer"] = true;

            $token = $arguments[0];
            $model = new \App\Models\Main();

            $targetToken = $model->consultAccountRecoveryToken($token);
            if(!empty($targetToken))
            {
                $now = new \DateTime("now");
                $expireDate = new \DateTime($targetToken->expireDate);
                
                if($targetToken->used)
                {
                    $return["alert"] = ["title" => "Invalid request", "message" => "The token has already been used", "type" => "error"];
                    return $this->view("Forgotpassword/index", $return);
                }
                else if($now > $expireDate)
                {
                    $return["alert"] = ["title" => "Invalid request", "message" => "Token expired, please request a new recovery", "type" => "error"];
                    return $this->view("Forgotpassword/index", $return);
                }
                else
                {
                    if($_SERVER['REQUEST_METHOD'] == 'POST')
                    {
                        $username = $targetToken->username;
                        $form = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                        if(empty($form["new_password"]) || empty($form["re_new_password"]))
                        {
                            $return["alert"] = ["title" => "Check fields", "message" => "You must fill in all fields", "type" => "warning"];
                        }
                        else if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\W]).{8,24}$/", $form["new_password"]))
                        {
                            $return["alert"] = ["title" => "Failed to change password", "message" => "The password must be at least 8 characters, case-sensitive, containing at least one number and one special character.", "type" => "warning"];
                        }
                        else if($form["new_password"] != $form["re_new_password"])
                        {
                            $return["alert"] = ["title" => "Failed to change password", "message" => "The passwords doesn't match", "type" => "warning"];
                        }
                        else
                        {
                            $userModel = new \App\Models\User();
                            $model->consumeAccountRecoveryToken($token);
                            $userModel->setPassword($username, $form["new_password"]);
                            $return["alert2"] = ["title" => "Your password has been changed!", "message" => "Now you can login into your account", "type" => "success"];
                            $login = new \App\Controllers\Login();
                            $login->index($return);
                        }

                        return $this->view("Forgotpassword/ChangePassword", $return);
                    }
                    else
                    {
                        $return["token"] = $arguments[0];
                        return $this->view("Forgotpassword/ChangePassword", $return);
                    }
                }
            } else {
                $this->index();
            }
        }
        else
        {
            $this->index();
        }
    }
}