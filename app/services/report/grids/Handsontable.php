<?php 
require_once("Grid.php");

class Handsontable extends Grid 
{
    public function getGridScript($config)
    {
        $gridHtml = "
        
        function renderGrid() {

            var container = document.getElementById('handsontablegrid');

            var colHeader = [];
            var rows = [];

            for(var i=0; i<tableData.length; i++) {

                if(i == 0) {
                    for (var key in tableData[i]) {
                        colHeader.push(key);
                    }
                }

                var row = [];
                for (var key in tableData[i]) {
                    row.push(tableData[i][key]);
                }
                rows.push(row);
            }

            var hot = new Handsontable(container, {
                data: rows,
                colHeaders: colHeader,
                filters: true,
                dropdownMenu: true,
                stretchH: 'all'
            });
        }";

        return $gridHtml;
    }
}