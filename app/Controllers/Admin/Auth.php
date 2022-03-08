<?php
namespace App\Controllers\Admin;

use \App\Core\Controller;

class Auth extends Controller
{
    public function index($arguments = [])
    {
        //$this->view("Admin/Auth/index");
        return header("Location: ".URL."/admin/dashboard");
    }
}