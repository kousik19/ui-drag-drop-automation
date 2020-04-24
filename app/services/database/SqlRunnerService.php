<?php 

require_once(__DIR__ . "/../IUiBuilderService.php");
require_once(__DIR__ . "/DbBaseService.php");

class SqlRunnerService extends DbBaseService implements IUiBuilderService 
{
    private $sql;
    private $dbType;
    private $conn;

    public function execute($params)
    {
        session_start();
        $this->sql = $params['sql'];

        if(!isset($_SESSION['dbtype'])){
            $obj = new stdClass;
            $obj->status = "error";
            $obj->message = "Database profile is not set";
            echo json_encode($obj);
            die;
        }

        if(!isset($_SESSION['dbtype'])) {
            $obj = new stdClass;
            $obj->status = "error";
            $obj->message = "Database profile is not set";
            echo json_encode($obj);
            die;
        }
        $this->dbname = $_SESSION['dbname'];

        $this->dbType = $_SESSION['dbtype'];
        $this->conn = $this->getDbConn($this->dbType, $this->dbname);

        if($this->dbType == "mysql") return json_encode($this->getMySqlRsultSet());
        else if($this->dbType == "db2") return json_encode($this->getDb2ResultSet());
    }

    private function getMySqlRsultSet() {
        try{

            $this->sql .= " LIMIT 100";
            $result = $this->conn->query($this->sql);

            if(!$result) {
                $obj = new stdClass;
                $obj->status = "error";
                $obj->message = $this->conn->error;
                echo json_encode($obj);
                die;
            }
            $rows = [];

            if ($result->num_rows > 0) {

                while($row = $result->fetch_assoc()) {
                    array_push($rows, $row);
                } 
            }

            return $rows;

        }catch(Exception $e) {
            return null;
        }
    }

    private function getDb2ResultSet() {

        try{

            $this->sql .= " FETCH FIRST 100 ROWS ONLY";

            if (!($stmt = db2_exec($this->conn, $this->sql)))
            {
                $obj = new stdClass;
                $obj->status = "error";
                $obj->message = "<b>Error ".db2_stmt_error() .":".db2_stmt_errormsg(). "</b>";
                echo json_encode($obj);
                die;
            }

            $rows=Array();
            while($row = db2_fetch_assoc($stmt))
            {
                array_push($rows,$row);
            }

            return $rows;

        }catch(Exception $e) {
            return null;
        }
    }
}