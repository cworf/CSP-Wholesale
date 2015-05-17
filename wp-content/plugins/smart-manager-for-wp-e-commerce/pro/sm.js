// Function to batchUpdate Records.
var batchUpdateRecords = function(batchUpdatePanel,toolbarCount,cnt_array,store,jsonURL,batchUpdateWindow,radioValue,flag,pagingToolbar,products_search_flag,SM_IS_WOO16,SM_IS_WOO21,SM_IS_WOO22)
{	
	var sb;
	var selectedField     = [];
	var selectedFields    = [];
	var selectedAction    = [];
	var dropDown3Value    = [];
        var dropDown4Value    = [];
	var value             = [];
	var selected          = [];
	var ids               = [];
	var values            = [];
	var regionId          = [];
	var columnDetailsArr  = [];

	var firstFieldDropdownCmp    = batchUpdatePanel.items.items[0].items.items[0].items.items[0];
	var firstActionDropdownCmp   = batchUpdatePanel.items.items[0].items.items[0].items.items[2];
	if (wpscRunning == 1) {
		var firstCategoryDropdownCmp = batchUpdatePanel.items.items[0].items.items[0].items.items[4];
		var firstTextFieldCmp        = batchUpdatePanel.items.items[0].items.items[0].items.items[5];
		var countryDropdownCmp       = batchUpdatePanel.items.items[0].items.items[0].items.items[6];
		var stateTextCmp       		 = batchUpdatePanel.items.items[0].items.items[0].items.items[11];
	} else {
		var firstCategoryDropdownCmp = batchUpdatePanel.items.items[0].items.items[0].items.items[7];
		var firstTextFieldCmp        = batchUpdatePanel.items.items[0].items.items[0].items.items[6];
		var countryDropdownCmp       = batchUpdatePanel.items.items[0].items.items[0].items.items[4];
		var attributeValueCmp		 = batchUpdatePanel.items.items[0].items.items[0].items.items[8];
		var firstTextAreaCmp		 = batchUpdatePanel.items.items[0].items.items[0].items.items[10];
	}
	var weightUnitCmp            = batchUpdatePanel.items.items[0].items.items[0].items.items[7];
	var regionIdCmp              = batchUpdatePanel.items.items[0].items.items[0].items.items[9];
	
        var toolbarValidationCond    = new Array();
	toolbarValidationCond.push(firstFieldDropdownCmp.isValid());
	toolbarValidationCond.push(firstActionDropdownCmp.isValid());
	if(!firstCategoryDropdownCmp.hidden)
	toolbarValidationCond.push(firstCategoryDropdownCmp.isValid());
	if(!firstTextFieldCmp.hidden)
	toolbarValidationCond.push(firstTextFieldCmp.isValid());
	
	if(wpscRunning == 1) {
		if(!weightUnitCmp.hidden)
			toolbarValidationCond.push(weightUnitCmp.isValid());	
	} else {
		if(!countryDropdownCmp.hidden)
			toolbarValidationCond.push(countryDropdownCmp.isValid());
	}
	
	for(sb=1; sb<toolbarCount; sb++) {
		if(batchUpdatePanel.items.items[sb] != undefined) {
			toolbarValidationCond.push(batchUpdatePanel.items.items[sb].items.items[0].isValid());
			toolbarValidationCond.push(batchUpdatePanel.items.items[sb].items.items[2].isValid());
			if(!batchUpdatePanel.items.items[sb].items.items[4].hidden)
				toolbarValidationCond.push(batchUpdatePanel.items.items[sb].items.items[4].isValid());
			
			if(wpscRunning == 1) {
				if(!batchUpdatePanel.items.items[sb].items.items[5].hidden)
					toolbarValidationCond.push(batchUpdatePanel.items.items[sb].items.items[5].isValid());
				if(!batchUpdatePanel.items.items[sb].items.items[7].hidden)
					toolbarValidationCond.push(batchUpdatePanel.items.items[sb].items.items[7].isValid());
				if(!batchUpdatePanel.items.items[sb].items.items[9].hidden)
					toolbarValidationCond.push(batchUpdatePanel.items.items[sb].items.items[9].isValid());				
			} else {
				if(!batchUpdatePanel.items.items[sb].items.items[6].hidden)
					toolbarValidationCond.push(batchUpdatePanel.items.items[sb].items.items[6].isValid());
				if(!batchUpdatePanel.items.items[sb].items.items[8].hidden)
					toolbarValidationCond.push(batchUpdatePanel.items.items[sb].items.items[8].isValid());
				if(!batchUpdatePanel.items.items[sb].items.items[10].hidden)
					toolbarValidationCond.push(batchUpdatePanel.items.items[sb].items.items[10].isValid());
			}
		}
	}

	var toolbarValidationResult = firstFieldDropdownCmp.isValid();
	for(var toolbarNo = 0;toolbarNo < toolbarValidationCond.length ;toolbarNo ++) {
		toolbarValidationResult = toolbarValidationResult && toolbarValidationCond[toolbarNo];
	}

	if(toolbarValidationResult) {
		for(sb=0; sb<toolbarCount; sb++) {
			if(batchUpdatePanel.items.items[sb] != undefined) {
				if(SM.activeModule == 'Products'){
                                        var image;
					var columnDetails = {
						colId: '',
						colName: '',
						tableName: '',
						action: '',
						colValue: '',
						unit: '',
						colFilter: '',
						updateColName: '',
						colType: '' // For Custom columns
						};
	
						if(sb == 0) {
							columnDetails.colId         = firstFieldDropdownCmp.value;
							columnDetails.colName       = SM['productsCols'][columnDetails.colId]['colName'];
							columnDetails.tableName     = SM['productsCols'][columnDetails.colId]['tableName'];;
							columnDetails.action        = firstActionDropdownCmp.value;
							columnDetails.colValue      = (firstTextFieldCmp.getValue() == '') ? firstCategoryDropdownCmp.value : firstTextFieldCmp.getValue();
							columnDetails.colFilter     = SM['productsCols'][columnDetails.colId]['colFilter'];
							columnDetails.updateColName = SM['productsCols'][columnDetails.colId]['updateColName'];
							columnDetails.colType 		= (SM['productsCols'][columnDetails.colId]['colType'] != undefined) ? SM['productsCols'][columnDetails.colId]['colType'] : '';
							if ( wpscRunning == 1 ) {
								columnDetails.unit          = weightUnitCmp.value;
							} else if ( wooRunning == 1 ) {
								columnDetails.unit          = attributeValueCmp.getValue();

								//Condition for description and additional description
								if(firstTextAreaCmp != undefined) {
									columnDetails.colValue  = (firstTextAreaCmp.getValue() != '') ? firstTextAreaCmp.getValue() : columnDetails.colValue;
								}
							}
                                                        
                            //Code for handling the image batch update
                            if (columnDetails.colName == 'thumbnail') {
                                if ( wpscRunning == 1 ) {
                                    image = batchUpdatePanel.items.items[sb].items.items[0].items.items[13];
                                }
                                else if ( wooRunning == 1 ) {
                                    image = batchUpdatePanel.items.items[sb].items.items[0].items.items[9];
                                }
                                columnDetails.colValue = image['image_id'];
                            }
                                                        
							columnDetailsArr.push(columnDetails);
	
							selectedField[sb]  = firstFieldDropdownCmp.value;
							selectedAction[sb] = firstActionDropdownCmp.value;
							dropDown3Value[sb] = (weightUnitCmp.value)? weightUnitCmp.value : '';
							if(wpscRunning == 1){
								regionId[sb]       = regionIdCmp.value;								
							}
							(Ext.isNumber(selectedField[sb])) ? value[sb] = firstCategoryDropdownCmp.value : (value[sb] = (firstTextFieldCmp.getValue()) ? firstTextFieldCmp.getValue() : '') ;
						}else{
							columnDetails.colId         = batchUpdatePanel.items.items[sb].items.items[0].value;
							columnDetails.colName       = SM['productsCols'][columnDetails.colId]['colName'];
							columnDetails.tableName     = SM['productsCols'][columnDetails.colId]['tableName'];
							columnDetails.colType 		= (SM['productsCols'][columnDetails.colId]['colType'] != undefined) ? SM['productsCols'][columnDetails.colId]['colType'] : '';
							columnDetails.action        = (batchUpdatePanel.items.items[sb].items.items[2].value) ? batchUpdatePanel.items.items[sb].items.items[2].value : '';
                                                        if ( wpscRunning == 1 ) {
								columnDetails.colValue      = (batchUpdatePanel.items.items[sb].items.items[5].getValue() == '') ? batchUpdatePanel.items.items[sb].items.items[4].value : batchUpdatePanel.items.items[sb].items.items[5].getValue();
								if(batchUpdatePanel.items.items[sb].items.items[7] != undefined) {
									columnDetails.unit  = (batchUpdatePanel.items.items[sb].items.items[7].value) ? batchUpdatePanel.items.items[sb].items.items[7].value : '';
								}
							} else if ( wooRunning == 1 ) {
								columnDetails.colValue      = (batchUpdatePanel.items.items[sb].items.items[6].getValue() == '') ? batchUpdatePanel.items.items[sb].items.items[7].value : batchUpdatePanel.items.items[sb].items.items[6].getValue();
								if(batchUpdatePanel.items.items[sb].items.items[8] != undefined) {
									columnDetails.unit  = (batchUpdatePanel.items.items[sb].items.items[8].getValue() != '') ? batchUpdatePanel.items.items[sb].items.items[8].getValue() : '';
								}

								//Condition for description and additional description
								if(batchUpdatePanel.items.items[sb].items.items[10] != undefined) {
									columnDetails.colValue  = (batchUpdatePanel.items.items[sb].items.items[10].getValue() != '') ? batchUpdatePanel.items.items[sb].items.items[10].getValue() : columnDetails.colValue;
								}
							}
							columnDetails.colFilter     = SM['productsCols'][columnDetails.colId]['colFilter'];
							columnDetails.updateColName = SM['productsCols'][columnDetails.colId]['updateColName'];
							
                                                        //Code for handling the image batch update
                                                        if (columnDetails.colName == 'thumbnail') {
                                                            image = batchUpdatePanel.items.items[sb].items.items[0].items.items[13];
                                                            columnDetails.colValue = image['image_id'];
                                                        }
                                                        
                                                        columnDetailsArr.push(columnDetails);
					}
				}else if(SM.activeModule == 'Customers' || SM.activeModule == 'Orders'){
					if(sb == 0) {
						
						selectedField[sb]  = firstFieldDropdownCmp.value;
						selectedAction[sb] = firstActionDropdownCmp.value;

						if(wpscRunning == 1){
							dropDown3Value[sb] = (weightUnitCmp.value)? weightUnitCmp.value : '';
							regionId[sb]       = ( regionIdCmp.value ) ? regionIdCmp.value : stateTextCmp.getValue();
						} else if(wooRunning == 1){
//							dropDown3Value[sb] = (firstCategoryDropdownCmp.value)? firstCategoryDropdownCmp.value : batchUpdatePanel.items.items[sb].items.items[0].items.items[4].value;
                            dropDown3Value[sb] = batchUpdatePanel.items.items[sb].items.items[0].items.items[4].value;
                            regionId[sb] = firstCategoryDropdownCmp.value;
						}
						(Ext.isNumber(selectedField[sb])) ? value[sb] = firstCategoryDropdownCmp.value : (value[sb] = (firstTextFieldCmp.getValue()) ? firstTextFieldCmp.getValue() : '') ;
					}else{
						selectedField[sb]  = batchUpdatePanel.items.items[sb].items.items[0].value;
						selectedAction[sb] = (batchUpdatePanel.items.items[sb].items.items[2].value) ? batchUpdatePanel.items.items[sb].items.items[2].value : '';
						if ( wpscRunning == 1 ) {	
							if(batchUpdatePanel.items.items[sb].items.items[6] != undefined)
								dropDown3Value[sb] = (batchUpdatePanel.items.items[sb].items.items[6].value) ? batchUpdatePanel.items.items[sb].items.items[6].value : batchUpdatePanel.items.items[sb].items.items[7].value;
							if(batchUpdatePanel.items.items[sb].items.items[4] != undefined)
								var categoryId = batchUpdatePanel.items.items[sb].items.items[4].value;
							if(batchUpdatePanel.items.items[sb].items.items[5] != undefined)
								var textFieldValue = (batchUpdatePanel.items.items[sb].items.items[5].getValue()) ? batchUpdatePanel.items.items[sb].items.items[5].getValue() : '';
							if(batchUpdatePanel.items.items[sb].items.items[9] != undefined)
								regionId[sb]  = batchUpdatePanel.items.items[sb].items.items[9].value;
							if(batchUpdatePanel.items.items[sb].items.items[11] != undefined && regionId[sb] == '')
								regionId[sb]  = batchUpdatePanel.items.items[sb].items.items[11].getValue();
							
						} else if ( wooRunning == 1 ) {
							
							if(batchUpdatePanel.items.items[sb].items.items[4] != undefined)
								dropDown3Value[sb] = (batchUpdatePanel.items.items[sb].items.items[4].value) ? batchUpdatePanel.items.items[sb].items.items[4].value : '';
							if(batchUpdatePanel.items.items[sb].items.items[6] != undefined)
								var textFieldValue = (batchUpdatePanel.items.items[sb].items.items[6].getValue()) ? batchUpdatePanel.items.items[sb].items.items[6].getValue() : '';
							if(batchUpdatePanel.items.items[sb].items.items[7] != undefined)
								regionId[sb]  = (batchUpdatePanel.items.items[sb].items.items[7].getValue()) ? batchUpdatePanel.items.items[sb].items.items[7].getValue() : '';
						}
						(Ext.isNumber(selectedField[sb])) ? value[sb] = categoryId : value[sb] = textFieldValue;
					}
				}
		selected[sb] = selectedFields.concat(selectedField[sb],selectedAction[sb],value[sb],dropDown3Value[sb],regionId[sb]);
			}
		}
		
		var selectedrows = editorGrid.getSelectionModel();
		var records      = selectedrows.selections.keys;

		if(cnt_array) {    // For new Products START
			var selected_ones    = editorGrid.getSelectionModel();
			var selected_records = selected_ones.selections.items;
			var modified         = store.getModifiedRecords();
			var selectedarray    = [];
			var edited           = [];
			var changes          = [];			
			
			if(selected_records) {
				Ext.each(selected_records, function(r, i) {
					var categoryName = r.get('category');
					r.data.category = newCatId;
					
                                        if (r.store.baseParams.active_module == 'Customers') {
                                            r.data.last_order_id = r.json.last_order_id;
                                        }
                                        
					selectedarray.push(r.data);
				});
			}

			if(modified) {
				Ext.each(modified, function(r, i) {
					var categoryName = r.get('category');
					r.data.category = newCatId;
					
					edited.push(r.data);
				});
			}
		}

		Ext.each(records, function(r, i) {
			ids.push(r);
		});
		
		Ext.each(selected, function(r, i) {
			values.push(r);
		});
//		batchUpdateWindow.loadMask.show();

                var progress = Ext.MessageBox.show({
                    title: 'Please wait',
                    msg: 'Updating...',
                    progressText: 'Initializing...',
                    width: 300,
                    progress: true,
                    closable: false
                });

        var o2 = {
            // url:jsonURL,
            url: (ajaxurl.indexOf('?') !== -1) ? ajaxurl + '&action=sm_include_file' : ajaxurl + '?action=sm_include_file',
            method:'post',
            callback: function(options, success, response){
                

            }
            ,scope:this
            ,params: {
                cmd:'batchUpdateprice',
                activeModule: SM.activeModule,
                ids:Ext.encode(ids),
                values:Ext.encode(values),
                selected:Ext.encode(selectedarray),
                edited:Ext.encode(edited),
                updateDetails: Ext.encode(columnDetailsArr),
                radio: radioValue,
                flag: flag,
                wpscRunning: wpscRunning,
                wooRunning: wooRunning,
                incVariation: SM.incVariation,
                isWPSC37 :isWPSC37,
                SM_IS_WOO16: SM_IS_WOO16,
                SM_IS_WOO21: SM_IS_WOO21,
                SM_IS_WOO22: SM_IS_WOO22,
                file:  jsonURL
            }
        };

        //Code to get the active page of the extjs editor grid
        var activePage = Math.ceil((pagingToolbar.cursor + pagingToolbar.pageSize) / pagingToolbar.pageSize);
        
        var o1 = {
            // url:jsonURL,
            url: (ajaxurl.indexOf('?') !== -1) ? ajaxurl + '&action=sm_include_file' : ajaxurl + '?action=sm_include_file',
            method:'post',
            callback: function(options, success, response){
                try{
                    var myJsonObj = Ext.decode(response.responseText);
                    //batchUpdateWindow.loadMask.hide();

                    if(true !== success){
                        Ext.notification.msg('Failed',response.responseText);
                        return;
                    } else {
                        Ext.notification.msg('Success');
                    }

                }catch(e){
                    Ext.notification.msg('Warning', 'Batch Update was not successful');
                }
                store.commitChanges();
                pagingToolbar.changePage(activePage); // code to load the load the page on the current active page


            }
            ,scope:this
            ,params: {
                cmd:'batchUpdatesync',
                activeModule: SM.activeModule,
                ids:Ext.encode(ids),
                values:Ext.encode(values),
                selected:Ext.encode(selectedarray),
                edited:Ext.encode(edited),
                updateDetails: Ext.encode(columnDetailsArr),
                radio: radioValue,
                flag: flag,
                wpscRunning: wpscRunning,
                wooRunning: wooRunning,
                incVariation: SM.incVariation,
                isWPSC37 :isWPSC37,
                SM_IS_WOO16: SM_IS_WOO16,
                SM_IS_WOO21: SM_IS_WOO21,
                SM_IS_WOO22: SM_IS_WOO22,
                file:  jsonURL
            }
        };

        function batch_update(count,total_records) {
            
            var arr = new Array();
            updatecnt = 0;
            if (total_records > 50) {
                fupdatecnt = 50;
            }
            else{
                fupdatecnt = total_records;
            }
            
            var task = new Ext.util.DelayedTask(function(){
                progress.hide();
                batchUpdateWindow.hide();
                batchUpdateWindow.loadMask.show();
                if (wooRunning == 1 && (SM.activeModule == 'Products')) {
                    Ext.Ajax.request(o1);
                }
                else {
                    store.commitChanges();
//                    store.load();
                    pagingToolbar.changePage(activePage); // code to load the load the page on the current active page
                }
            });
            
            for (var i=0;i<=count;i++) {
                arr[i] = {
            // url:jsonURL,
            url: (ajaxurl.indexOf('?') !== -1) ? ajaxurl + '&action=sm_include_file' : ajaxurl + '?action=sm_include_file',
            method:'post',
            callback: function(options, success, response){
                try{
                    var myJsonObj = Ext.decode(response.responseText);
                                var nxtreq      = myJsonObj.nxtreq;
                                var per          = myJsonObj.per;
                                var val          = myJsonObj.val;
                    //batchUpdateWindow.loadMask.hide();
                    if(true !== success){
                        Ext.notification.msg('Failed',response.responseText);
                        return;
                    } else {
                                    
                                    progress.updateProgress(val,per+"% Completed");
                                    
                                    if (nxtreq < count) {
                                        Ext.Ajax.request(arr[nxtreq]); 
                                    }
                                    else {
                                        task.delay(2500);
                                    }
                                    
                                    if (per == 100) {
                        Ext.notification.msg('Success', myJsonObj.msg);
                    }

                    }
                }catch(e){
                    Ext.notification.msg('Warning', 'Batch Update was not successful');					
                }
            }
            ,
            scope:this
            ,
            params: {
                            cmd :'batchUpdate',
                            activeModule: SM.activeModule,
                            part : i+1,
                            count : count,
                            updatecnt : updatecnt,
                            fupdatecnt : fupdatecnt,
                            wpscRunning: wpscRunning,
                            wooRunning: wooRunning,
                            values:Ext.encode(values),
                            selected:Ext.encode(selectedarray),
                            edited:Ext.encode(edited),
                            updateDetails: Ext.encode(columnDetailsArr),
                            ids:Ext.encode(ids),
                            radio: radioValue,
                            flag: flag,
                            incVariation: SM.incVariation,
                            isWPSC37 :isWPSC37,
                            SM_IS_WOO16: SM_IS_WOO16,
                            SM_IS_WOO21: SM_IS_WOO21,
                            SM_IS_WOO22: SM_IS_WOO22,
                            file:  jsonURL
                        }
                    };
                    
                    updatecnt = fupdatecnt;
                    if ((fupdatecnt+50) <= total_records) {
                          fupdatecnt = fupdatecnt +50;
                    }
                     else{
                        fupdatecnt = total_records;
                    }
            }
            Ext.Ajax.request(arr[0]); 
        }

        var o = {
            // url:jsonURL,
            url: (ajaxurl.indexOf('?') !== -1) ? ajaxurl + '&action=sm_include_file' : ajaxurl + '?action=sm_include_file',
            method:'post',
            callback: function(options, success, response){
                try{
                    var myJsonObj = Ext.decode(response.responseText);
                    //batchUpdateWindow.loadMask.hide();
                    if(true !== success){
                        Ext.notification.msg('Failed',response.responseText);
                        return;
                    } else {
                                var count_batch = myJsonObj.count_batch;
                                var total_batch = myJsonObj.total_records;
                                batch_update(count_batch,total_batch);
                    }
                    
                    
                }catch(e){
                    Ext.notification.msg('Warning', 'Batch Update was not successful');					
                }
            }
            ,
            scope:this
            ,
            params: {
                cmd:'batchUpdate',
                part : 'initial',
                activeModule: SM.activeModule,
                ids:Ext.encode(ids),
                values:Ext.encode(values),
                selected:Ext.encode(selectedarray),
                edited:Ext.encode(edited),		
                updateDetails: Ext.encode(columnDetailsArr),
                radio: radioValue,
                flag: flag,
                wpscRunning: wpscRunning,
                wooRunning: wooRunning,
                incVariation: SM.incVariation,
                isWPSC37 :isWPSC37,
                SM_IS_WOO16: SM_IS_WOO16,
                SM_IS_WOO21: SM_IS_WOO21,
                SM_IS_WOO22: SM_IS_WOO22,
                products_search_flag : products_search_flag,
                file:  jsonURL
            }
        };
		Ext.Ajax.request(o);
	}else
	Ext.notification.msg('Warning', getText('Either the fields are empty or invalid'));	
};

