class KmrEditor {
    uiEditor;
    bService;

    init() {
        this.bService = new BlockManagerService();

        var root = this;

        var success = function(config) {
            root.uiEditor = grapesjs.init(config);

            root.uiEditor.Panels.addPanel({
                id: 'panel-top',
                el: '.panel__top',
            });

            root.uiEditor.Panels.addPanel({
                id: 'basic-actions',
                el: '.panel__basic-actions',

                buttons: [

                    {
                        id: 'export',
                        className: 'btn-open-export',
                        label: 'Exp',
                        command: 'export-template',
                        context: 'export-template', // For grouping context of buttons from the same panel
                    },

                    {
                        id: 'dbprofile',
                        className: 'btn-open-export',
                        label: 'Database Profile',
                        command: 'dbprofile',
                        context: 'dbprofile',
                        command(editor){
                            var dbProfilingService = new DbProfilingService();
                            dbProfilingService.execute();

                        }
                    },

                    {
                        id: 'advcomp',
                        className: 'btn-open-export',
                        label: 'Advance Components',
                        command: 'advcomp',
                        context: 'advcomp',
                        command(editor){
                            var advCompService = new AdvanceComponentService();
                            advCompService.execute();

                        }
                    },

                    {
                        id: 'map',
                        className: 'btn-open-export',
                        label: 'Map SQL',
                        command: 'map-sql',
                        context: 'map-sql',
                        command(editor) {
                            var html = editor.getHtml();
                            GlobalConfig.generatedHtml = html;
                            GlobalConfig.generatedCss = editor.getCss();
                            var mService = new MappingManagerService();
                            //mService.configure(html);
                            mService.execute(html);
                        }
                    },

                    {
                        id: 'settings',
                        className: 'btn-open-export',
                        label: ' Settings',
                        command: 'settings',
                        context: 'settings',
                        command(editor) {
                            
                            var setService = new SettingsManagerService();
                            setService.execute();
                        }
                    },

                    {
                        id: 'save-template',
                        className: 'btn-show-json',
                        label: 'Save',
                        context: 'save',
                        command(editor) {

                            var Html = editor.getHtml();
                            var Css = editor.getCss();

                            editor.Modal.setTitle('Save Template')
                                .setContent(
                                    `<table class="mat-tbl">
                    <tr>
                      <td> <label> Layout Name : </label> </td> 
                      <td>  <input id="layoutName" class="" type="text" /> </td>
                    </tr>

                    <tr>
                      <td> <label> Layout Category : </label> </td>
                      <td>
                        <select id="layoutCategory" class="">
                          <option value="Structural Tags"> Structural </option>
                          <option value="Form Tags"> Form </option>
                          <option value="Table Tags"> Table </option>
                          <option value="List Tags"> List </option>
                          <option value="Formatting Tags"> Formatting </option>
                          <option value="Embaded Tags"> Embaded </option>
                          <option value="Layout"> Layout </option>
                        </select>
                      </td>
                    </tr>
                  </table>

                  <hr/>
                  <br />

                  <center>
                    <button class="savebutton" onclick="saveTemplate()"> SAVE </button>
                  </center>
                  `
                                )
                                .open()
                        }
                    }
                ],

            })

            // Define commands
            root.uiEditor.Commands.add('show-layers', {

                getRowEl(editor) { return editor.getContainer().closest('.editor-row'); },
                getLayersEl(row) { return row.querySelector('.layers-container') },

                run(editor, sender) {
                    const lmEl = this.getLayersEl(this.getRowEl(editor));
                    lmEl.style.display = '';
                },
                stop(editor, sender) {
                    const lmEl = this.getLayersEl(this.getRowEl(editor));
                    lmEl.style.display = 'none';
                },
            });

            root.uiEditor.Commands.add('show-styles', {

                getRowEl(editor) { return editor.getContainer().closest('.editor-row'); },
                getStyleEl(row) { return row.querySelector('.styles-container') },

                run(editor, sender) {
                    const smEl = this.getStyleEl(this.getRowEl(editor));
                    smEl.style.display = '';
                },
                stop(editor, sender) {
                    const smEl = this.getStyleEl(this.getRowEl(editor));
                    smEl.style.display = 'none';
                },
            });

            root.uiEditor.on('block:drag:stop', function(model) {
                //model.replaceWith("A");
                var interval = setInterval(function() {
                    var content = root.bService.getCurrentlyDraggingBlockContent();
                    if (content == undefined) return;

                    var min = 0;
                    var max = 10000;
                    var random = Math.random() * (+max - +min) + +min;

                    if(GlobalConfig.isDraggingInputElem)
                    {
                        //Show pop up asking for name
                        var name = showInputNamePopup()           //function is in DOMEventHandler

                        //var min = 0;
                        //var max = 10000;
                        //var random = Math.random() * (+max - +min) + +min; 

                        var generatedRandomId = name + "--" + random;  
                        var elem = $.parseHTML(content);
                        $(elem).attr("id", generatedRandomId);
                        var content = $('<div>').append($(elem)).html();
                        GlobalConfig.isDraggingInputElem = false;
                    }

                    if(GlobalConfig.isDraggingButtonElem)
                    {
                        //Show pop up asking for name
                        var name = showButtonPurposePopup()           //function is in DOMEventHandler

                        //var min = 0;
                        //var max = 10000;
                        //var random = Math.random() * (+max - +min) + +min; 

                        var generatedRandomId = name + "--" + random;
                        var elem = $.parseHTML(content);
                        $(elem).attr("id", generatedRandomId);
                        var content = $('<div>').append($(elem)).html();
                        GlobalConfig.isDraggingButtonElem = false;
                    }
                    
                    //centralized theming
                    var elem = $.parseHTML(content);
                    for(var e=0; e<elem.length; e++)
                    {
                        var cls = $(elem[e]).attr('class');
                        if(cls == undefined)
                            $(elem[e]).attr("class", "centralized-theme-" + random.toString().replace(".", ""));
                        else if(cls.indexOf('centralized-theme') < 0)
                            $(elem[e]).attr("class", "centralized-theme-" + random.toString().replace(".", ""));
                    }
                    //$(elem).attr("class", "centralized-theme-" + random.toString().replace(".", ""));
                    var content = $('<div>').append($(elem)).html();
                    
                    if (model != undefined)
                        model.replaceWith(content); //<<=========== Check it

                    
                    clearInterval(interval);
                }, 100)
            })

            root.uiEditor.on('block:drag:start', function(model) {

                var successCallback = function(content) {
                                        
                    //check if this is an input element
                    if(block.category != 'Layout' && (content.indexOf("data-elemtype=\"input\"") > 0 || content.indexOf("data-elemtype='input'") > 0))
                    {
                        GlobalConfig.isDraggingInputElem = true;
                    }

                    //check if this is button element
                    if(block.category != 'Layout' && content.indexOf("data-elemtype=\"button\"") > 0)
                    {
                        GlobalConfig.isDraggingButtonElem = true;
                    }

                    root.bService.setCurrentlyDraggingBlockContent(content);
                }
                var blockId = model.id;

                var block = root.bService.getBlockByBlockId(blockId);
                root.bService.setCurrentlyDraggingBlock(block);

                var componentCategory = block.category;
                var fileName = block.contentFile;
                root.bService.retrieveComponentContent(componentCategory, fileName, successCallback);

            })
        }

        this.getConfig(success);

        //configure right click on blocks
        //customRightClickOnBlocks();
    }

