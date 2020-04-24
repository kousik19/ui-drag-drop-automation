<?php 
class DbProfileSetterService {

    public function execute($params) {

        try{
            session_start();

            $profile = $params['profile'];
            $jsonStr = file_get_contents(__DIR__ . "/dbprofiles.json");
            $json = json_decode($jsonStr);

            for($i=0; $i < count($json); $i++) {

                if($json[$i]->pname == $profile) {

                    $_SESSION['dbtype'] = $json[$i]->dbtype;
                    $_SESSION['dbhost'] = $json[$i]->dbhost;
                    $_SESSION['dbusername'] = $json[$i]->dbusername;
                    $_SESSION['dbpassword'] = $json[$i]->dbpassword;
                    $_SESSION['dbname'] = $json[$i]->dbname;
                }
            }

            $obj = new stdClass;
            $obj->status = "success";
            echo json_encode($obj);

        } catch(Exception $e) {
            $obj = new stdClass;
            $obj->status = "error";
            $obj->message = $e->getMessage();
            echo json_encode($obj); 
        }
    }
}