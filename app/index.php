<?php 
    require_once ("Config.php");
    require_once ("services/ServiceFactory.php");

    //error_reporting(-1);
    //ini_set("display_errors", 1);
    //ini_set('error_reporting', E_ALL);

    //Load application config
    Config::loadConfig();

    $package = $_REQUEST["req"];
    $service = $_REQUEST["usecase"];

    $factory = new ServiceFactory();
    $service = $factory->getService($package, $service);
    $result = $service->execute($_REQUEST);

    echo $result;
?>