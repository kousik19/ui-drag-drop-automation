class MappingManagerService
{
    inputs;
    inputs = [];
    inputIds = [];
    buttons = [];
    buttonIds = [];

    configure(html)
    {
        var dom = $.parseHTML(html);
        /*var inputs = [];
        var inputIds = [];
        var buttons = [];
        var buttonIds = [];*/

        for(var i=0; i< dom.length; i++)
        {

            if (dom[i].hasChildNodes()) {
                this.configure(dom[i].innerHTML);
            }
            
            if(dom[i].dataset == undefined) continue;

            if(dom[i].dataset.elemtype == "input")
            {
                var id = dom[i].id;
                var purpose = id.split("--")[0];
                this.inputs.push(purpose);
                this.inputIds.push(id);
            }

            if(dom[i].dataset.elemtype == "button")
            {
                var id = dom[i].id;
                var purpose = id.split("--")[0];
                this.buttons.push(purpose);
                this.buttonIds.push(id);
            }
        }
    }

    execute(html) {
        this.configure(html);
        showMappingSQLPopup(this.inputs, this.inputIds, this.buttons, this.buttonIds);
        parseSql();
    }

    getSqlTagList(sql)
    {
        var tags = sql.match(/\${([^}]+)}/g);
        if(tags == undefined || tags == null)return [];

        for(var i=0; i< tags.length; i++)
            tags[i] = tags[i].replace(/\$/g,"").replace(/\{/g,"").replace(/\}/g,"");

        return tags;
    }

    constructSqlWithTestVal(sql, map) {

        for (var key in map) {
            sql = sql.replace("${" + key + "}", map[key]);
        }

        return sql;
    }


}