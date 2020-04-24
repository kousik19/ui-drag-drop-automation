<?php 
class DbProfileSaverService {

    public function execute($params) {

        try{
            $profileData = $params['data'];

            $path = __DIR__ . "/dbprofiles.json";
            if(!file_exists($path)) {
                $content = "[]";
                $fp = fopen($path,"wb");
                fwrite($fp,$content);
                fclose($fp);
            }

            $json = file_get_contents($path);
            $json = json_decode($json, true);
            $nameStore = [];
            for($i=0; $i < count($json); $i++ ) {
                array_push($nameStore, $json[$i]['pname']);
            }

            $data = new stdClass;
            for($i=0; $i < count($profileData); $i++) {
                if(
                    $profileData[$i]['name'] == "pname" && 
                    in_array($profileData[$i]['val'], $nameStore))
                {
                    $obj = new stdClass;
                    $obj->status = 'error';
                    $obj->message = "Profile name already exists";
                    echo json_encode($obj);;
                    die;
                }
                $data->{$profileData[$i]['name']} = $profileData[$i]['val'];
            }

            array_push($json, $data);
            $jsonStr = json_encode($json);
            file_put_contents($path, $jsonStr);
            $obj = new stdClass;
            $obj->status = 'success';
            echo json_encode($obj);
        } catch(Exception $e) {
            $obj = new stdClass;
            $obj->status = 'error';
            $obj->message = $e->getMessage();
            echo json_encode($obj);
        }
    }
}