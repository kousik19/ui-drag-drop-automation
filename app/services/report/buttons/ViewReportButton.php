<?php 
require_once(__DIR__ . "/Button.php");

class ViewReportButton extends Button 
{
    public function getScript($idStartsWith)
    {
        $scriptHtml = "$(\"[id^='$idStartsWith']\").click(function(){";
        $scriptHtml .= $this->generateScriptForGettingFormData();
        $scriptHtml .= "    renderGrid(formdata);
        })";

        return $scriptHtml;
    }
}