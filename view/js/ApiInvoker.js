class ApiInvoker
{
    apiMapping = {

        "getBlocks" : {"req": "component", "usecase": "getAllComponents"},
        "getComponentContent" : {"req": "component", "usecase": "getComponentContent"},
        "saveTemplate" : {"req": "component", "usecase": "saveComponent"},
        "removeComponent" : {"req": "component", "usecase": "removeComponent"},
        "getDynamicElementContent" : {"req": "component", "usecase": "getDynamicElementContent"},
        "fetchDbList" : {"req": "database", "usecase": "fetchDbList"},
        "runSql" : {"req": "database", "usecase": "runSql"},
        "saveReport" : {"req": "report", "usecase": "saveReport"},
        "getReports" : {"req": "report", "usecase": "getReports"},
        "getReportMetadata" : {"req": "report", "usecase": "getReportMetadata"},
        "saveProfile" : {"req": "dbprofile", "usecase": "saveProfile"},
        "retrieveProfiles" : {"req": "dbprofile", "usecase": "retrieveProfiles"},
        "setProfile" : {"req": "dbprofile", "usecase": "setProfile"}
    };

    invokeApiCall(usecase, restMethod, params, successCallback, error)
    {
        params = this.addRouteParams(usecase, params);
        var apiUrl = new GlobalConfig().config.url;

        $.ajax({

            url : apiUrl,

            method: restMethod,

            data: params,

            success: successCallback
        })
    }

    addRouteParams(ucase, params)
    {
        var obj =  this.apiMapping[ucase];
        var req = obj.req;
        var usecase = obj.usecase;

        params['req'] = req;
        params['usecase'] = usecase;

        return params;
    }
}