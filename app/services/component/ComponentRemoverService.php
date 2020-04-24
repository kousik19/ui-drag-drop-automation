<?php 

class ComponentRemoverService
{
    public function execute($params) {

        try{
            $category = $params['category'];
            $file = $params['file'];
            $name;

            $categories = json_decode(file_get_contents(__DIR__ . "/../../customUIComponents/category.json"));
            
            for($i = 0; $i < count($categories); $i++) {
                if(trim($category) == trim($categories[$i]->display)) {
                    $name = $categories[$i]->name;
                    break;
                }
            }
            
            if(!isset($name)) {
                $obj = new stdClass;
                $obj->status = "error";
                $obj->message = "Category not found";
                echo json_encode($obj);
                die;
            }

            $components = json_decode(file_get_contents(__DIR__ . "/../../customUIComponents/store/$name/componentList.json"));

            for($i = 0; $i < count($components); $i++) {
                if($components[$i]->content == $file) {
                    $components[$i]->enable = "false";
                    break;
                }
            }

            $json_string = json_encode($components, JSON_PRETTY_PRINT);
            file_put_contents(__DIR__ . "/../../customUIComponents/store/$name/componentList.json", $json_string);

            $obj = new stdClass;
            $obj->status = "success";
            echo json_encode($obj);

        } catch(Exception $e) {
            $obj = new stdClass;
            $obj->status = "error";
            $obj->message = $e->getMessage;
            echo json_encode($obj);
        }
    }
}