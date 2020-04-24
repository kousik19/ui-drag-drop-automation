<?php 
require_once(__DIR__ . "/Button.php");

class PrintButton extends Button 
{
    public function getScript($idStartsWith)
    {
        $scriptHtml = "$(\"[id^='$idStartsWith']\").click(function(){";
        $scriptHtml .= $this->generateScriptForGettingFormData();

        $scriptHtml .= "

            var queryString = '';
            for (var key in formdata) {
                queryString += key + '=' + formdata[key] + '&';
            }
            queryString = queryString.substring(0, queryString.length - 1);

            $.ajax({
                url: 'portal.php?' + queryString,

                success: function(data) {
                    data = JSON.parse(data);
                    var props = [];
                    if(data.length > 0) {
                        var first = data[0];
                        for (var key in first) {
                           props.push(key);
                        }
                    }
                    printJS({printable: data, properties: props, type: 'json'});
                }
            })

            
        })";

        return $scriptHtml;
    }
}