var showPrintWindow = function(editorGrid){
	selectedrows = editorGrid.getSelectionModel();
	records      = selectedrows.selections.keys;
	var win 	 = window.open('', 'Invoice / Packing Slip');
	
	var o = {
		// url:jsonURL
		url: (ajaxurl.indexOf('?') !== -1) ? ajaxurl + '&action=sm_include_file' : ajaxurl + '?action=sm_include_file'
		,method:'post'
		,callback: function(options, success, response)	{

			if(true !== success){
				Ext.notification.msg(getText('Failed'),response.responseText);
				return;
			}try{
				var purchaseLogs = response.responseText;
				purchaseLogs = purchaseLogs.trim();
				win.document.write(purchaseLogs);
				win.document.close();
				win.print();
				return;
			}catch(e){
				var err = e.toString();
				Ext.notification.msg(getText('Error'), err);
				return;
			}
		}
		,scope:this
		,params:
		{
			cmd: 'getData',
			label:'getPurchaseLogs',
			active_module: SM.activeModule,
			log_ids:Ext.encode(records),
			file:  jsonURL
		}};
		Ext.Ajax.request(o);
};

// var showPrintWindow = function(editorGrid){
// 	selectedrows = editorGrid.getSelectionModel();
// 	records      = selectedrows.selections.keys;
// 	var win 	 = window.open('', 'Invoice / Packing Slip');
	
