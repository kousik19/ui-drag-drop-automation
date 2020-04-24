<?php 
require_once(__DIR__ . "/DownloadButton.php");

class ExcelDownloadButton extends DownloadButton 
{
    public function getSpecificParams()
    {
        return [
            "download" => "excel"
        ];
    }
}