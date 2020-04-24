<?php 
/*error_reporting(-1);
ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL);*/
error_reporting(E_ERROR | E_PARSE);
ob_end_clean();       //<====================== NOTICE IT!!

require_once(__DIR__ . "/../IUiBuilderService.php");
require_once(__DIR__ . "/buttons/ButtonFactory.php");
require_once(__DIR__ . "/grids/GridFactory.php");
require_once(__DIR__ . "/../../lib/simple_html_dom.php");

class SaveReportService implements IUiBuilderService 
{
    //This needs to bring from configuration 
    private $path = "C:\\wamp64\\www\\sammsa\\reports\\";
    //private $path = "/www/zendsvr6/htdocs/developer/dev/dynamicReports/reports/";
    private $html;
    private $sql;
    private $reportName;
    private $buttonMap;
    private $sqlInputMap;
    private $dbName;
    private $dbType;
    private $gridConfig;
    private $gridLib;
    private $settings;
    private $reportlocation = "http://localhost/sammsa/dumpfiles";

    public function execute($params) {
        
        session_start();
        $this->html = $params["html"];
        $this->sql = $params["sql"];
        $this->reportName = $params["reportName"];
        $this->buttonMap = $params["buttonMap"];
        $this->sqlInputMap = $params["sqlInputMap"];
        $this->dbName = $_SESSION["dbname"];
        $this->dbType = $_SESSION["dbtype"];
        $this->gridConfig = $params["gridConfig"];
        $this->gridLib = $this->gridConfig['gridlib'];
        $this->settings = $params['settings'];
        $this->editMode = $params['editMode'];

        //check if same name report already exists
        if(file_exists($this->path . $this->reportName) && !$this->editMode) 
            $this->generateErroResponse("Another report with same name already exists");
        

        //save html
        $html = $this->generateHtml();

        //Format the HTML
        /*$dom = new DOMDocument();
        $dom->preserveWhiteSpace = FALSE;
        $dom->loadHTML($html);
        $dom->formatOutput = TRUE;
        $html = $dom->saveHTML();*/

        //generate portal.php
        $landingPageContent = $this->getLandingPageContent();

        //generate portal.php
        $dbConfigPageContent = $this->getDbConfigPageContent();
        
        //create report folder
        if($this->editMode == "true") {
            rename($this->path . $this->reportName, $this->path . $this->reportName . "_" . rand());
        }
        mkdir($this->path . $this->reportName);

        //Save the HTML
        $file = fopen($this->path . $this->reportName."/index.php","w");
        fwrite($file, $html);
        fclose($file);

        //save dbConfig page
        $file = fopen($this->path . $this->reportName."/dbConfig.php","w");
        fwrite($file, $dbConfigPageContent);
        fclose($file);

        //save portal.php
        $file = fopen($this->path . $this->reportName."/portal.php","w");
        fwrite($file, $landingPageContent);
        fclose($file);

        //create a file and write the sql
        $file = fopen($this->path . $this->reportName . "/template.sql","w");
        fwrite($file, $this->sql);
        fclose($file);

        //save report metadata for later editing purpose
        $this->saveMetadata();

        if($this->editMode != "true") {
            //keep a track of newly created report
            $str = file_get_contents($this->path. "/reportlist.json");
            $json = json_decode($str, true);
            $obj = new stdClass;
            $obj->name = $this->reportName;
            $obj->deleted = "no";
            array_push($json, $obj);
            file_put_contents($this->path. "/reportlist.json", json_encode($json));
        }

        
        $obj = new stdClass;
        $obj->status = "success";
        echo json_encode($obj);
    }

    private function generateHtml() {

        //common HTML starting lines
        $html = "<html>
                    <head>
                        <title> $this->reportName </title> 
                        <meta charset=\"UTF-8\">
                    </head>
                    <body>

                    <?php require_once('./dbConfig.php'); ?>

                    ";
        
        //Add style links
        $html .= $this->getStyleLinks();

        //Add script links
        $html .= $this->getScriptLinks();

        //Parse to check if dynamic element is present  and Add generated html (which includes generated CSS)
        $html .= $this->parseHTML($this->html);

        $html .= $this->getHtmlForWaitIconSection();

        //Grid Container
        $html .= $this->generateGridContainerHtml();

        //Custom JS
        $html .= $this->generateCustomJS();

        //End HTML Part
        $html .= "  </body>
                </html>";

        return $html;
    }

