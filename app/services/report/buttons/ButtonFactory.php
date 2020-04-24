<?php 

class ButtonFactory
{
    private $commonButtonMap = [
        "trigger" => "ViewReportButton",
        "reset" => "ResetButton",
        "print" => "PrintButton"
    ];

    private $downloadButtonMap = [

        "csv" => "CSVDownloadButton",
        "excel" => "ExcelDownloadButton",
        "pdf" => "PdfDownloadButton"
    ];

    public function getButton($btn) 
    {

        if(isset($this->commonButtonMap[$btn])) {
            
            require_once(__DIR__ . "/" . $this->commonButtonMap[$btn]. ".php");
            $className = $this->commonButtonMap[$btn];
        }
        else {

            require_once(__DIR__ . "/downloadbuttons/" . $this->downloadButtonMap[$btn]. ".php");
            $className = $this->downloadButtonMap[$btn];
        }

        $btn = new $className();
        return $btn;
    }
}