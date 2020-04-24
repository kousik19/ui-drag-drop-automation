<?php 

class CategoryWiseComponentListProviderService {

    public function execute($params) {

        try{
            $categoryName = $params["category"]; //category name is the folder name
            $componentListJsonPath = __DIR__ . "/../../$categoryName/componentList.json";

            if(file_exists($componentListJsonPath))
            {
                $obj = new stdClass;
                $obj->status = "error";
                $obj->message = "Unknown Category";
                echo json_encode($obj);
                die;
            }

            $componentJson = file_get_content();
            echo $componentJson;
        } catch(Exception $e) {
            $obj = new stdClass;
            $obj->status = "error";
            $obj->message = $e->getMessage();
            echo json_encode($obj);
        }
    }
}