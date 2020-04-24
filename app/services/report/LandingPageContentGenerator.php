<?php 

class LandingPageContentGenerator {

    private $tempFileLocation = "C:\\wamp64\\www\\sammsa\\dumpfiles";
    //private $tempFileLocation = "/www/zendsvr6/htdocs/developer/dev/dynamicReports/temp";
    private $dbType;

    public function __construct($dbType) {

        $this->dbType = $dbType;
    }

    public function generateContent($sqlInputMap, $reportName) {

        //$json = 
        $content = '<?php 

            $params = $_REQUEST;
            $sqlInputmap = json_decode(\''. json_encode($sqlInputMap) .'\');
            
            $templateSql = file_get_contents("template.sql");
            $templateSql = str_replace("{", "[", $templateSql);
            $templateSql = str_replace("}", "]", $templateSql);
            $filterMap = new stdClass;
            foreach ($params as $key => $value) {

                $rep = "";
                if(is_array($value)) {
                    $temp = "\'";
                    for($i = 0; $i < count($value); $i++)
                    {
                        if($i != 0) $temp .= ", \'";
                        $temp .= $value[$i] . "\'";
                    }
                    $value = $temp;
                }
                
                if(!is_string($value))continue;

                foreach ($sqlInputmap as $skey => $svalue) {
                    if($svalue == $key){$rep = $skey; break;}
                }
                $value = trim($value, \'\\\'\');
                $value = trim($value, \'"\');
                $templateSql = str_replace("$[".$skey."]", $value, $templateSql);
                /*$templateSql = str_replace("\'\'", "\'", $templateSql);
                $templateSql = str_replace("\"\"", "\"", $templateSql);
                $templateSql = str_replace("\'\"", "\'", $templateSql);
                $templateSql = str_replace("\"\'", "\'", $templateSql);*/

                if($key != "download")
                    $filterMap->{$key} = $value;
            }';

            $content .= $this->generateDataFetchingCodeSnippet();

            $content .= '
            if(isset($params["download"]))
            {
                if($params["download"] == "excel")
                {
                    require_once(__DIR__ . "/../excel/ExcelReportBase.php");
                    $generator = new ExcelReportBase();
                    $rows = [];';
                    
            
            //MySQL
            if($this->dbType == 'mysql') {

                $content .= 'if ($result->num_rows > 0) { 
                        while($row = $result->fetch_object()) {

                            array_push($rows, $row);
                        }
                    }';
            }
            //DB2
            else if($this->dbType == 'db2') {

                $content .= '
                    while($row = db2_fetch_object($stmt)) {
                        
                        array_push($rows, $row);
                    }';
            }

            $content .= '
                $xmlStr = "<workbook>";
                $xmlStr .= $generator->generateSheetHeader("'. $reportName .'", date("m/d/Y"), $filterMap);
                $xmlStr .= $generator->generateSheetBody($rows);
                $xmlStr .= $generator->generateSheetFooter();
                $xmlStr .= "</workbook>";

                echo $generator->generateReport($xmlStr);
            ';

            $content .= '
                }
            
                if($params["download"] == "csv")
                {
                    $count = 0;
                    $filename = "report_" . time() . ".csv";
                    $file = fopen("'. $this->tempFileLocation . '/".$filename' .',"w");
                    ';
            
            //MySQL
            if($this->dbType == 'mysql') {

                $content .= '
                fwrite($file,"Report Count : , " . $result->num_rows . "\n");
                if ($result->num_rows > 0) { 
                        while($row = $result->fetch_object()) {

                            $rowline = "";
                            $headerline = "";
                            foreach ($row as $key => $value) 
                            {
                                if($count == 0)
                                    $headerline .= $key . ",";

                                $rowline .= $value . ",";
                            }
                            
                            if($count == 0) fwrite($file, $headerline . "\n");
                            fwrite($file, $rowline . "\n");
                            $count++;
                        }
                    }';
            }
            //DB2
            else if($this->dbType == 'db2') {

                $content .= '
                    //fwrite($file,"Report Count : , " . $result->num_rows . "\n");
                    while($row = db2_fetch_object($stmt)) {
                        
                        $rowline = "";
                        $headerline = "";
                        foreach ($row as $key => $value) 
                        {
                            if($count == 0)
                                $headerline .= $key . ",";

                            $rowline .= $value . ",";
                        }
                            
                        if($count == 0) fwrite($file, $headerline . "\n");
                        fwrite($file, $rowline . "\n");
                        $count++;
                    }';
            }

            $content .= '
                    fclose($file);
                    echo $filename;
                }
            }
            else
            {';

            if($this->dbType == 'mysql') {
                
                $content .= 'if ($result->num_rows > 0) { 
                    while($row = $result->fetch_object()) {
                        array_push($rows, $row);
                    }
                }';
            }
            else if($this->dbType == 'db2') {
                
                $content .= '
                    while($row = db2_fetch_object($stmt)) {
                        array_push($rows, $row);
                    }';
            }
            
            $content .= '
                echo json_encode($rows);
            }

        ?>';
        return $content;
    }

    private function generateDataFetchingCodeSnippet() {

        if($this->dbType == 'mysql') {
         return 
         ' require_once("./dbConfig.php");
         $result = $conn->query($templateSql);
         $rows = [];';
        }
        else if($this->dbType == 'db2') {
            return 
            ' require_once("./dbConfig.php");
            $stmt = db2_exec($conn, $templateSql);
            $rows = [];';
        }
    }
}