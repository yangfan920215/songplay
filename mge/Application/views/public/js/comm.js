/**
 * 执行最简单的ajax请求
 * @param url
 * @param modelId
 */
function doAjax(url, modelId){
	var req = getReqJson(modelId);

    $.ajax({
    	url: url,
        type: 'POST',
        data: req,
        dataType:'json',
        success: function(ret){
        	status = ret.status;
        	data = ret.data;
        	
        	if (status == undefined || data == undefined) {
        		alert('服务器异常:返回了不正确的格式的数据');
        		return;
			}
        	
        	// 若函数存在则执行第三方函数
			alert(data);
        },
        error: function(ret){
        	alert('服务器异常:系统错误');
        	return;
        },
        
    });
}

function hasDom(id){
	obj = document.getElementById(id);
	return obj != null ? true : false;
}

function analy_data(json){
	var status = json.status;
	var data = json.data;
	if (typeof(status) == 'undefined' || data == 'undefined') {
		alert('未知异常,请通知超级管理员处理');
	}else if(status != 0) {
		alert(data);
	}else{
		if (typeof(data) == 'undefined') {
			return true;
		}
		return data;
	}
	return false;
}

function cascadeSelect(fatherId, childId, json){
	// 处理json数据		
	var json = JSON.parse(json);
	
	var fatherSel = $('#' + fatherId);
	var childSel = $('#' + childId);
	
	$i = 0;
	// 初始化
	$.each(json, function(key1, value1){
		fatherSel.append('<option value="' + value1['id']  + '">' + value1['name'] + '</option>');
		if ($i == 0) {
			$.each(value1['child'], function(key2, value2){
				childSel.append('<option value="' + key2  + '">' + value2 + '</option>');
			});
		}
		$i++;
	});
	
	
	
	fatherSel.bind('change', function(){
		
		var val = fatherSel.val();
		var child = [];
		
		$.each(json, function($key4, $value4){
			if (val == $value4['id']) {
				child = $value4['child'];
			}
		});
		
		childSel.empty();
		
		$.each(child, function(key3, value3){
			childSel.append('<option value="' + key3  + '">' + value3 + '</option>');
		});
	});
}

function addModel(modId, tableId, url){
	// 验证成功,发送数据
	if (getValidResult(modId)) {
		var result = '';
		req = getReqJson(modId);
		$.ajax({
			type:"POST",
			url:url,
			data:{"reqData":req},
			dataType:"json",
			async:false,
			cache : false,
			success:function(ret){
				$('#loading').hide();
		       	status = ret.status;
	        	data = ret.data;
	        	
	        	if (tableId != undefined) {
	        		table_reload(tableId);
				}
	        	
	        	if (status == undefined || data == undefined) {
	        		alert('服务器异常:返回了不正确的格式的数据');
	        		return;
				}
	        	
	        	alert(data);
			}
		});
	}
	
	return result;
}

/**
 * 发送一条ajax请求,并对返回值进行解析
 * @param url 访问url
 * @param ext 拓展参数
 */
function edit(url, modelId, tableId, sfunc){
	var pList = new Array();
	
	// 获取选中的数据
	var aData = eval(tableId).rows('.selected').data();
	
	if (aData.length == 0) {
		alert('请选中一行数据再提交!');
		return;
	}
	
	if (getValidResult(modelId)) {
		aData.each(function(key, value){
			isCheck = false;
			pList.push(key);
		});
		
		
		var req = getReqJson(modelId);

		req.tData = pList;
		
	    $.ajax({
	    	url: url,
	        type: 'POST',
	        data: req,
	        dataType:'json',
	        success: function(ret){
	        	status = ret.status;
	        	data = ret.data;
	        	
	        	if (tableId != undefined) {
	        		table_reload(tableId);
				}
	        	
	        	if (status == undefined || data == undefined) {
	        		alert('服务器异常:返回了不正确的格式的数据');
	        		return;
				}
	        	
	        	// 若函数存在则执行第三方函数
	        	if (sfunc != undefined && isExitsFunc(sfunc)) {
	        		window[sfunc](data);
				}else{
					alert(data);
				}
	        },
	        error: function(ret){
	        	alert('服务器异常:系统错误');
	        	return;
	        },
	        
	    });
	}
}

