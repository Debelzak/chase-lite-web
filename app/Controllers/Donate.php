<?php
namespace App\Controllers;

use Exception;
use App\Core\Controller;
use Lib\PaypalIPN\PaypalIPN;

class Donate extends Controller
{
    public function index()
    {
        if(isset($_SESSION["loggedUser"]))
        {
            if(isset($_SESSION["gc_terms"]))
            {
                $this->view("Donate/donate");
            } else {
                $this->view("Donate/terms");
            }
        } else {
            $this->view("Login/index", ["urlRedirect" => URL . "/donate/"]);
        }
    }

    public function notification()
    {   
        if($_SERVER["REQUEST_METHOD"] != "POST")
        {
            echo json_encode(["code" => "-1", "message" => "Nothing received", "type" => "error"]);
            return; 
        }

        $ipn = new PaypalIPN();

        if(PAYPAL_USE_SANDBOX)
        {
            $ipn->useSandbox();
        }

        $verified = $ipn->verifyIPN();
        
        if ($verified) {
            // Check receiver email
            if(strtolower($_POST["receiver_email"]) != strtolower(PAYPAL_RECEIVE_EMAIL)) {
                throw new Exception('RECEIVER EMAIL MISMATCH');
            }
            
            // data
            $item_name = $_POST['item_name'];
            $item_number = $_POST['item_number'];
            $payment_status = $_POST['payment_status'];
            $payment_amount = $_POST['mc_gross'];
            $payment_currency = $_POST['mc_currency'];
            $receiver_email = $_POST['receiver_email'];
            $payer_email = $_POST['payer_email'];
            $user_id = $_POST['custom'];

            $model = new \App\Models\Donation;
            if(empty($model->getByReference($item_number)))
            {
                $model->insertPaymentLog($user_id, $item_number, $payer_email, $payment_status, $payment_amount, "PayPal");
            }
    
            if($_POST['payment_status'] == 'Completed') 
            {
                $model->changePaymentStatus($item_number, $payment_status);
                $model->applyDonation($item_number);
            }
            else
            {
                $model->changePaymentStatus($item_number, $payment_status);
            }
        }

        header("HTTP/1.1 200 OK");
    }
}