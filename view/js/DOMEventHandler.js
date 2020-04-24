/**
 * Save template
 */

function saveTemplate()
{
    var layoutname = $("#layoutName").val();
    var layoutcategory = $("#layoutCategory").val();

    var html = GlobalConfig.editor.getHtml();
    var css = GlobalConfig.editor.getCss();

    var content = "<style>\n\n\t" + css + "\n</style>\n\n" + html; 

    var tservice = new TemplateManagerService();
    tservice.saveTemplate(layoutname, layoutcategory, content, GlobalConfig.editor.successTemplateSave)
}

/**
 * Show pop up for asking name of input elements
 */

 function showInputNamePopup()
 {
     var name = prompt("Enter input type purpose:")
     if(name.trim() == "")showInputNamePopup();
     else return name;
 }

 function showButtonPurposePopup()
 {
     var name = prompt("Enter Button purpose:")
     if(name.trim() == "")showButtonPurposePopup();
     else return name;
 }

 /**
  * Open mapping popup
  */
function showMappingSQLPopup(inputs, inputIds, buttons, buttonIds)
{
    var html = "";
    for(var i=0; i<inputs.length; i++)
    {
        var htmlInput = inputs[i].split("-")[0];
        html += "<tr>";
        html += "<td> <span id=\"inputelem_" + inputIds[i] + "\">" + inputs[i] + "</span></td>";
        html += "<td><select data-forinput = '" + htmlInput + "'  class=\"sql-tags-dropdown\" id=\"map_" + inputIds[i] + "\"></select></td>";
        html += "<td><input data-forinput = '" + htmlInput + "' onkeyup='runSqlWithTestval(this)' type=\"text\" id=\"testval_" + inputIds[i] + "\"></td>";
        html += "</tr>";
    }
    $("#mapping-body").html(html);

    var html = "<h5 style=\"background:#83bff8;padding: 3px\"> Configure Buttons </h5> <table style=\"width:100%\">";
    for(var i=0; i<buttons.length; i++)
    {
        var htmlInput = buttons[i].split("-")[0];
        html += "<tr>";
        html += "<td> <span data-btnid='"+ buttonIds[i] +"' id=\"buttonelem_" + buttonIds[i] + "\">" + buttons[i] + "</span></td>";
        html += "<td><select id=\"buttontype_"+ buttonIds[i] +"\"><option value='trigger'>Trigger Report</option><option value='reset'>Reset Form</option><option value='csv'>CSV Download</option></option><option value='excel'>Excel Download</option><option value='pdf'>PDF Download</option><option value='print'>Print Report</option></select></td>";
        html += "</tr>";
    }
    html += "</table>";

    $("#buttonarea").html(html);

    ModalManagerService.openModal("mapmodal");
}

/**
  * Close mapping popup, this is called on keyup of cross icon of popup
  */
function closeSqlMappingModal()
{
    ModalManagerService.closeModal("mapmodal");
}

/**
 * Close DB profiling modal
 */
function closeDbProfilingModal()
{
    ModalManagerService.closeModal("dbprofilingmodal");
}

/**
 * Close Advance component modal
 */
function closeAdvCompModal()
{
    ModalManagerService.closeModal("advcompmodal");
}

/**
 * Parse SQL tag, this is called on keyup od sql textarea
 */
function parseSql()
{
    document.querySelectorAll('textarea#sql-area').forEach((block) => {
        hljs.highlightBlock(block);
    });
    var sql = $("#sql-area").val();
    var tags = new MappingManagerService().getSqlTagList(sql);
    var dbname = $("#dbnamelist").val();

    populateTagDropdowns(tags);
}

/**
 * Execute the SQL query
 */
function getPreview() {
    var sql = $("#sql-area").val();
    var tags = new MappingManagerService().getSqlTagList(sql);

    if(tags.length == 0) {
        $("#runQueryForPreview").html("Please Wait...");
        dbService = new DatabaseService();
        dbService.runSql(sql, renderResultTable);
        return;
    }
}

/**
 * Populate the sql tag dropdowns from parsed sql in mapping section
 * @param {Sql Tag List} tags 
 */
function populateTagDropdowns(tags)
{
    if(tags == null || tags == undefined)return;
    var html = "";
    for(var i=0; i< tags.length; i++)
    {
        html += "<option>" + tags[i] + "</option>";
    }
    $(".sql-tags-dropdown").html(html);
}

