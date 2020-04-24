class BlockManagerService
{
    blockCollection = [];
    currentDraggingBlock;
    currentDraggingBlockContent;

    setBlockCollection(bc)
    {
        this.blockCollection = bc;
    }

    getBlockCollection()
    {
        return this.blockCollection;
    }

    setCurrentlyDraggingBlock(cdb)
    {
        this.currentDraggingBlock = cdb;
    }

    getCurrentlyDraggingBlock()
    {
        return this.currentDraggingBlock;
    }

    setCurrentlyDraggingBlockContent(content)
    {
        this.currentDraggingBlockContent = content;
    }

    getCurrentlyDraggingBlockContent()
    {
        return this.currentDraggingBlockContent;
    }

    getBlockByBlockId(bid)
    {
        if(this.blockCollection.length == 0)return null;

        for(var i=0; i< this.blockCollection.length; i++)
            if(this.blockCollection[i].id == bid)
                return this.blockCollection[i];
    }

    retrieveAllBlocks(successCallback)
    {
        var invoker = new ApiInvoker();

        var success = function(data)
        {
            data = JSON.parse(data);
            successCallback(data);
        }

        var failed = function(data)
        {

        }

        var usecase = "getBlocks";
        var method = "GET";
        var params = {};
        invoker.invokeApiCall(usecase, method, params, success, failed);
    }

    retrieveComponentContent(componentCategory, fileName, successCallback)
    {
        var invoker = new ApiInvoker();

        var success = function(data)
        {
            data = JSON.parse(data);
            successCallback(data.content);
        }

        var failed = function(data)
        {

        }

        var usecase = "getComponentContent";
        var method = "GET";
        var params = {
            "contentFile" : fileName,
            "componentType" : componentCategory
        }

        invoker.invokeApiCall(usecase, method, params, success, failed);
    }

    removeBlock(category, fileName, successCallback) {

        var invoker = new ApiInvoker();

        var success = function(data)
        {
            try{
                data = JSON.parse(data);
                successCallback(data);
            }catch(e) {
                alert("Error: Can not be deleted");
                console.log(e);
            }
        }

        var failed = function(data)
        {

        }

        var usecase = "removeComponent";
        var method = "POST";
        var params = {
            "file" : fileName,
            "category" : category
        }

        invoker.invokeApiCall(usecase, method, params, success, failed);
    }
}