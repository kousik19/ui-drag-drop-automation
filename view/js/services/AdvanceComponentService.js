class AdvanceComponentService {

    execute() {
        ModalManagerService.openModal("advcompmodal");
    }

    saveComponent(compDetails) {

        var layoutname = compDetails.name;
        var layoutcategory = "Form Tags";
        var basicElem = compDetails.basicelem;
        var content = "";

        if(compDetails.mode == "manual") {
            if(basicElem == "select") {
                content += "<select data-elemtype=\"input\">";

                for(var i=0; i < compDetails.dynamicset.length; i++) {
                    content += "<option value='" + compDetails.dynamicset[i].value + "'>" ;
                    content += compDetails.dynamicset[i].label
                    content += "</option>"
                }

                content += "</select>";
            }
            else if(basicElem == "multiselect") {
                content += "<select data-elemtype=\"input\" multiple>";

                for(var i=0; i < compDetails.dynamicset.length; i++) {
                    content += "<option value='" + compDetails.dynamicset[i].value + "'>" ;
                    content += compDetails.dynamicset[i].label
                    content += "</option>"
                }

                content += "</select>";
            }
            else if(basicElem == "radio") {

                var name = Math.random();
                for(var i=0; i < compDetails.dynamicset.length; i++) {
                    content += "<input data-elemtype=\"input\" type='radio' value='" + compDetails.dynamicset[i].value + "' name='" + name + "'/> &nbsp; " ;
                    content += compDetails.dynamicset[i].label + " &nbsp; ";
                }
                
            }
            else if(basicElem == "checkbox") {

                var name = Math.random();
                for(var i=0; i < compDetails.dynamicset.length; i++) {
                    content += "<input data-elemtype=\"input\" type='checkbox' value='" + compDetails.dynamicset[i].value + "' name='" + name + "'/> &nbsp; " ;
                    content += compDetails.dynamicset[i].label + " &nbsp; ";
                }
            }

            this.saveAdvancedComponent(layoutname, layoutcategory, content);
        }
        else if(compDetails.mode == "db") {
            this.saveAdvancedComponent(layoutname, layoutcategory);
        }

        
    }

    saveAdvancedComponent(layoutname, layoutcategory, htmlContent) {

        var obj = {};
        obj.elemname = layoutname;
        obj.dynlbl = $("#dynlbl").val();
        obj.dynval = $("#dynval").val();
        obj.sql = $("#dynamicInputSql").val();
        obj.basicelem = $("#basicelemtype").val();

        if(htmlContent != undefined)
            obj.html = htmlContent;

        var success = function(data) {
            
            data = JSON.parse(data);
            var id = data.id;
            var content = "<input data-elemtype='input' type='text' dyn-id='" + id + "' value='" + layoutname + "' readonly />";

            var tservice = new TemplateManagerService();
            tservice.saveTemplate(layoutname, layoutcategory, content, function(data) {

                try{
                    data = JSON.parse(data);
    
                    if(data.status == "success"){
                        alert("Component is saved successfully");
                        window.location.reload();
                    }
                    else if(data.status == "error")alert("Error! while saving the component");
                }catch(e) {
                    alert("Server error");
                }
            })
        }

        var invoker = new ApiInvoker();
        invoker.invokeApiCall("getDynamicElementContent", "POST", obj, success, null);
    }
}