<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Main;

class Home extends Controller
{
    public function index()
    {
        //Get slides
        $arguments = ["all" => false, "limit" => 10];
        $model = $this->model("Main", "getSliders", $arguments);
        $sliders = json_decode($model);

        //Get important news
        $importantNews = [];
        $model = new Main();
        $return = $model->getPatchnotes(2, true);
        
        for($i=0; $i<2; $i++)
        {
            if(empty($return[$i])) {
                $return[$i] = new \stdClass();
                $return[$i]->title = "----------------------------------";
            }
            $important = [
                "title" => $return[$i]->title
            ];
            array_push($importantNews, $important);
        }

        //Get regular news
        $regularNews = [];
        $model = new Main();
        $return = $model->getPatchnotes(3);
        for($i=0; $i<3; $i++)
        {
            switch(true)
            {
                case $i==0: $boxClass = "box amy"; break;
                case $i==1: $boxClass = "box ronan"; break;
                case $i==2: $boxClass = "box lass"; break;
                default: $boxClass = "box amy"; break;
            }

            if(empty($return[$i])) {
                $return[$i] = new \stdClass();
                $return[$i]->title = "----------------------------------";
                $return[$i]->regDate = "1970-01-01";
            }

            $date = date_create($return[$i]->regDate);
            $regDate = date_format($date, "m-d-Y");

            $regular = [
                "title" => $return[$i]->title,
                "regDate" => $regDate,
                "boxClass" => $boxClass
            ];

            array_push($regularNews, $regular);
        }

        $this->view("Home/index", ["sliders" => $sliders, "importantNews" => $importantNews, "regularNews" => $regularNews]);
    }
}