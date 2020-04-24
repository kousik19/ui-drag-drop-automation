<?php
require_once(__DIR__ . "/../IUiBuilderService.php");

class ComponentContentProviderService implements IUiBuilderService
{
    public function execute($params)
    {
        $contentFile = $params['contentFile'];
        $componentType = $params['componentType'];

        //load category list file
        $categoryFileName = __DIR__ . "/../../customUIComponents/category.json";
        $categoryContent = file_get_contents($categoryFileName);
        $categoryList = json_decode($categoryContent);
        $categoryFolder = "";

        for($i = 0; $i < count($categoryList); $i++)
        {
            if($categoryList[$i] -> display == $componentType)
                $categoryFolder = $categoryList[$i] -> name;
        }

        $fileName = __DIR__ . "/../../customUIComponents/store/$categoryFolder/$contentFile";
        $content = file_get_contents($fileName);

        $res = new stdClass;
        $res->content = $content;

        return json_encode($res);
    }
}

?>