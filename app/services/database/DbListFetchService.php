<?php 

require_once(__DIR__ . "/../IUiBuilderService.php");

class DbListFetchService implements IUiBuilderService 
{
    private $dbType;
    private $host;
    private $username;
    private $password;

    public function execute($params)
    {
        $this->dbType = $params['dbType'];
        $this->host = $params['host'];
        $this->username = $params['username'];
        $this->password = $params['password'];

        if($this->dbType == "mysql") return json_encode($this->getMySqlDbList());
        else if($this->dbType == "db2") return json_encode($this->getDb2List());
    }

    private function getMySqlDbList() {
        try{
            $conn = new mysqli($this->host, $this->username, $this->password);

            if ($conn->connect_error) {
                $obj = new stdClass;
                $obj->message = $conn->connect_error;
                return $obj;
            }

            //Store in session for later use
            session_start();
            $_SESSION['dbType']  = $this->dbType;
            $_SESSION['dbHost']  = $this->host;
            $_SESSION['dbUsername']  = $this->username;
            $_SESSION['dbPassword']  = $this->password;

            $sql = "SHOW DATABASES";
            $result = $conn->query($sql);
            $rows = [];

            if ($result->num_rows > 0) {

                while($row = $result->fetch_assoc()) {
                    if($row['Database'] == "information_schema" 
                        || $row['Database'] == "mysql") continue;
                    array_push($rows, $row['Database']);
                } 
            }

            return $rows;

        }catch(Exception $e) {
            return null;
        }
    }

    private function getDb2List() {

        $conn = db2_connect ($this->host, $this->username, $this->password);

        if (!$conn) {
            $obj = new stdClass;
            $obj->message = "DB2 connection creation failed";
            return $obj;
        }
    }
}