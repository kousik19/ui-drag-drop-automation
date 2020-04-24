<?php 

class DbProfileRetrievalService {

    public function execute() {

        try{

            $path = __DIR__ . "/dbprofiles.json";
            $json = file_get_contents($path);
            echo $json;
            
        } catch(Exception $e) {
            echo "{'status' : 'error'}";
        }
    }
}