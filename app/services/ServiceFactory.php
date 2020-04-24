<?php 

class ServiceFactory
{
    public function getService($package, $service)
    {
        $str = file_get_contents(__DIR__ . '/serviceConfig.json');
        $jsonConfig = json_decode($str, true);
        
        $package = $jsonConfig[$package]["package"];
        $serviceClass = $jsonConfig[$package]["services"][$service];

        require_once("$package/$serviceClass.php");
        $service = new $serviceClass;
        return $service;
    }
}