// 	var o = {
// 		url:jsonURL
// 		,method:'post'
// 		,callback: function(options, success, response)	{

// 			if(true !== success){
// 				Ext.notification.msg(getText('Failed'),response.responseText);
// 				return;
// 			}try{
// 				var purchaseLogs = response.responseText;
// 				purchaseLogs = purchaseLogs.trim();
// 				win.document.write(purchaseLogs);
// 				win.document.close();
// 				win.print();
// 				return;
// 			}catch(e){
// 				var err = e.toString();
// 				Ext.notification.msg(getText('Error'), err);
// 				return;
// 			}
// 		}
// 		,scope:this
// 		,params:
// 		{
// 			cmd: 'getData',
// 			label:'getPurchaseLogs',
// 			active_module: SM.activeModule,
// 			log_ids:Ext.encode(records)
// 		}};
// 		Ext.Ajax.request(o);
// };


var getVariations = function (params,columnModel,store){

		params.file = jsonURL; // For wp-ajax

        if ( editorGrid.loadMask != undefined ) editorGrid.loadMask.show();
        var o = {
		// url: jsonURL,
		url: (ajaxurl.indexOf('?') !== -1) ? ajaxurl + '&action=sm_include_file' : ajaxurl + '?action=sm_include_file',
		method: 'post',
		callback: function (options, success, response) {
                editorGrid.loadMask.show();
			if (true !== success) {
				Ext.notification.msg('Failed',response.responseText);
				return;
			}

			try{
				if(typeof(response.responseText) != 'undefined'){
					var result = response.responseText;
					    result = result.trim();
					    result = SM.escapeCharacters(result);
					var myJsonObj = Ext.decode(result);

					var records_cnt = myJsonObj.totalCount;
					if (records_cnt == 0){
						myJsonObj.items = '';
					}
                                        store.loadData(myJsonObj);
					if(SM.incVariation == false){
						columnModel.setHidden(SM.typeColIndex,true);
					}else{
						columnModel.setHidden(SM.typeColIndex,false);
					}
				}
			} catch (e) {
				return;
			}
		},
		scope: this,
		params: params
	};
	Ext.Ajax.request(o);
}



