<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Main;

class Patchnotes extends Controller
{
    public function index()
    {
        $model = new Main();
        $return = $model->getPatchnotes();
        
        $this->view("Patchnotes/index", $return);
    }
}