    private function parseHTML($htmlstr) {

        $html = str_get_html($htmlstr);
        $map = [];
        foreach($html->find('input') as $element) {

            //check for dynamic element
            if(isset($element->{'dyn-id'})) {
                $dynId = $element->{'dyn-id'};
                $name = $element->{'value'};
                $id = $element->{'id'};
                $dynamicContent = $this->getDynamicContent($dynId);

                $obj = new stdClass;
                $obj->content = $dynamicContent;
                $obj->name = $name;
                $obj->id = $id;
                
                $map[$dynId] = $obj;
            }
        }

        $dom = new DOMDocument();
        $dom->loadHTML($htmlstr);

        foreach ($map as $key => $value) {

            $pos = strpos($htmlstr, $value->id);
            $start = 0;
            $end = 0;

            for ($i = $pos; $i >= 0; $i--){
                if($htmlstr[$i] == '<') {
                    $start = $i;
                    break;
                }
            }

            for ($i = $pos; $i < strlen($htmlstr); $i++){
                if($htmlstr[$i] == '>') {
                    $end = $i;
                    break;
                }
            }

            //set id of dynamic element [NOT A VERY GOOD APPROCH]
            $dynPrimaryElemList = ["<select", "<input"];
            $phpContent = $value->content;
            $startLoc = 0;
            $foundElemIndex = -1;
            $updatedContent = "";
            $startCheck = false;

            for($i = 0; $i < count($dynPrimaryElemList); $i++){
                if(strpos($phpContent, $dynPrimaryElemList[$i]) > -1) {
                    $foundElemIndex = $i;
                    $el = $dynPrimaryElemList[$i];

                    if($el == '<input')
                        preg_match_all('/<\s*input[^>]/', $value->content, $positions, PREG_OFFSET_CAPTURE);
                    if($el == '<select')
                        preg_match_all('/<\s*select[^>]/', $value->content, $positions, PREG_OFFSET_CAPTURE);
                    break;
                }
            }

            $locations = [];
            for($i = 0; $i < count($positions[0]); $i++) {
                array_push($locations, $positions[0][$i][1]);
            }
            //var_dump($locations);
            
            for ($i = 0; $i < strlen($phpContent); $i++){
                
                if($startCheck && $phpContent[$i] == ' ') {
                    $updatedContent .= " id='" . $value->id . "'";
                    $startCheck = false;
                }

                $updatedContent .= $phpContent[$i];
                if(in_array($i, $locations)) {
                    $startCheck = true;
                }
            }

            $elemHtml = substr($htmlstr, $start, $end - $start + 1);
            $htmlstr = str_replace($elemHtml, $updatedContent, $htmlstr);

            //check if multiselect
            if(strpos($htmlstr, " multiple")) {
                $htmlstr .= "<script>
                    (function(){
                        multiSelectWithSearch('". $value->id ."');
                    })()
                </script>";
            }
        }

        return $htmlstr;
    }

    private function getDynamicContent($dynamicId) {
        $dynamicFilePath = __DIR__ . "/../../customUIComponents/store/dynamic/dyn_" . $dynamicId;
        if(file_exists($dynamicFilePath))
            return file_get_contents($dynamicFilePath);
        else {
            $msg = "Unknown dynamic element. Report can not be saved";
            $this->generateErroResponse($msg);
        }
    }
 
    private function getStyleLinks() {

        $html = "";
        //Fund System Specific CSS
        $cssList = json_decode(file_get_contents(__DIR__ . "/cssfilelist.json"));
        
        for($i = 0; $i < count($cssList); $i++) {
            $html .= "<link rel='stylesheet' href='". $cssList[$i] ."'>
            ";
        }

        //some infile CSS
        $html .= "
        
            <style type=\"text/css\">
                .ui-datepicker {z-index:1200;}
                .hints {
                    color:#CC3300;
                }
                
                /* Added class for calender image alignment*/
                img.ui-datepicker-trigger{margin-left:5px; vertical-align:middle;}
                input.hasDatepicker{vertical-align:middle;}
                
                .topbar {cursor:pointer;}
                .topbar span { float:right; width:20px;}
                .heading {width:90%; float:left;}
            </style>
            
            ";
        //JQuery UI
        /*$html = "<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/themes/redmond/jquery-ui.min.css'>\n";

        //font awesome
        $html .= "<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' /> \n";

        //JqGrid
        $html .= "<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/free-jqgrid/4.15.5/css/ui.jqgrid.min.css'>\n";

        //AG-Grid
        $html .= "<link rel='stylesheet' href='https://unpkg.com/ag-grid-community/dist/styles/ag-grid.css'>
        <link rel='stylesheet' href='https://unpkg.com/ag-grid-community/dist/styles/ag-theme-balham.css'>\n";

        //Handsontable
        $html .= "<link href='https://cdn.jsdelivr.net/npm/handsontable@7.1.1/dist/handsontable.full.min.css' rel='stylesheet' media='screen'>";*/

        //Custom Style
        if($this->settings['useCentralizedTheme'] == "true")
            $html .= "<link rel='stylesheet' href='../theme.css'> \n";

        return $html;
    }

