(function(){


    GlobalConfig.editor = new KmrEditor();
    GlobalConfig.editor.init();

    var dbProfilingService = new DbProfilingService();
    dbProfilingService.getProfiles();

    var miscService = new MiscellaneousService();
    miscService.execute();

})()