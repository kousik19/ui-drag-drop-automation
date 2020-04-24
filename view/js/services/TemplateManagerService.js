class TemplateManagerService
{
    saveTemplate(templateName, templateCategory, templateContent, successCallback)
    {
        var invoker = new ApiInvoker();

        var success = function(data)
        {
            //data = JSON.parse(data);
            successCallback(data);
        }

        var failed = function(data)
        {

        }

        var usecase = "saveTemplate";
        var method = "POST";
        var params = {
            "templateName": templateName,
            "templateCtegory": templateCategory,
            "templateContent": templateContent
        };

        invoker.invokeApiCall(usecase, method, params, success, failed);
    }
}