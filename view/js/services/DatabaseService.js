class DatabaseService {

    fetchDbList(dbType, host, username, password, successCallback) {

        var invoker = new ApiInvoker();

        var success = function(data)
        {
            try {
                data = JSON.parse(data);
                successCallback(data);
            }catch(e) {
                console.log(e);
                alert("Server error");
            }
        }

        var failed = function(data)
        {

        }

        var usecase = "fetchDbList";
        var method = "GET";
        var params = {
            dbType: dbType,
            host: host,
            username: username,
            password: password
        };
        invoker.invokeApiCall(usecase, method, params, success, failed);
    }

    runSql(sql, successCallback) {

        var invoker = new ApiInvoker();
        
        //add limit to sql
        //sql = sql + " "

        var success = function(data)
        {
            try {
                data = JSON.parse(data);
                if(data.status == "error")
                    successCallback(data.message, 0);
                else
                    successCallback(data, 1);  // 1 for success
            } catch(e) {
                console.log(e);
                successCallback("The SQL syntax is not correct or all test values are not provided", 0); //0 for error
            }
        }

        var failed = function(data)
        {

        }

        var usecase = "runSql";
        var method = "GET";
        var params = {
            sql: sql
        };
        invoker.invokeApiCall(usecase, method, params, success, failed);
    } 
}