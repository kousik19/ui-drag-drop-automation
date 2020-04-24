<?php 

    $params = $_POST;
    $sqlInputmap = $params["sqlInputMap"];
    
    $templateSql = file_get_contents("template.sql");
	$templateSql = str_replace("{", "[", $templateSql);
	$templateSql = str_replace("}", "]", $templateSql);

    foreach ($params as $key => $value) {

        $rep = "";
        if(!is_string($value))continue;
        foreach ($sqlInputmap as $skey => $svalue) {
            if($svalue == $key){$rep = $skey; break;}
        }

        $templateSql = str_replace("$[".$skey."]", $value, $templateSql);
    }

    $conn = new mysqli("localhost", "root", "", $params["dbName"]);
    $result = $conn->query($templateSql);
    $rows = [];

    

    //grid lib
    /*if($params['gridLib'] == 'JqGrid')
    {
        $ret = new stdClass;
        if ($result->num_rows > 0) { 
            while($row = $result->fetch_object()) {
                array_push($rows, $row);
            }
        }
    }*/

    if(isset($params['download']))
    {
        if($params['download'] == 'csv')
        {
            $count = 0;
            $filename = "report_" . time() . ".csv";
            $file = fopen("C:\\wamp64\\www\\sammsa\\dumpfiles\\$filename","w");
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
            }

            fclose($file);
            echo $filename;
        }
    }
    else
    {
        if ($result->num_rows > 0) { 
            while($row = $result->fetch_object()) {
                array_push($rows, $row);
            }
        }
        echo json_encode($rows);
    }

?>