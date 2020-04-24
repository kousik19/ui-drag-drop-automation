<?php 

abstract class Button 
{
    public abstract function getScript($idStartsWith);

    protected function generateScriptForGettingFormData() {

        return "
        
            var inputs = $(\"[data-elemtype='input']\");
            var formdata = {};
            var processedNames = [];

            for(var i=0; i<inputs.length; i++) {
                var purpose = $(inputs[i]).attr('id').split('-')[0];
                var type = $(inputs[i]).attr('type');
                var name = $(inputs[i]).attr('name');
                var val = '';

                if(processedNames.indexOf(name) > -1) continue;

                if(type == 'checkbox') {
                    var vals = [];
                    $.each($(\"input[name='\"+ name +\"']:checked\"), function(){
                        vals. push(\"'\" + $(this). val() + \"'\");
                    })
                    val = vals.join(',');
                    processedNames.push(name);
                }
                else if(type == 'radio') {
                    val = $(\"input[name='\"+ name +\"']:checked\"). val();
                    processedNames.push(name);
                }
                else
                    val = $(inputs[i]).val();

                //for multiselect val can be array, in that case make it comma separated string
                if(Array.isArray(val))
                {
                    var temp = '';
                    for(var i=0; i< val.length; i++) {
                        if(i != 0)temp += ', ';
                        temp += \"'\" + val[i] + \"'\";
                    }
                    val = temp;
                }
                
                formdata[purpose] = val;
            }
            
            ";
    }
}