<?php
namespace App\Controllers\Admin;

use \App\Core\Controller;

class Dashboard extends Controller
{
    public function index($arguments = [])
    {
        $this->view("Admin/Dashboard/index", $arguments);
    }
}