    private function getScriptLinks() {

        //$html = "<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js'></script> \n";

        //Jq Grid
        //$html .= "<script src='https://cdnjs.cloudflare.com/ajax/libs/free-jqgrid/4.15.5/jquery.jqgrid.min.js'></script> \n";

        //load js list
        $jsList = json_decode(file_get_contents(__DIR__ . "/jsfilelist.json"));
        $html = "";
        for($i = 0; $i < count($jsList); $i++) {
            $html .= "<script src ='". $jsList[$i] ."'></script>
            ";
        }

        //FOR NOW IGNORING AG_GRID AND HANDSONTABLE
        //Ag Grid
        //$html .= "<script src='https://unpkg.com/ag-grid-community/dist/ag-grid-community.min.noStyle.js'></script> \n";

        //Handsontable
        //$html .= "<script src='https://cdn.jsdelivr.net/npm/handsontable@7.1.1/dist/handsontable.full.min.js'></script> \n";

        return $html;
    }

    private function generateGridContainerHtml() {

        $html = "";

        if($this->gridLib == 'JqGrid') {
            $html .= "
            <style>.ui-jqgrid .ui-jqgrid-bdiv { max-height: 450px; overflow-y: scroll }</style>
            <div style='margin-top:20px; height: 500px; margin-left: 20px; width: 98%;' id='gridSection'>
                <table style='width:100%' id='grid'></table>
                <div id='tblGridPager' style='text-align:center; height: 30px' > </div>
            </div>";
        }

        else if($this->gridLib == 'AgGrid') {
            $html .= "<div id='aggrid' style='width:100%; margin-top: 20px' class='ag-theme-balham'></div>";
        }

        else if($this->gridLib  == 'Handsontable') {
            $html .= "
            <div style='margin-top:20px; height: 500px; overflow: hidden;
            margin-left: 20px; width: 98%;'>
                <div id='handsontablegrid' style='width:100%; margin-top: 20px' class='ag-theme-balham'>
                </div>
            </div>";
        }

        return $html;
    }

