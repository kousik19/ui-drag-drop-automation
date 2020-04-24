class EditReportService {

    getReportMetaData(reportName, callback) {
        
        var usecase = "getReportMetadata";
        var method = "GET";
        var params = {
            "reportName" : reportName
        };
        var invoker = new ApiInvoker();
        invoker.invokeApiCall(usecase, method, params, callback, null);
    }
}