<?php 
require_once(__DIR__ . "/../Button.php");

abstract class DownloadButton extends Button
{
    public abstract function getSpecificParams();

    public function getScript($idStartsWith)
    {
        $scriptHtml = "$(\"[id^='$idStartsWith']\").click(function(){";
        $scriptHtml .= $this->generateScriptForGettingFormData();

        $extraParams = $this->getSpecificParams();
        foreach($extraParams as $key => $value) {
            $scriptHtml .= "formdata['$key'] = '$value';\n";
        }
            

        $scriptHtml .= "$.ajax({

                url: 'portal.php',

                method: 'post',

                data: formdata,

                success: function(data) {
                    window.open('http://localhost/sammsa/dumpfiles/' + data);
                    //window.open('https://kmrapplications.com:9010/developer/dev/dynamicReports/temp/' + data);
                }
            })
        })";

        return $scriptHtml;
    }
}