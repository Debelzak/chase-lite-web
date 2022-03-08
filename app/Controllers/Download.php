<?php
namespace App\Controllers;

use App\Core\Controller;

class Download extends Controller
{
    public function index()
    {
        $this->view("Download/index");
    }
}