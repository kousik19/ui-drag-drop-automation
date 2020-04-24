<?php 
require_once(__DIR__ . "/../IUiBuilderService.php");

class ComponentRetrieveAllService implements IUiBuilderService
{
    public function execute($params)
    {
        try{

            $str = file_get_contents(__DIR__ . '/../../customUIComponents/category.json');
            $categories = json_decode($str);

            $res = array();

            for($i = 0; $i < count($categories); $i++)
            {
                if($categories[$i]->enable)
                {
                    $category = $categories[$i]->name;
                    $componentsStr = file_get_contents(__DIR__ . "/../../customUIComponents/store/$category/componentList.json");
                    $components = json_decode($componentsStr);
                    $filteredComponents = [];

                    for($j = 0; $j < count($components); $j++)
                    {
                        if($components[$j]->enable == "true")
                            array_push($filteredComponents, $components[$j]);
                    }

                    
                    for($j = 0; $j < count($filteredComponents); $j++)
                    {
                        //This is for so that one component can be dropped inside another
                        $filteredComponents[$j]->contentFile = $filteredComponents[$j]->content;
                        $filteredComponents[$j]->content = "<div data-gjs-droppable=\"*\">" . $filteredComponents[$j]->content . "</div>";
                        
                        array_push($res, $filteredComponents[$j]);
                    }
                }
            }

            return json_encode($res);

        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
}