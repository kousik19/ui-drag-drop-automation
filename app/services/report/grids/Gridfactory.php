<?php 

class GridFactory
{
    private $gridMap = [
        "JqGrid" => "JqGrid",
        "Handsontable" => "Handsontable",
        "AgGrid" => "AgGrid"
    ];

    public function getGrid($grid) 
    {
        require_once($this->gridMap[$grid]. ".php");
        $className = $this->gridMap[$grid];
        $grid = new $className();
        return $grid;
    }
}