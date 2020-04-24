class MiscellaneousService {

    execute() {
        
        //fetch reports list
        this.fetchReportList();
    }

    fetchReportList() {

        var invoker = new ApiInvoker();

        var success = function(data)
        {
            data = JSON.parse(data);
            populateEditReportDropdown(data)
        }

        var failed = function(data)
        {

        }

        var usecase = "getReports";
        var method = "GET";
        var params = {};
        invoker.invokeApiCall(usecase, method, params, success, failed);
    }
}