    private function generateCustomJS() {

        $jsonPaylod = json_encode($this->sqlInputMap);
        $html = "
            <script>
            var firstTimeGridLoad = true;

            //configure datepicker
            var datepickers = $(\"[type='date']\");
            for(var i=0; i < datepickers.length; i++) {
                $(datepickers[i]).datepicker({showOn: 'button', buttonImage: '../css/images/clnd.gif', buttonImageOnly: true, changeMonth: true, changeYear: true});
            }

            function adv_date_check(dt) {
         
                var val = dt.value;  
                var ret_date = isDate(val);
              
                if (ret_date) 
                {
                    if(ret_date != true)
                    dt.value = ret_date;
                }
                else 
                {
                    inlineMsg(dt.id, \"Please enter a valid date\",2);
                    dt.value = \"\";
                    return false;
                 }
             }
        ";

        //Script for buttons
        foreach($this->buttonMap as $key => $value) {
            $buttonType = $key;
            $buttonIdStartsWith = $value;
            $bf = new ButtonFactory();
            $button = $bf->getButton($buttonType);
            $buttonScript = $button->getScript($buttonIdStartsWith, $jsonPaylod);
            $html .= "\n\n " . $buttonScript . "\n\n";
        }

        //Script for grids
        $this->gridConfig['caption'] = $this->reportName;
        $gridFactory = new GridFactory();
        $grid = $gridFactory->getGrid($this->gridLib);
        $gridHtml = $grid->getGridScript($this->gridConfig);
        $html .= $gridHtml;

        $html .= $this->getOtherFunctionalitiesScript();

        $html .= "</script>";

        return $html;
    }

    private function generateErroResponse($msg) {
        $obj = new stdClass;
        $obj->status = "error";
        $obj->message = $msg;
        echo json_encode($obj);
        die;
    }

    private function getLandingpageContent() {

        require_once("LandingPageContentGenerator.php");
        $generator = new LandingPageContentGenerator($this->dbType);
        $content = $generator->generateContent($this->sqlInputMap, $this->reportName);
        return $content;
    }

    private function getOtherFunctionalitiesScript() {

        $script = "
        var getAJAXRequests = (function() {
            var oldSend = XMLHttpRequest.prototype.send,
                currentRequests = [];
        
            XMLHttpRequest.prototype.send = function() {
                currentRequests.push(this); // add this request to the stack
                oldSend.apply(this, arguments); // run the original function
        
                // add an event listener to remove the object from the array
                // when the request is complete
                this.addEventListener('readystatechange', function() {
                    var idx;
        
                    if (this.readyState === XMLHttpRequest.DONE) {
                        idx = currentRequests.indexOf(this);
                        if (idx > -1) {
                            currentRequests.splice(idx, 1);
                        }
                    }
                }, false);
            };
        
            return function() {
                return currentRequests;
            }
        }());
        
        
        setInterval(function(){

            if(getAJAXRequests().length > 0) {

                $('#waitingoverlay').show();
            }
            else
                $('#waitingoverlay').hide();
        }, 1000)"; 

        return $script;
    }

    private function getDbConfigPageContent() {

        if($_SESSION['dbtype'] == 'mysql') {
            $content = '<?php

                $conn = new mysqli("'. $_SESSION['dbhost'] .'", "'. $_SESSION['dbusername'].'", "'. $_SESSION['dbpassword'] .'", "'. $_SESSION['dbname'] .'")
            ?>';
        }
        else if($_SESSION['dbtype'] == 'db2'){
            $content = '<?php 
                $conn = db2_connect ("", "' . $_SESSION['dbusername'] . '", "' . $_SESSION['dbpassword'] . '");
                db2_exec($conn, "SET CURRENT SCHEMA = '. $_SESSION['dbname']  .'");

                ?>';
        }

        return $content;
    }

    private function getHtmlForWaitIconSection() {

        return "
            <div style='display:none;' id='waitingoverlay'>
                <div style='top: 0; left:0' id='overlay'></div>
                    <img src='https://thumbs.gfycat.com/MarriedMarvelousHawaiianmonkseal-small.gif'  style='
                        margin-left:calc(50% - 40px); margin-top: 8px; width: 60px;'/>
                    <center> <h4>Loading</h4> </center>
                        
            </div>";
    }

    private function saveMetadata() {

        $metadataPath = $this->path . $this->reportName . "/.metadata";
        mkdir($metadataPath);
        $htmlTemplateFile = fopen($metadataPath . "/htmlTemplate", "w");
        fwrite($htmlTemplateFile, $this->html);
        fclose($htmlTemplateFile);

        $sqlTemplateFile = fopen($metadataPath . "/sqlTemplate", "w");
        fwrite($sqlTemplateFile, $this->sql);
        fclose($sqlTemplateFile);

        $buttonMapFile = fopen($metadataPath . "/buttonMap", "w");
        fwrite($buttonMapFile, json_encode($this->buttonMap));
        fclose($buttonMapFile);

        $sqlInputMapFile = fopen($metadataPath . "/sqlInputMapFile", "w");
        fwrite($sqlInputMapFile, json_encode($this->sqlInputMap));
        fclose($sqlInputMapFile);

        $gridConfigFile = fopen($metadataPath . "/gridConfigFile", "w");
        fwrite($gridConfigFile, json_encode($this->gridConfig));
        fclose($gridConfigFile);

        $gridLibFile = fopen($metadataPath . "/gridLibFile", "w");
        fwrite($gridLibFile, $this->gridLib);
        fclose($gridLibFile);

        $settingsFile = fopen($metadataPath . "/settingsFile", "w");
        fwrite($settingsFile, json_encode($this->settings));
        fclose($settingsFile);
    }

    private function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
}