function delRow(url, tableId, sfunc){
	var req = new Object();
	var pList = new Array();
	
	if(confirm("确认处理该行数据?"))
	{
		// 获取选中的数据
		var aData = eval(tableId).rows('.selected').data();
		
		if (aData.length == 0) {
			alert('请选中一行数据再提交!');
			return;
		}
		
		aData.each(function(key, value){
			pList.push(key);
		});
		
		req.tData = pList;
		
		
		$.ajax({
	    	url: url,
	        type: 'POST',
	        data: req,
	        dataType:'json',
	        success: function(ret){
	        	status = ret.status;
	        	data = ret.data;
	        	
	        	table_reload(eval(tableId));
	        	
	        	if (status == undefined || data == undefined) {
	        		alert('服务器异常:返回了不正确的格式的数据');
	        		return;
				}
	        	
	        	// 若函数存在则执行第三方函数
	        	if (sfunc != undefined && isExitsFunc(sfunc)) {
	        		window[sfunc](data);
				}else{
					alert(data);
				}
	        },
	        error: function(ret){
	        	alert('服务器异常:系统错误');
	        	return;
	        },
	        
	    });
	}
	

}

/**
* 将某个div内全部input值拼成json字符串,返回返回
 * @param $divId
 * @returns
 */
function getReqJson(divId){
	
	if(divId == ''){
		return '';
	}
	
	var obj = new Object(); 
	
	// 普通输入框
	$('#' + divId + ' input[type=\'text\']').each(function(){
		var name = this.name;
		
		if ($('#' + divId + ' input[name=\'' +name + '\']').length > 1) {
			obj[name] = $('#' + divId + ' input[name=\'' +name + '\']').serializeArray();
		}else{
			var value = this.value;
			obj[name] = value;
		}	
	});
	
	$('#' + divId + ' input[type=\'password\']').each(function(){
		var name = this.name;
		var value = this.value;
		obj[name] = value;
	});
	
	$('#' + divId + ' input[type=\'number\']').each(function(){
		var name = this.name;
		var value = this.value;
		obj[name] = value;
	});
	
	// 下拉框
	$('#' + divId + ' select').each(function(){
		var name = this.name;
		if ($('#' + divId + ' select[name=\'' +name + '\']').length > 1) {
			obj[name] = $('#' + divId + ' select[name=\'' +name + '\']').serializeArray();
		}else{
			obj[$(this).attr('name')] = $(this).val();
		}
		
	});
	
	// checkbox
	$('#' + divId + ' input[type=\'checkbox\']:checked').each(function(){ 
		var name1 = this.name;
		var value1 = this.value;
		
		if(typeof(obj[name1]) == 'undefined'){
			obj[name1] = '';
		}
		
		obj[name1] += value1 + '_';
		
	}); 
	
	$('#' + divId + ' textarea').each(function(){
		obj[$(this).attr('name')] = $(this).val();
	});
	
	
	return obj;
}

/**
 * 表格数据重载
 * @param tableid
 */
function table_reload(tableid, modelId){
	req = getReqJson(modelId);
	eval(tableid).ajax.reload();
};


/**
 * 在下拉列表选中某个值(val)后,在该行后新增一行,并在其中放置一个input标签
 * @param name
 * @param desc
 * @param val
 * @param tab this
 */
function cInput(name, desc, val, tab){
	if ($(":input[name='" + name + "']").length == 0 && $(tab).val() == val) {
		$(tab).parent().parent().after('<div><br/><div class="row"><div class="col-sm-2"><input class="form-control" placeholder="' + desc + '" name="' + name + '" type="text"></div></div></div></div>');
	}else if($(":input[name='" + name + "']").length > 0){
		$(":input[name='" + name + "']").parent().parent().parent().remove();
	}
}


/**
 * 格式化表中表
 * @param d
 * @returns {String}
 */
function format (d, param) {
	var paramobj = JSON.parse(param);
	var thStr = '';
	var colStr = '';
	$.each(paramobj, function(key, val){
		thStr += '<th>' + val + '</th>';
		colStr += '<th>' + d[key] + '</th>';
	});
	
	var htm = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;"><thead><tr>' + thStr + '</tr></thead>';
		
	htm += '<tr>' + colStr + '</tr>';
	
	htm += '</table>';
    return htm;
}


