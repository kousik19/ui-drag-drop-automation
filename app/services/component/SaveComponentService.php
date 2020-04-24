<?php 

require_once(__DIR__ . "/../IUiBuilderService.php");

class SaveComponentService implements IUiBuilderService
{
    public function execute($params)
    {
        try{

            $name = $params["templateName"];
            $category = $params["templateCtegory"];
            $content = $params["templateContent"];

            $categoryFileName = __DIR__ . "/../../customUIComponents/category.json";
            $categoryContent = file_get_contents($categoryFileName);
            $categoryList = json_decode($categoryContent);
            $categoryFolder = "";

            for($i = 0; $i < count($categoryList); $i++)
            {
                if($categoryList[$i] -> display == $category)
                    $categoryFolder = $categoryList[$i] -> name;
            }

            $blockListingFile = __DIR__ . "/../../customUIComponents/store/" . $categoryFolder . "/componentList.json";
            $blockListingFolder = __DIR__ . "/../../customUIComponents/store/" . $categoryFolder;

            $jsonStr = file_get_contents($blockListingFile);
            $json = json_decode($jsonStr);

            $id = uniqid("custom-", true);

            $obj = new stdClass;
            $obj->id = $id;
            $obj->label = "<div> $name </div>";
            $obj->attributes = "{ class:'gjs-block-section' }";
            $obj->content = "stock_" . $id ;
            $obj->category = $category;
            $obj->enable = "true";

            array_push($json, $obj);

            $json_string = json_encode($json, JSON_PRETTY_PRINT);
            file_put_contents($blockListingFile, $json_string);

            $file = fopen($blockListingFolder . "/" . "stock_" . $id ,"w");
            fwrite($file, $content);
            fclose($file);

            $obj = new stdClass;
            $obj->status = "success";
            echo json_encode($obj);

        }catch(Exception $e){
            $obj = new stdClass;
            $obj->status = "error";
            $obj->message = $e->getMessage();
            echo json_encode($obj);
        }
    }
}