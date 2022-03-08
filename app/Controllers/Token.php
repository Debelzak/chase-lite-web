<?php
namespace App\Controllers;

use App\Core\Controller;
use ReCaptcha\ReCaptcha;

class Token extends Controller
{
    public function index($arguments = [])
    {
        if(isset($_SESSION["loggedUser"]))
        {
            $return = [];
            $username = $_SESSION["loggedUser"]["Login"];

            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $return["gotoContainer"] = true;
    
                if(RECAPTCHA_ENABLE) {
                    $reCaptcha = new ReCaptcha(RECAPTCHA_SECRET);
                    $response = null;
                    $response = $reCaptcha->verify($_POST["g-recaptcha-response"], $_SERVER["REMOTE_ADDR"]);
                }

                if(RECAPTCHA_ENABLE && ($response == null || !$response->isSuccess()))
                {
                    $return["alert"] = ["title" => "reCAPTCHA verification failed", "message" => "reCAPTCHA verification failed", "type" => "error"];
                    return $this->view("Token/index", $return);
                }
    
                $form = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                $tokenInserted = $form["1"];
    
                if(!preg_match("/^[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}$/", $tokenInserted))
                {
                    $return["alert"] = ["title" => "Invalid token", "message" => "Invalid token format", "type" => "error"];
                    return $this->view("Token/index", $return);
                }
    
                $model = new \App\Models\Main();
                $tokenConsult = $model->consultRewardToken($tokenInserted);
                $now = new \DateTime("now");
                $expireDate = new \DateTime($tokenConsult->expireDate);

                if(empty($tokenConsult))
                {
                    $return["alert"] = ["title" => "Invalid token", "message" => "The token you entered is invalid", "type" => "error"];
                    return $this->view("Token/index", $return);
                }
                else if($model->consultAlreadyUsedToken($username, $tokenConsult->id))
                {
                    $return["alert"] = ["title" => "Invalid token", "message" => "The token you entered has already been used", "type" => "error"];
                    return $this->view("Token/index", $return);
                }
                else if($now > $expireDate)
                {
                    $return["alert"] = ["title" => "Invalid token", "message" => "The token you enteres has expired", "type" => "error"];
                    return $this->view("Token/index", $return);
                }
                else if($tokenConsult->useCount >= $tokenConsult->maxUse)
                {
                    $return["alert"] = ["title" => "Invalid token", "message" => "The token you entered has already been used", "type" => "error"];
                    return $this->view("Token/index", $return);
                }
                else
                {
                    $return["TokenSerial"] = $tokenInserted;

                    if(isset($form["confirm"]))
                    {
                        $model->useToken($username, $tokenConsult->id);

                        $user = new \App\Models\User();
                        $loginUid = json_decode($user->getInfo(["username" => $username]))->LoginUID;

                        $postMailId = $user->insertPostMail($loginUid, "Token Redeem", "You recently redeemed a token through our website, here is your rewards.\n\nHave fun!\nChase Lite Network", 0);

                        $item = new \App\Models\User();
                        $rewards = $model->consultRewardTokenItems($tokenConsult->id);
                        for($i=0; $i<sizeof($rewards); $i++)
                        {
                            $count = ($rewards[$i]->Period != -1) ? $rewards[$i]->Period : $rewards[$i]->Count; 
                            $wigaUid = $item->insertPostWaitItem($loginUid, $rewards[$i]->ItemID, $rewards[$i]->Period, $rewards[$i]->Count);
                            $item->insertPostMailItem($postMailId, $loginUid, $wigaUid, $count);
                        }
                        
                        return $this->view("Token/Success");
                    }
                    else
                    {
                        $item = new \App\Models\User();
                        $rewards = $model->consultRewardTokenItems($tokenConsult->id);
        
                        for($i=0; $i<sizeof($rewards); $i++)
                        {
                            $return["tokenRewards"][$i]["Number"] = $i+1;
                            $return["tokenRewards"][$i]["ItemName"] = $item->getItemName($rewards[$i]->ItemID);
        
                            if($rewards[$i]->Period == -1)
                            {
                                if($rewards[$i]->Count == -1)
                                {
                                    $return["tokenRewards"][$i]["Count"] = "Permanent";
                                }
                                else
                                {
                                    $return["tokenRewards"][$i]["Count"] =  $rewards[$i]->Count . " units";
                                }
                            }
                            else
                            {
                                $return["tokenRewards"][$i]["Count"] = $rewards[$i]->Period . " Days";
                            }
                        }

                        return $this->view("Token/RewardOverview", $return);
                    }
                }
            }
            else
            {
                return $this->view("Token/index", $return);
            }
        }
        else
        {
            $arguments = ["urlRedirect" => URL."/token/"];
            $this->view("Login/index", $arguments);
        }

    }
}