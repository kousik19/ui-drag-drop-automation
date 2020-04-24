<?php 
require_once(__DIR__ . "/Button.php");

class DocxDownloadButton extends Button 
{
    public function getScript($idStartsWith)
    {
        return [
            "download" => "docx"
        ];
    }
}