<?php 
require_once(__DIR__ . "/Button.php");

class PdfDownloadButton extends Button 
{
    public function getScript($idStartsWith)
    {
        return [
            "download" => "pdf"
        ];
    }
}