function details(tableId, param){
	$('#' + tableId).on('click', 'td.details-control', function () {
	    var tr = $(this).closest('tr');
	    var row = eval(tableId).row( tr );
	    if ( row.child.isShown() ) {
	        // This row is already open - close it
	        row.child.hide();
	        tr.removeClass('shown');
	    }
	    else {
	        // Open this row
	        row.child(format(row.data(), param)).show();
	        tr.addClass('shown');
	    }
	} );
}

function details_v1(tableId, param){
	$('#' + tableId).on('click', 'td.details-control', function () {
	    var tr = $(this).closest('tr');
	    var row = eval(tableId).row( tr );
	    if ( row.child.isShown() ) {
	        // This row is already open - close it
	        row.child.hide();
	        tr.removeClass('shown');
	    }
	    else {
	        // Open this row
	        row.child(format_v1(row.data(), param)).show();
	        tr.addClass('shown');
	    }
	} );
}

function format_v1(d, param) {
	var paramobj = JSON.parse(param);
	
	var htm = '';
	$.each(paramobj, function(key, val){
		var tableStr = '';
		var thStr = '';
		var tdStr = '';
		
		tableStr = d[val];
		thStrArr = tableStr['head'];
		tdStrArr = tableStr['rows'];
		titleStr = tableStr['title'];
		
		
		$.each(thStrArr, function(key1, val1){
			thStr += '<th>' + val1 + '</th>';
		});
		
		
		var i = 0;
		$.each(tdStrArr, function(key2, val2){
			var tdStr1 = '';
			

			if(key2 == i){
				$.each(val2, function(key3, val3){
					tdStr1 += '<td>' + val3 + '</td>';
				});
				
			}else{
				tdStr1 += '<td></td>';
			}
			
			tdStr += '<tr>' + tdStr1 + '</tr>';
			
			i++;
		});
		
		htm += '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;"><caption>' + titleStr + '</caption><thead><tr>' + thStr + '</tr></thead>' + tdStr + '</table>';
	});
	
    return htm;
}

/**
 * 往对应的mod中插入被选中的表格数据(根据name相同自动插入)
 * @param model_id	模版ID
 * @param table_id	表格ID
 * @param noInsert	不需要插入的列key
 */
function edit_i_data(model_id, table_id, noInsert){
	var aData = eval(table_id).rows('.selected').data()[0];
	var noInsertArr = noInsert.split(',');
	
	if (aData != undefined) {
		$.each(aData, function(key1, value1){
			
			var i = 0;
			// 删除
			$.each(noInsertArr, function(key2, value2){
				if (value2 == key1) {
					i++;
				}
			});
			
			if (i == 0) {
				// 不存在相同name,则过滤
				if ($('#' + model_id).find('[name=' + key1 +']').length == 1) {
					$('#' + model_id).find('[name=' + key1 +']').val(value1);
					$('#' + model_id).find('[name=' + key1 +']').val(value1);
				}
				
				// 处理checkbox
				if ($('#' + model_id).find('[name=' + key1 +']').length > 1 && value1 != null) {
					var arr = value1.split(',')

					$('#' + model_id).find('[name=' + key1 +']').each(function(key2, val2){
						var val = $(val2).val();
						$(val2).attr("checked", false);
						$.each(arr, function(key3, val3){
							if (val3 == val) {
								$(val2).click();
							}
						});
					});
				}
			}
		});
	}else{
		$('#' + model_id).find('input[type!="checkbox"]').val('');
		$('#' + model_id).find('textarea').val('');
	}
	
	
	
	$('#' + model_id).bootstrapValidator('validate');
	
	var bootstrapValidator = $('#' + model_id).data('bootstrapValidator');
	
	bootstrapValidator.validate();
}

/**
 * 前端检测检查modId的值是否合法
 * @param modId
 * @returns
 */
function getValidResult(modId){
	$('#' + modId).bootstrapValidator('validate');
	
	var bootstrapValidator = $('#' + modId).data('bootstrapValidator');
	
	bootstrapValidator.validate();
	
	return bootstrapValidator.isValid();
}

