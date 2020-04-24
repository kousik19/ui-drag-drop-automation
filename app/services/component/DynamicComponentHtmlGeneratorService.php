<?php 
error_reporting(-1);
ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL);
class DynamicComponentHtmlGeneratorService {

    /**
     * For dynamic element I am going to generate content and saving it to dynamic folder.
     * But during drag and drop it will appear as non editable text box and actual elem will appear in final report
     * 
     */
    public function execute($params) {

        session_start();
        $sql = trim($params['sql']);
        $elem = trim($params['basicelem']);
        $dynlbl = trim($params['dynlbl']);
        $dynval = trim($params['dynval']);

        if(isset($params['html']))
            $html = $params['html'];

        $content = "";

        if((isset($_SESSION['dbtype']) && $_SESSION['dbtype'] == 'mysql') && !isset($html)) {
            if($elem == "select") {

                $content .= '<select data-elemtype="input">
                <?php
                        $res = $conn->query("' . $sql . '");
                        if ($res->num_rows > 0) { 
                            while($row = $res->fetch_assoc()) {
                                echo "<option value=\'". $row["'. $dynval .'"]. "\'>" . $row["'. $dynlbl .'"] . "</option>";
                            }
                        }
                    ?>
                    </select>';

            }
            else if($elem == "multiselect") {

                $content .= '<select multiple  data-elemtype="input">
                <?php
                        $res = $conn->query("' . $sql . '");
                        if ($res->num_rows > 0) { 
                            while($row = $res->fetch_assoc()) {
                                echo "<option value=\'". $row["'. $dynval .'"]. "\'>" . $row["'. $dynlbl .'"] . "</option>";
                            }
                        }
                    ?>
                    </select>';
            }
            else if($elem == "radio") {

                $uniq = uniqid();
                $content .= '<?php
                        $res = $conn->query("' . $sql . '");
                        if ($res->num_rows > 0) { 
                            while($row = $res->fetch_assoc()) {
                                echo "<input data-elemtype=\"input\" type=\"radio\" value=\'". $row["'. $dynval .'"]. "\' name=\"'. $uniq .'\"> &nbsp; " . $row["'. $dynlbl .'"] . " &nbsp; &nbsp; ";
                            }
                        }
                    ?>';
            }
            else if($elem == "checkbox") {

                $content .= '<?php
                        $res = $conn->query("' . $sql . '");
                        if ($res->num_rows > 0) { 
                            while($row = $res->fetch_assoc()) {
                                echo "<input data-elemtype=\"input\" type=\"checkbox\" value=\'". $row["'. $dynval .'"]. "\' name=\"'. $uniq .'\"> &nbsp; " . $row["'. $dynlbl .'"] . " &nbsp; &nbsp; ";
                            }
                        }
                    ?>';
            }
        }
        ////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////DB2//////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////
        else if((isset($_SESSION['dbtype']) && $_SESSION['dbtype'] == 'db2') && !isset($html)) {

            if($elem == "select") {

                $content .= '<select data-elemtype="input">
                <?php
                        $stmt = db2_exec($conn, "'.$sql .'");
                        
                        while($row = db2_fetch_assoc($stmt)) {
                                echo "<option value=\'". $row["'. $dynval .'"]. "\'>" . $row["'. $dynlbl .'"] . "</option>";
                            }
                        
                    ?>
                    </select>';

            }
            else if($elem == "multiselect") {

                $content .= '<select multiple  data-elemtype="input">
                <?php
                        $stmt = db2_exec($conn, "'.$sql .'");
                        
                        while($row = db2_fetch_assoc($stmt)) {
                                echo "<option value=\'". $row["'. $dynval .'"]. "\'>" . $row["'. $dynlbl .'"] . "</option>";
                            }
                        
                    ?>
                    </select>';
            }
            else if($elem == "radio") {

                $uniq = uniqid();
                $content .= '<?php
                        $stmt = db2_exec($conn, "'.$sql .'");
                        
                            while($row = db2_fetch_assoc($stmt)) {
                                echo "<input data-elemtype=\"input\" type=\"radio\" value=\'". $row["'. $dynval .'"]. "\' name=\"'. $uniq .'\"> &nbsp; " . $row["'. $dynlbl .'"] . " &nbsp; &nbsp; ";
                            }
                        
                    ?>';
            }
            else if($elem == "checkbox") {

                $content .= '<?php
                        $stmt = db2_exec($conn, "'.$sql .'");
                        
                        while($row = db2_fetch_assoc($stmt)) {
                                echo "<input data-elemtype=\"input\" type=\"checkbox\" value=\'". $row["'. $dynval .'"]. "\' name=\"'. $uniq .'\"> &nbsp; " . $row["'. $dynlbl .'"] . " &nbsp; &nbsp; ";
                            }
                        
                    ?>';
            }
        }
        else 
            $content = $html;

        //generate a unique ID
        $id = uniqid();

        //generate file name
        $fileName = "dyn_$id";

        //read dynamicElementMap.json file
        $fileLocation = __DIR__ . "/../../customUIComponents/store/dynamic/$fileName";
        $file = fopen($fileLocation, "w");
        fwrite($file, $content);
        fclose($file);


        $obj = new stdClass;
        $obj->id = $id;
        echo json_encode($obj);
        
    }
}