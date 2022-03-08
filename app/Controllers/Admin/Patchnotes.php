<?php
namespace App\Controllers\Admin;

use \App\Core\Controller;
use \App\Models\Main;

class Patchnotes extends Controller
{
    public function index($arguments = [])
    {
        $model = new Main();
        $arguments["patchnotes"] = $model->getPatchnotes();
        $this->view("Admin/Patchnotes/index", $arguments);
    }

    public function insert($arguments = [])
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $model = new Main();

            $form = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $important = (isset($form["important"]) && $form["important"] == "on") ? 1 : 0;
            $banner = URL . "/images/patchnotes/_noimage.png";
            
            //Image upload
            if(isset($_FILES['banner']))
            {
                switch($_FILES['banner']['type'])
                {
                    case "image/jpeg" : $extension = "jpg"; break;
                    case "image/png" : $extension = "png"; break;
                    default: $extension = null; break;
                }
                
                $uploadOk = ($extension != null && $_FILES['banner']['size'] < 2097152) ? true : false;
                
                if($uploadOk)
                {
                    $target_dir = __DIR__ . "/../../../public/images/patchnotes/";
                    $target_name = gen_uuid() . "." . $extension;
                    $target_file = $target_dir . $target_name;
                    if(move_uploaded_file($_FILES['banner']['tmp_name'], $target_file))
                    {
                        $banner = URL . "/images/patchnotes/" .  $target_name;
                    }
                }
            }
            //Image upload
            

            $model->insertPatchnote($form["title"], $_POST["body"], $banner, $important);

            $arguments["alert"] = ["title" => "Inserted successfully!", "message" => "The patch note was inserted", "type" => "success"];
            return $this->index($arguments);
        }

        $this->view("Admin/Patchnotes/insert", $arguments);
    }

    public function edit($arguments = [])
    {
        $id = $arguments[0];
        $model = new Main();
        $arguments["patchnote"] = $model->getPatchnote($id);
        if(!empty($arguments["patchnote"]->id))
        {
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $form = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                $important = (isset($form["important"]) && $form["important"] == "on") ? 1 : 0;
                $banner = URL . "/images/patchnotes/_noimage.png";

                if(!empty($form["bannerFileName"]) && $form["bannerFileName"] != $banner)
                {
                    $banner = $arguments["patchnote"]->banner;
                }
                
                //Image upload
                if(isset($_FILES['banner']))
                {
                    switch($_FILES['banner']['type'])
                    {
                        case "image/jpeg" : $extension = "jpg"; break;
                        case "image/png" : $extension = "png"; break;
                        default: $extension = null; break;
                    }
                    
                    $uploadOk = ($extension != null && $_FILES['banner']['size'] < 2097152) ? true : false;
                    
                    if($uploadOk)
                    {
                        $target_dir = __DIR__ . "/../../../public/images/patchnotes/";
                        $target_name = gen_uuid() . "." . $extension;
                        $target_file = $target_dir . $target_name;
                        if(move_uploaded_file($_FILES['banner']['tmp_name'], $target_file))
                        {
                            $banner = URL . "/images/patchnotes/" .  $target_name;
                        }
                    }
                }
                //Image upload
                

                $model->updatePatchnote($id, $form["title"], $_POST["body"], $banner, $important);
                $arguments["patchnote"] = $model->getPatchnote($id);

                $arguments["alert"] = ["title" => "Edited successfully!", "message" => "The patch note was edited", "type" => "success"];
            }

            return $this->view("Admin/Patchnotes/edit", $arguments);
        }
        else
        {
            return $this->index($arguments);
        }
    }

    public function delete($arguments = [])
    {
        $id = $arguments[0];
        $model = new Main();
        if($model->deletePatchnote($id))
        {
            $arguments["alert"] = ["title" => "Deleted successfully!", "message" => "The patch note was deleted", "type" => "success"];
        }
        else
        {
            $arguments["alert"] = ["title" => "Delete failed!", "message" => "Could not find the especified patch note", "type" => "error"];
        }

        return $this->index($arguments);
    }
}