function confirmDbDetails() {

    var dbtype = $("#dbtype").val();
    var host = $("#dbhost").val();
    var username = $("#dbusername").val();
    var password = $("#dbpassword").val();

    $dbService = new DatabaseService();
    $dbService.fetchDbList(dbtype, host, username, password, populateDbDropdown);
}

function populateDbDropdown(dbList) {

    var html = "";
    for(var i=0; i<dbList.length; i++) {
        html += "<option>" + dbList[i] + "</option>"
    }
    $("#dbnamelist").html(html);
}

function toggleCollapse() {
    $(".collapsible-container").toggle();
}

function renderResultTable(data, stat) {

    $("#runQueryForPreview").html("Run Query For Preview");

    if(stat == 0) {
        $(".collapsible-container").html("<p>" + data + "</p>");
        return;
    }

    var html = "<table class='mat-tbl'>"
    for(var i=0; i< data.length; i++) {

        var row = data[i];

        //based on the columns configure view GRID columns
        if(i == 0) {
            for (var key in row) html+= "<th>" + key + "</th>";
            renderGridConfigurationSection(row);
        }

        html += "<tr>";
        
        for (var key in row) 
            html += "<td>" + row[key] + "</td>"

        html += "</tr>";
    }

    html += "</table>";

    $(".collapsible-container").html(html);
}

function renderGridConfigurationSection(row) {

    //result table header
    var gridConfigureHtml = "<table class='map-table grid-config-table' style='font-size:12px'>"; 
    gridConfigureHtml += "<tbody>";
    gridConfigureHtml += "<tr>";
    for (var key in row) {

        //grid configure section table header
        gridConfigureHtml += "<tr>";

        //Col-0
        gridConfigureHtml += "<td> <input type='hidden' data-class='col-name' value='" + key + "' /></td>";

        //Col-1
        gridConfigureHtml += "<td> <input type='text' data-class='col-header' value='" + key + "' /></td>";

        //Col-2
        gridConfigureHtml += "<td>";
        gridConfigureHtml += "<input type='checkbox' data-class='col-sort' id='"+key+"-sort'> Enable Sorting";
        gridConfigureHtml += "</td>";

        //Col-3
        gridConfigureHtml += "<td>";
        gridConfigureHtml += "<select data-class='col-datatype'>";
        gridConfigureHtml += "<option value='text'> Text </option>";
        gridConfigureHtml += "<option value='number'> Number </option>";
        gridConfigureHtml += "<option value='integer'> Integer </option>";
        gridConfigureHtml += "<option value='currency'> Currency ($x.xx) </option>";
        gridConfigureHtml += "<option value='date'> Date </option>";
        gridConfigureHtml += "</select>";
        gridConfigureHtml += "</td>";

        //Col-4
        gridConfigureHtml += "<td>";
        gridConfigureHtml += "<select data-class='col-align'>";
        gridConfigureHtml += "<option value='left'> Left </option>";
        gridConfigureHtml += "<option value='center'> Center </option>";
        gridConfigureHtml += "<option value='right'> Right </option>";
        gridConfigureHtml += "</select>";
        gridConfigureHtml += "</td>";

        //Col-5
        gridConfigureHtml += "<td>";
        gridConfigureHtml += "<input type='checkbox' data-class='col-hide' id='"+key+"-hide'> Hide Column";
        gridConfigureHtml += "</td>";

        //Col-6
        gridConfigureHtml += "<td>";
        gridConfigureHtml += "<input type='checkbox' data-class='col-search' id='"+key+"-search'> Enable Searching";
        gridConfigureHtml += "</td>";

        gridConfigureHtml += "</tr>";
        //end grid configure section table header
    }
    gridConfigureHtml += "</tbody>";
    gridConfigureHtml += "</table>";

    gridConfigureHtml += "<div class='global-grid-config-section'>";
    gridConfigureHtml += "<p>Display Rows in each page: <input type='number' class='gridglobalconfig' id='pagination'/> (Default 50)</p> <br />";
    //gridConfigureHtml += "<p>Row List: <input type='text' class='gridglobalconfig' id='rowlist'/> (Default 10, 20, 50, 100)</p> <br />";

    $("#column-configure-section").html(gridConfigureHtml);

    //if edit mode
    if(GlobalConfig.editMode) {

        var columns = GlobalConfig.editGridConf.columns;
        var rows = $(".grid-config-table tr");
        for(var i=0; i< columns.length; i++) {

            for(var j=0; j< rows.length; j++) {

                if($(rows[j]).find("[data-class='col-name']").val() == columns[i]['col-name']) {
                    console.log(columns[i]['col-name'] + ":" + columns[i]['col-sort']);
                    $(rows[j]).find("[data-class='col-sort']").prop("checked", columns[i]['col-sort'] == "true");
                    $(rows[j]).find("[data-class='col-hide']").prop("checked", columns[i]['col-hide'] == "true");
                    $(rows[j]).find("[data-class='col-search']").prop("checked", columns[i]['col-search'] == "true");
                    $(rows[j]).find("[data-class='col-datatype']").val(columns[i]['col-datatype']);
                    $(rows[j]).find("[data-class='col-align']").val(columns[i]['col-align']);
                    $(rows[j]).find("[data-class='col-header']").val(columns[i]['col-header']);
                }
            }
        }
    }
}

