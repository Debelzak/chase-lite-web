<?php
namespace App\Controllers;

use App\Core\Controller;
use ReCaptcha\ReCaptcha;

class Myaccount extends Controller
{
    public function index()
    {
        if(isset($_SESSION["loggedUser"]))
        {
            $model = new \App\Models\User();
            $login = $_SESSION["loggedUser"]["Login"];

            $user = json_decode($model->getInfo(["username" => $login]));

            $login = $user->Login;
            $email = $user->email;
            $lastConnection = date_format(date_create($user->lastLogin), "m-d-Y h:iA");
            $nickname = $model->getNickname($login);
            $cash = $model->getCashUser($login);
            $cash = (!empty($cash)) ? number_format($cash->Cash) : 0;

            $characters = $model->getCharacters($login);
            $characterInfo = [];

            for($i = 0; $i < sizeof($characters); $i++)
            {
                $characterInfo[$i]["CharacterName"] = getCharacterName($characters[$i]->CharType);
                $characterInfo[$i]["PromotionName"] = getCharacterJobName($characters[$i]->CharType, $characters[$i]->Promotion);
                $characterInfo[$i]["Nickname"] = $nickname;
                $characterInfo[$i]["Level"] = number_format($characters[$i]->Level);
                $characterInfo[$i]["Win"] = number_format($characters[$i]->Win);
                $characterInfo[$i]["Lose"] = number_format($characters[$i]->Lose);
                $characterInfo[$i]["Exp"] = number_format($characters[$i]->ExpS4);
            }

            $arguments = [
                "userInfo" => [
                    "Login" => $login,
                    "Email" => $email,
                    "LastConnection" => $lastConnection,
                    "Cash" => $cash
                ],
                "Characters" => $characterInfo
            ];

            $this->view("MyAccount/index", $arguments);
        }
        else
        {
            $arguments = ["urlRedirect" => URL."/myaccount/"];
            $this->view("Login/index", $arguments);
        }
    }

    public function changepassword($arguments)
    {
        if(!isset($_SESSION["loggedUser"]))
        {
            $arguments["gotoContainer"] = true;
            return $this->view("Login/index", ["urlRedirect" => URL . "/myaccount/changepassword"]);
        }

        $model = new \App\Models\User();
        $user = json_decode($model->getInfo(["username" => $_SESSION["loggedUser"]["Login"]]));
        
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $arguments["gotoContainer"] = true;

            if(RECAPTCHA_ENABLE) {
                $reCaptcha = new ReCaptcha(RECAPTCHA_SECRET);
                $response = null;
                $response = $reCaptcha->verify($_POST["g-recaptcha-response"], $_SERVER["REMOTE_ADDR"]);
            }

            $form = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            if(empty($form["password"]) || empty($form["new_password"]) || empty($form["re_new_password"]))
            {
                $arguments["alert"] = ["title" => "Check fields", "message" => "You must fill in all fields", "type" => "warning"];
            }
            else if(RECAPTCHA_ENABLE && ($response == null || !$response->isSuccess()))
            {
                $arguments["alert"] = ["title" => "Failed to change password", "message" => "reCAPTCHA verification failed", "type" => "error"];
            }
            else if($user->passwd != md5($form["password"]))
            {
                $arguments["alert"] = ["title" => "Failed to change password", "message" => "Your current password is incorrect", "type" => "warning"];
            }
            else if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\W]).{8,24}$/", $form["password"]))
            {
                $return["alert"] = ["title" => "Failed to change password", "message" => "The password must be at least 8 characters, case-sensitive, containing at least one number and one special character.", "type" => "warning"];
            }
            else if($form["new_password"] != $form["re_new_password"])
            {
                $arguments["alert"] = ["title" => "Failed to change password", "message" => "The passwords doesn't match", "type" => "warning"];
            }
            else
            {
                $model->setPassword($_SESSION["loggedUser"]["Login"], $form["new_password"]);
                $arguments["alert"] = ["title" => "Password changed", "message" => "Your password has been changed", "type" => "success"];
            }
        }

        $this->view("MyAccount/changepassword", $arguments);
    }

    public function donationlog($arguments)
    {
        if(!isset($_SESSION["loggedUser"]))
        {
            $arguments["gotoContainer"] = true;
            return $this->view("Login/index", ["urlRedirect" => URL . "/myaccount/donationlog"]);
        }

        $page = 1;
        if(isset($arguments[0]))
        {
            $page = (int)$arguments[0];
            $page = ($page != 0) ? $page : 1;
        }

        $model = new \App\Models\Donation();
        $userDonation = $model->getUserDonations($_SESSION["loggedUser"]["Login"]);

        $list = [];
        for($i=0; $i<sizeof($userDonation); $i++)
        {
            $list[$i]["number"] = $i+1;
            $list[$i]["date"] = date_format(date_create($userDonation[$i]->regDate), "m-d-Y h:iA");
            $list[$i]["reference"] = $userDonation[$i]->reference;
            $list[$i]["value"] = number_format($userDonation[$i]->amount, 2, ",", " ");
            $list[$i]["status"] = $userDonation[$i]->status;
        }

        $return["userDonations"] = $list;

        $this->view("MyAccount/donationlog", $return);
    }

    public function logout()
    {
        if(isset($_SESSION["loggedUser"]))
        {
            unset($_SESSION["loggedUser"]);
        }
        header("Location: ".URL."/home");
    }
}