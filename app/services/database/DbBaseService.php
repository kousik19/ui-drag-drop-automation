<?php 

class DbBaseService {

    protected function getDbConn($dbType, $dbName) {

        if($dbType == "mysql") {
            $this->host = $_SESSION['dbhost'];
            $this->username = $_SESSION['dbusername'];
            $this->password = $_SESSION['dbpassword'];

            $conn = new mysqli($this->host, $this->username, $this->password, $dbName);

            return $conn;
        }
        else if($dbType == "db2") {
            $this->host = $_SESSION['dbhost'];
            $this->username = $_SESSION['dbusername'];
            $this->password = $_SESSION['dbpassword'];
            
            //$conn_string = "DATABASE=" . $dbName . ";HOSTNAME=" . $this->host . ";PORT=" . $this->port . ";PROTOCOL=TCPIP;UID=" . $this->username . ";PWD=" . $this->password . ";";
     
            $conn = db2_connect ("", $this->username, $this->password);
            //$conn = db2_connect ($conn_string, "", "");
            db2_exec($conn, "SET CURRENT SCHEMA = '$dbName'");

            return $conn;
        }
    }
}