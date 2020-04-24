<?php 
require_once(__DIR__ . "/../IUiBuilderService.php");

class ReportListProviderService implements IUiBuilderService 
{
    public function execute($params) {
        //For KMR Server
        //echo file_get_contents(__DIR__ . "/../../../reports/reportlist.json");

        //For Local
        echo file_get_contents("C:\\wamp64\\www\\sammsa\\reports/reportlist.json");
    }
}