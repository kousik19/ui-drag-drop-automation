<?php 

class CategoryListProviderService
{
    public function execute($params) {

        $content = file_get_contents(__DIR__ . "/../../customUIComponents/category.json");
        echo $content;
    }
}