function runSqlWithTestval(elem) {

    var map = constructTestValTagMap(elem);

    var mService = new MappingManagerService();
    var modifiedSql = mService.constructSqlWithTestVal($("#sql-area").val(), map);

    var dbname = $("#dbnamelist").val();
    dbService = new DatabaseService();
    dbService.runSql(modifiedSql, renderResultTable);
}

function constructTestValTagMap(elem) {

    var sqlVariables = $("[id^='map_']");
    var testVals = $("[id^='testval_']");
    var obj = {};

    for(var i=0; i<sqlVariables.length; i++) {
        var sqlvar = $(sqlVariables[i]).val();
        var idPart = $(sqlVariables[i]).attr("id").split("_")[1];
        var testvalId = "testval_" + idPart;

        for(var j=0; j< testVals.length; j++)
        {
            if($(testVals[i]).attr("id") == testvalId)
            {
                testval = $(testVals[i]).val();
                obj[sqlvar] = testval;
                break;
            }
        }
    }

    return obj;
    
}

function constructInputSqlTagMap() {

    var sqlVariables = $("[id^='map_']");
    var obj = {};

    for(var i=0; i<sqlVariables.length; i++) {
        var sqlvar = $(sqlVariables[i]).val();
        var forInput = $(sqlVariables[i]).attr("data-forinput");
        obj[sqlvar] = forInput;
    }
    return obj;
}

function constructButtonMap() {

    var buttons = $("[id^='buttonelem_']");
    var buttonTypes = $("[id^='buttontype_']");
    var obj = {};

    for(var i=0; i<buttons.length; i++) {
        var btn = $(buttons[i]).html();
        var buttonId = $(buttons[i]).attr("data-btnid");

        for(var j=0; j< buttonTypes.length; j++ ) {
            if($(buttonTypes[j]).attr("id") == "buttontype_" + buttonId)
            {
                obj[$(buttonTypes[j]).val()] = btn;
                break;
            }
        }
    }
    return obj;
}

function saveReport() {

    //get report name
    if(GlobalConfig.editMode)
    reportname = GlobalConfig.editReportName;
    else {
        reportname = prompt("Enter Report Name : ");
        if(reportname.trim() == "" ) {
            alert("Report name can not be blank");
            return;
        }
    }
    
    $("#rlink").hide();
    $("#saveReportButton").html("Please Wait...");

    //get html 
    var html = "<style>" + GlobalConfig.generatedCss + "</style>\n" + GlobalConfig.generatedHtml;

    //get sql 
    var sql = $("#sql-area").val();

    //get map
    var map = constructInputSqlTagMap();

    //get button map
    var btnMap = constructButtonMap();

    //get grid configuration
    var gridConfig = {};
    gridConfig.gridlib = $("#gridlib").val();
    gridConfig.columns = [];

    var trs = $(".grid-config-table tr");
    for(var i = 0; i < trs.length; i++) {
        var obj = {};
        var tds = $(trs[i]).find("td");
        for(var j=0; j < tds.length; j++) {
            var configInputElem = $(tds[j]).find("[data-class^='col-']")[0];

            if($(configInputElem).attr("type") == "checkbox")
                obj[$(configInputElem).attr("data-class")] = $(configInputElem).is(':checked');
            else 
                obj[$(configInputElem).attr("data-class")] = $(configInputElem).val();
        }

        if(Object.keys(obj).length > 0)
            gridConfig.columns.push(obj);
    }

    var globalConfigs = $(".gridglobalconfig");
    for(var i = 0; i < globalConfigs.length; i++) {
        gridConfig[$(globalConfigs[i]).attr("id")] = $(globalConfigs[i]).val();
    }

    var finalFn = function() {
        $("#saveReportButton").html("Save Report");
    }

    //settings data
    var useCentralizedTheme = $('#usecentralizedtheme').is(":checked")

    var settings = {};
    settings.useCentralizedTheme = useCentralizedTheme;

    var saveService = new SaveReportService();
    saveService.saveReport(reportname, html, sql, map, btnMap, gridConfig, settings, finalFn);

    
}

