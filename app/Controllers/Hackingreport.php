<?php
namespace App\Controllers;

use App\Core\Controller;

class Hackingreport extends Controller
{
	public function index()
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$form = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
			if(isset($form["username"]) && isset($form["cheat"]) && isset($form["map"]))
			{
				$return = ["code" => "0", "message" => "Occurence registered successfully", "type" => "success"];
			}
			else
			{
				$return = ["code" => "-1", "message" => "Missing info", "type" => "error"];
			}

			echo json_encode($return);
		}
		else
		{
			header("Location: ".URL."/myaccount");
		}
	}
}