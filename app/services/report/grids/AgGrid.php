<?php 
require_once("Grid.php");

class AgGrid extends Grid 
{
    public function getGridScript($config)
    {
        $gridHtml = "
        
        function renderGrid() {

            var colModel = [];

            for(var i=0; i<tableData.length; i++) {

                if(i == 0) {
                    for (var key in tableData[i]) {
                        var obj = {};
                        obj['headerName'] = key;
                        obj['field'] = key;
                        colModel.push(obj);
                    }
                    break;
                }
            }

            var gridOptions = {
                columnDefs: colModel,
                rowData: tableData
            };

            var eGridDiv = document.querySelector('#aggrid');
            new agGrid.Grid(eGridDiv, gridOptions);
        }";

        return $gridHtml;
    }
}