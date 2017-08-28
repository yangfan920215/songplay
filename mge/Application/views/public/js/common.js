/**
 * 在下拉列表选中某个值(val)后,在该行后新增一行,并在其中放置一个input标签
 * @param name
 * @param desc
 * @param val
 * @param tab this
 */
function cInput(name, desc, val, tab){
	if ($(":input[name='" + name + "']").length == 0 && $(tab).val() == val) {
		$(tab).parent().parent().after('<div><br/><div class="row"><div class="col-sm-3"><input class="form-control" placeholder="' + desc + '" name="' + name + '" type="text"></div></div></div></div>');
	}else if($(":input[name='" + name + "']").length > 0){
		$(":input[name='" + name + "']").parent().parent().parent().remove();
	}
}

/**
 * 判断一个函数是否存在
 * @param funcName
 * @returns {Boolean}
 */
function isExitsFunc(funcName) {
    try {
        if (typeof(eval(funcName)) == "function") {
            return true;
        }
    } catch(e) {}
    return false;
}

function table_reload(tableid){
	tableid.ajax.reload();
};

/**
 * 废弃函数
 * 执行ajax
 * @param url	访问url
 * @param tabID	表单数据的id
 * @param sfunc	ajax成功回调后要执行的函数
 */
function doAjax(url, data, sfunc){
	type = data.type == undefined ? 'POST' : data.type;
	dataType = data.dataType == undefined ? 'json' : data.dataType;
	data = data.data == undefined ? 'json' : data.data;
	
	// console.info(dataType == undefined);
    $.ajax({
    	url: url,
        type: type,
        data: data,
        dataType: dataType,
        success: function(ret){
        	console.info(ret);
        	status = ret.status;
        	data = ret.data;
        	
        	if (status == undefined || data == undefined) {
        		alert('服务器异常,ajax失败1');
        		return;
			}
        	
        	// 若函数存在则执行第三方函数
        	if (sfunc != undefined && isExitsFunc(sfunc)) {
        		window[sfunc](data);
			}
        },
        error: function(ret){
        	alert('服务器异常,ajax失败2');
        	return;
        },
        
    });
}


function addModel(modId, tableid, url){
	var result = '';
	req = getReqJson(modId);
	$.ajax({
		type:"POST",
		url:url,
		data:{"reqData":req},
		dataType:"json",
		async:false,
		cache : false,
		success:function(data){
			result = data;
		}
	});
	return result;
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
		var value = this.value;
		obj[name] = value;
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
		obj[$(this).attr('name')] = $(this).val();
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


function reloadPieChart(index){
	var req = getReqJson(index + '_row_search');
	
	var cName = index.indexOf('_') > 0 ? iName.replace('_', '/') : iName + '/index';
	
	$.ajax({
		type:"POST",
		url:"http://'.$_SERVER['SERVER_NAME'].'/index.php/'.$this->method.'/ajaxData",
		data:{"reqData":reqData},
		success:function(data){
			$("#'.$this->tab.'_row_body").empty();
			$("#'.$this->tab.'_row_body'.'").append(data);
		}
	});
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

