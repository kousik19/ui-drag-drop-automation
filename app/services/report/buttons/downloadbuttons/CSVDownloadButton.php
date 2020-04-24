<?php 
require_once(__DIR__ . "/DownloadButton.php");

class CSVDownloadButton extends DownloadButton 
{
    public function getSpecificParams()
    {
        return [
            "download" => "csv"
        ];
    }
}