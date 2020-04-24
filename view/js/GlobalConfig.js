class GlobalConfig
{
    config = {

        "url" : "http://localhost/sammsa/dynamicReport/UIBuilder/app/index.php",
        "url_kmr" : "https://kmrapplications.com:9010/developer/dev/dynamicReports/app/index.php"
    }

    static editor;
    static isDraggingInputElem = false;
    static isDraggingButtonElem = false;
    static generatedSql;

    static editMode  = false;
    static editReportName;
    static editGridConf;
}