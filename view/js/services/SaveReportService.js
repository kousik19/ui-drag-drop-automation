class SaveReportService {

    saveReport(reportName, html, sql, inputSqlMap, buttonMap, gridConfig, settings, finalFn) {

        var invoker = new ApiInvoker();

        var success = function(data)
        {
            try {

                data = JSON.parse(data);

                if(data.status == "error") {
                    alert("Error : " + data.message);
                    return;
                }
                alert("Report is saved successfully");
                $("#rlink").attr("href",  "http://localhost/sammsa/reports/" + reportName);
                //$("#rlink").attr("href",  "https://kmrapplications.com:9010/developer/dev/dynamicReports/reports/" + reportName);

                $("#rlink").show();

            }catch(e) {
                console.log(e);
                alert("Error : Report can not be generated");
            } finally {
                if(finalFn != undefined)
                    finalFn();
            }
            
        }

        var failed = function(data)
        {

        }

        var usecase = "saveReport";
        var method = "POST";
        var params = {
            reportName: reportName,
            html: html,
            sql: sql,
            settings: settings,
            sqlInputMap: inputSqlMap,
            buttonMap: buttonMap,
            gridConfig: gridConfig,
            editMode: GlobalConfig.editMode
        };
        invoker.invokeApiCall(usecase, method, params, success, failed);
    }
}