function clickOnTab(elem, tabbodyclass) {

    $(".tabbody").css("display", "none");
    $("." + tabbodyclass).css("display", "block");
    $(".tab").removeClass("activetab");
    $(elem).addClass("activetab");
}

function saveprofile() {
    var inputElems = $(".dbprofileinput");
    var data = [];

    for(var i=0; i < inputElems.length; i++) {
        var name = $(inputElems[i]).attr("id");
        var val = $(inputElems[i]).val();

        var obj = {};
        obj["name"] = name;
        obj["val"] = val;
        data.push(obj);
    }

    var profilingService = new DbProfilingService();
    profilingService.saveProfile(data);
}

function checkDefaultprofile(elem) {
    if($(elem).val() == "false")$(elem).val("true");
    else if($(elem).val() == "true")$(elem).val("false");
}

function populateDbProfileDropDown(list) {

    var html = "";
    for(var i=0; i< list.length; i++) {
        html += "<option value='" + list[i].pname + "'>" + list[i].pname + "</option>";
    }

    $("#dbprofilelist").html(html);
}

function setDbProfile() {

    var profileName = $("#dbprofilelist").val();
    var profilingService = new DbProfilingService();
    profilingService.setProfile(profileName);

}

function addNewManualEntry() {
    $("#manualentrysectioncol").append($(".manualdataset:last-of-type").clone());
}

function manualEntryDelete(elem) {
    $(elem).parent().remove();
}

function saveAdvanceComponent() {

    var obj = {};
    obj.name = $("#advelemname").val();
    obj.basicelem = $("#basicelemtype").val();
    obj.mode = $("#dynamicmode").val();
    
    var elements = $(".manualdataset");
    var set = [];
    for(var i=0; i< elements.length; i++) {

        var pair = {};
        var pairElems = $(elements[i]).find("input");
        pair.label = $(pairElems[0]).val();
        pair.value = $(pairElems[1]).val();

        set.push(pair);
    }

    obj.dynamicset = set;
    var advCompService = new AdvanceComponentService();
    advCompService.saveComponent(obj);
}

function handleDynamicModeChange() {

    var mode = $("#dynamicmode").val();
    if(mode == "manual") {

        $("#manualsection").show();
        $("#dbsection").hide();
    } else if(mode == "db") {

        $("#manualsection").hide();
        $("#dbsection").show();
    }
}

function runSqlForDynamicInput() {

    var dbservice = new DatabaseService();
    dbservice.runSql($("#dynamicInputSql").val(), function(data, status) {
        
        if(status == 0) {
            alert(data);
        }
        else {
            var row = data[0];
            var html = "";
            for (var key in row) {
                html += "<option value='" + key + "'>" + key + "</option>";
            }
            $("#dynlbl").html(html);
            $("#dynval").html(html);
        }
    })
}

function closeSettingsModal() {

    ModalManagerService.closeModal("settingsmodal");
}

function deleteComponent(category, file, elem) {

    if(confirm("Do you really want to delete the component ? ")) {
        var bmanager = new BlockManagerService();
        bmanager.removeBlock(category, file, function(data){
            if(data.status == "success"){
                $(elem).parent().parent().fadeOut(500);
                //alert("Component is deleted successfully");
            }
            else {
                alert("Error: Component can not be deleted. " + data.message);
            }
        })
    }
}

function populateEditReportDropdown(list) {

    var html = "";
    for(var i=0; i < list.length; i++)
        html += "<option value='" + list[i].name + "'>" + list[i].name + "</option>";
    
    $("#reportList").html(html);
}

function editReport() {

    var reportname = $("#reportList").val();
    GlobalConfig.editReportName = reportname;
    var eService = new EditReportService;
    eService.getReportMetaData(reportname, handleEditReportData);
}

function handleEditReportData(data) {

    data = JSON.parse(data);

    if(data.status == "error") {
        alert(data.msg);
        return;
    }

    GlobalConfig.editMode = true;
    $("#sql-area").val(data.sqlTemplate);
    GlobalConfig.editor.uiEditor.setComponents(data.htmlTemplate);
    var gridConfig = JSON.parse(data.gridConfigFile);;
    GlobalConfig.editGridConf = gridConfig;

    $("#gridlib").val(gridConfig.gridlib);
    closeSettingsModal();
}