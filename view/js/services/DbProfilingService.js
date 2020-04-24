class DbProfilingService {

    execute() {
        ModalManagerService.openModal("dbprofilingmodal");
    }

    saveProfile(data) {
        
        var usecase = "saveProfile";
        var restMethod = "POST";
        var root = this;
        var successCallback = function(response) {
            var res = JSON.parse(response);

            if(res.status == "success") {
                alert("Profile is saved");
                root.getProfiles();
            }
            else {
                alert("Error : " + res.message);
            }
        }
        var params = {"data" : data}
        var invoker = new ApiInvoker();
        invoker.invokeApiCall(usecase, restMethod, params, successCallback, null);
    }

    getProfiles() {

        var successCallback = function(data) {
            data = JSON.parse(data);
            if(data.status == "error") {
                alert("Error: Database Profiles Can Not Be Loaded");
            }
            else {
                if(data.length > 0)
                    populateDbProfileDropDown(data);
            }
        }
        var invoker = new ApiInvoker();
        invoker.invokeApiCall("retrieveProfiles", "GET", {}, successCallback, null)
    }

    setProfile(profileName) {

        var obj = {};
        obj.profile = profileName;
        var successCallback = function(data) {

            data = JSON.parse(data);
            if(data.status == "error") {
                alert("Error: Profile can not be set");
            }
            else {
                alert("Database profile is set");
            }
        }
        var invoker = new ApiInvoker();
        invoker.invokeApiCall("setProfile", "POST", obj, successCallback, null)
    }
}