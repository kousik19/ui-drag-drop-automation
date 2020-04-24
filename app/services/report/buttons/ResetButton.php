<?php 
require_once(__DIR__ . "/Button.php");

class ResetButton extends Button 
{
    public function getScript($idStartsWith)
    {
        return  
        "$(\"[id^='$idStartsWith']\").click(function(){

            $('input').val('');
        })";
    }
}