// Function to add new Records
var addProduct  = function(store,cnt_array,cnt,newCatName){
	//	access the Record constructor through the grid's store
	var Product = editorGrid.getStore().recordType;	
	if (wooRunning == 1) {
		var jsString = "var p = new Product ({ id: '',"+
		SM.productsCols.name.colName +":'Enter Product Name',"+
		SM.productsCols.price.colName +":'',"+  
		SM.productsCols.salePrice.colName +":'',"+
		SM.productsCols.salePriceFrom.colName +":new Date(),"+
		SM.productsCols.salePriceTo.colName +":new Date(),"+
		SM.productsCols.inventory.colName +":'',"+
		SM.productsCols.sku.colName +":'',"+
		SM.productsCols.group.colName +":newCatName,"+
		SM.productsCols.group.colName +":'',"+
		SM.productsCols.weight.colName +":'',"+
		SM.productsCols.publish.colName +":'draft',"+
		SM.productsCols.desc.colName +":'',"+
		SM.productsCols.addDesc.colName +":'',"+
		SM.productsCols.height.colName +":'',"+
		SM.productsCols.width.colName +":'',"+ 
		SM.productsCols.lengthCol.colName +":'',"+
		SM.productsCols.post_parent.colName + ": '0',"+
		SM.productsCols.taxStatus.colName + ":'taxable',"+
        SM.productsCols.visibility.colName +":'visible',"+
		SM.productsCols.image.colName +":''})";
	} else if (wpscRunning == 1) {
		var jsString = "var p = new Product ({ id: '',"+
		SM.productsCols.name.colName +":'Enter Product Name',"+
		SM.productsCols.price.colName +":'',"+
		SM.productsCols.salePrice.colName +":'',"+
		SM.productsCols.inventory.colName +":'',"+
		SM.productsCols.sku.colName +":'',"+
		SM.productsCols.group.colName +":newCatName,"+
		SM.productsCols.group.colName +":'',"+
		SM.productsCols.weight.colName +":'',"+
		SM.productsCols.weightUnit.colName+":'pound',"+
		SM.productsCols.publish.colName +":'draft',"+
		SM.productsCols.disregardShipping.colName +":'0',"+ 
		SM.productsCols.desc.colName +":'',"+
		SM.productsCols.addDesc.colName +":'',"+
		SM.productsCols.pnp.colName +":'0.00',"+
		SM.productsCols.intPnp.colName +":'0.00',"+
		SM.productsCols.height.colName +":'',"+
		SM.productsCols.heightUnit.colName +":'in',"+
		SM.productsCols.width.colName +":'',"+ 
		SM.productsCols.widthUnit.colName +":'in',"+
		SM.productsCols.lengthCol.colName +":'',"+
		SM.productsCols.lengthUnit.colName +":'in',"+
		SM.productsCols.post_parent.colName + ": '0',"+
		SM.productsCols.image.colName +":''})";			
	}
	// since SM.productsCols was inaccessible in Product's new object so created a string consisting of 
	//	Product's new object structure and used eval function to evaluate the sting into js.

	eval(jsString);	
	
	editorGrid.stopEditing();
	store.insert(0, p);
	editorGrid.startEditing(0,0);
	cnt_array.push(++cnt);
	editorGrid.getSelectionModel().selectRows(cnt_array,true);
	return;
};