    getConfig(successCallback) {
        var root = this;

        //fetch block details
        var success = function(data) {
            //console.log(data);
            root.bService.setBlockCollection(data);

            for(var i=0; i < data.length; i++) {
                data[i].label = " &nbsp <div title='Delete' class='blockdeleteicon whitecard' onclick='deleteComponent(\""+ data[i].category +"\", \""+ data[i].contentFile+"\", this)'> <i class='fa fa-trash-o fa-2x' aria-hidden='true'></i> </div>" + data[i].label 
            }

            var config = {

                container: '#gjs',

                fromElement: true,

                height: '300px',
                width: 'auto',
                forceClass: false,

                storageManager: { type: null },

                panels: { defaults: [] },

                canvas: {

                    scripts: [
                        "js/uilib/jquery.min.js",
                        "js/uilib/bootstrap.min.js"
                    ],

                    styles: [
                        "css/uilib/bootstrap.min.css",
                        "css/uilib/custom.css",
                    ]
                },

                blockManager: {
                    appendTo: '#blocks',

                    blocks: data
                },

                layerManager: {
                    appendTo: '.layers-container'
                },

                panels: {
                    defaults: [{
                            id: 'layers',
                            el: '.panel__right',
                            // Make the panel resizable
                            resizable: {
                                maxDim: 350,
                                minDim: 200,
                                tc: 0, // Top handler
                                cl: 1, // Left handler
                                cr: 0, // Right handler
                                bc: 0, // Bottom handler
                                // Being a flex child we need to change `flex-basis` property
                                // instead of the `width` (default)
                                keyWidth: 'flex-basis',
                            },
                        },

                        {
                            id: 'panel-switcher',
                            el: '.panel__switcher',
                            buttons: [{
                                    id: 'show-layers',
                                    active: true,
                                    label: 'Layers',
                                    command: 'show-layers',
                                    // Once activated disable the possibility to turn it off
                                    togglable: false,
                                },
                                {
                                    id: 'show-style',
                                    active: true,
                                    label: 'Styles',
                                    command: 'show-styles',
                                    togglable: false,
                                }
                            ],
                        }
                    ]
                },

                selectorManager: {
                    appendTo: '.styles-container'
                },

                styleManager: {
                    appendTo: '.styles-container',

                    sectors: [

                        {
                            name: 'Dimension',
                            open: false,
                            // Use built-in properties
                            buildProps: ['width', 'min-height', 'padding', 'margin'],
                            // Use `properties` to define/override single property
                            properties: [{
                                // Type of the input,
                                // options: integer | radio | select | color | slider | file | composite | stack
                                type: 'integer',
                                name: 'The width', // Label for the property
                                property: 'width', // CSS property (if buildProps contains it will be extended)
                                units: ['px', '%'], // Units, available only for 'integer' types
                                defaults: 'auto', // Default value
                                min: 0, // Min value, available only for 'integer' types
                            }]
                        },

                        {
                            name: 'Extra',
                            open: false,
                            buildProps: ['font-size', 'font-family', 'color', 'background-color', 'background-image', 'font-size', 'font-weight', 'border'],
                            /*properties: [
                              {
                                id: 'font-size-prop',
                                name: 'Font Size',
                                property: 'font-size',
                                type: 'select',
                                defaults: '32px',
                                // List of options, available only for 'select' and 'radio'  types
                                options: [
                                  { value: '12px', name: 'Tiny' },
                                  { value: '18px', name: 'Medium' },
                                  { value: '32px', name: 'Big' },
                                ],
                            }
                            ]*/
                        }
                    ]
                }
            };

            successCallback(config);
        }

        this.bService.retrieveAllBlocks(success);

    }

    getHtml() {
        return this.uiEditor.getHtml();
    }

    getCss() {
        return this.uiEditor.getCss();
    }

    successTemplateSave() {
        //this.bService.retrieveAllBlocks(success);
        alert("Saved Successfully");
    }
}