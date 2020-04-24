<?php 
require_once(__DIR__ . "/../IUiBuilderService.php");

class ReportMetadataProviderService implements IUiBuilderService {

    //private $kmr_path = "/www/zendsvr6/htdocs/developer/dev/dynamicReports/reports/";
    private $path = "C:\\wamp64\\www\\sammsa\\reports\\";

    public function execute($params) {

        $reportName = $params["reportName"];
        $metadataPath = $this->path . $reportName . "/.metadata";

        if(!file_exists($metadataPath)) {
            $obj = new stdClass;
            $obj->status = "error";
            $obj->msg = "Report name is not correct";
            echo json_encode($obj);
            die;
        }

        $filelist = scandir($metadataPath);
        $obj = new stdClass;

        for($i = 0; $i < count($filelist); $i++) {
            if($filelist[$i] == "." || $filelist[$i] == "..") continue;
            $content = file_get_contents($metadataPath . "/" . $filelist[$i]);
            $obj->{$filelist[$i]} = $content;
        }

        echo json_encode($obj);
    }
}