var showOrderDetails = function(record,rowIndex){
        var recordData   = editorGrid.getStore().getAt(rowIndex);  // Get the Record
//	var emailColName = editorGrid.getColumnModel().getDataIndex(3); // Get email field name
//	var emailId      = recordData.get(emailColName);
        var emailId = "";
	if (wooRunning == 1) {
            emailId      = recordData.get("_billing_email");
        }
        else if (wpscRunning == 1) {
            emailId      = recordData.get("billingemail");
        }
        
	// delay configure model
        batchUpdateWindow.loadMask.show();
	clearTimeout(SM.colModelTimeoutId);
	SM.colModelTimeoutId = showOrdersView.defer(100,this,[emailId]);
	SM.searchTextField.setValue(emailId);
}

//code for enabling multiple advanced search conditions


	count = 0;	
	var addAdvancedSearchCondition = function() { 
		jQuery(function($){

		if (wooRunning == 1) { 
			products_search_cols = products_search_cols;
		} else if (wpscRunning == 1) {
			products_search_cols = wpec_products_search_cols;
		}

		// old_count = count - 1;
		old_id_search_box = "sm_advanced_search_box_0";
		old_id_search_value = "sm_advanced_search_box_value_0";
		count++;
		new_id_search_box = "sm_advanced_search_box_" + count;
		new_id_search_value = "sm_advanced_search_box_value_" + count;


		$("#sm_advanced_search_box").append($("#" + old_id_search_box).clone().attr('id', new_id_search_box));
		$("#sm_advanced_search_box").append($("#" + old_id_search_value).clone().attr({'id': new_id_search_value, 'name': new_id_search_value}));

		

		$("#" + new_id_search_box).empty();
		$("#" + new_id_search_value).val('');

		window.visualSearch = new VisualSearch({
			el		: $("#"+new_id_search_box),
			placeholder: "Enter your search conditions here!",
			strict: false,
			search: function(json){
				$("#"+ new_id_search_value).val(json);
			},
			parameters: products_search_cols
		});
		});
	}

