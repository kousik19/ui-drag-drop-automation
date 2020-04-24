<?php 
require_once("Grid.php");

class JqGrid extends Grid 
{
    public function getGridScript($config)
    {
        $colConfig = $config['columns'];
        $caption = $config['caption'];
        $colModel = "[";
        for($i = 0; $i < count($colConfig); $i++) {

            $colModel .= "{ 
                name : '". $colConfig[$i]['col-name'] ."', 
                label : '". $colConfig[$i]['col-header'] . "',
                sortable : ". $colConfig[$i]['col-sort'] . ",
                search : ". $colConfig[$i]['col-search'] . ",
                hidden : ". $colConfig[$i]['col-hide'] . ",
                align : '". $colConfig[$i]['col-align'] . "',
                formatter : '". $colConfig[$i]['col-datatype'] . "'
            }";

            if($i != count($colConfig) - 1)$colModel .= ", \n\t";
        }
        $colModel .= "]";

        $gridHtml = "
        
        function renderGrid(formdata) {

            var queryString = '';
            for (var key in formdata) {
                queryString += key + '=' + formdata[key] + '&';
            }
            queryString = queryString.substring(0, queryString.length - 1);

            if(!firstTimeGridLoad) {
                $(\"#grid\").jqGrid('GridUnload');
            }
            firstTimeGridLoad = false;

            var grid = $('#grid').jqGrid({

                url: 'portal.php?' + queryString,
                datatype: 'json',
                colModel: $colModel,
                //data: tableData,
                autowidth: true,
                emptyrecords: 'No Records To Display',
                pager: 'tblGridPager',
                loadonce: true,
                viewrecords: true,
                rowList: [10, 20, 50, 100], ";
        
        if($config["pagination"] != 0 && trim($config["pagination"]) != ""){
            $gridHtml .= "rowNum: ". $config["pagination"] .",";
        }
        else $gridHtml .= "rowNum: 50,";

        $gridHtml .= "   
                caption: '$caption'
        })
        grid.jqGrid('navGrid','#tblGridPager',{add:false,edit:false,del:false,search:true,refresh:true});
        }";

        return $gridHtml;
    }
}