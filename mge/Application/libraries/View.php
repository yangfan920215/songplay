<?php
/**
 * 前端渲染类
 * @author yangf@songplay.cn
 * @date 2016-1-13
 */

class View{
	
	/**
	 * 查询数据js函数名	
	 * @var unknown
	 */
	const SELECT_MEHTOD_NAME = 'ajaxData';
	
	/**
	 * 新增数据js函数名
	 * @var unknown
	 */
	const ADD_METHOD_NAME = 'addData';
	
	/**
	 * 删除数据js函数名
	 * @var unknown
	 */
	const DELETE_METHOD_NAME = 'deleteData';
	
	/**
	 * 修改数据js函数名
	 * @var unknown
	 */
	const ALERT_METHOD_NAME = 'alertData';
	
	/**
	 * 页面唯一标签
	 * @var unknown
	 */
	public $tab = '';
	
	/**
	 * 总html代码
	 * @var unknown
	 */
	public $html = '';
	
	/**
	 * 搜索行html
	 * @var unknown
	 */
	public $search = '';
	
	/**
	 * 搜索行id
	 * @var unknown
	 */
	public $search_row_id = '';
	
	/**
	 * 表格行html
	 * @var unknown
	 */
	public $table = '';
	
	/**
	 * 报表行html
	 * @var unknown
	 */
	public $report = '';
	
	/**
	 * 其他行html
	 * @var unknown
	 */
	public $row = '';
	
	/**
	 * CI对象
	 * @var unknown
	 */
	protected $CI;
	
	/**
	 * 表格动态更新数据url
	 * @var unknown
	 */	
	protected $ajax_data_url = '';

	/**
	 * action名
	 * @var unknown
	 */
	protected $aName = '';
	
	/**
	 * method名
	 * @var unknown
	 */
	protected $mName = '';
	
	
	protected $div_id = '';
	
	/**
	 * 表格数据编辑url
	 * @var unknown
	 */
	protected $ajax_curl_url = '';
	
	public function __construct($param){
		$this->CI =& get_instance();
		
		// 节点id
		$this->tab = 'node_'.$param[0];
		
		// 控制器名称
		$this->aName = $param[1]; 
		
		// 目录名称
		$this->mName = $param[2];
		
		$this->div_id = $this->aName.'_'.$this->mName;
		
		$this->ajax_data_url = 'http://'.$_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"].'/index.php/'.$this->aName.'/ajaxData';
		
		$this->ajax_curl_url = 'http://'.$_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"].'/index.php/'.$this->aName.'/ajaxCurl';
		
		$this->js_reload = 'ajax_data_'.$this->tab;
	}
	
	
	/**
	 * 设置搜索行
	 * @param array $part
	 */
	public function setSearch(array $part){
		// 搜索行id
		$this->search_row_id = 'search_row'.$this->tab;
		
		// 设置搜索行内容
		foreach ($part as $value) {
			if (method_exists($this, $value)) {
				$this->value.'()';
			};
		}
		
		$this->html .= <<<search
		<div id="$this->search_row_id" class="row">
			$this->search
		</div>
search;
	}
	
	/**
	 * 设置游戏id
	 * @return View
	 */
	private function setGidList(){
		$gId = 'gid_'.$this->tab;
		$gameList = $this->CI->config->items('gameList');
		
		$this->search .= <<<endStr
		<div class="col-sm-2">
		<select name="$gId" class="form-control">
endStr;

		foreach ($gameList as $k=>$v) {
			$id = isset($v['id']) ? $v['id'] : error_report(2, 'id');
			$name = isset($v['name']) ? $v['name'] : error_report(2, 'name');
			// 根据配置选择
			$this->search .= isset($v['check']) ? '<option selected="selected" value="'.$id.'">'.$name.'</option>'."\n" : '<option value="'.$id.'">'.$name.'</option>'."\n";
		}
		$this->search .= <<<endStr
		</select>
		</div>
endStr;
		return $this;
	}
	
	/**
	 * 设置表格
	 * @param unknown $thList
	 * @param unknown $colList
	 * @param string $isCheck 表格行是否可以被选中
	 * @return Html
	 */
	public function setTable(array $thList, array $colList, array $curl = array(), $is_export = true){
		// 表格ID
		$tableId = 'table_'.$this->tab;

		// 生成表头HTML代码
		$thStr = '';
		foreach ($thList as $value) {
			$thStr .= '<th>'.$value.'</th>';
		}
	
		// 生成列配置javaScript代码
		$colArr = array();
		$i = 0;
		foreach ($colList as $k=>$v) {
			// 拓展配置
			if (is_array($v)) {
				foreach ($v as $key=>$val) {
					$colArr[$i][$key] = $val;
				};
				$colArr[$i]['data'] = $k;
			}else{
				$colArr[$i]['data'] = $v;
			}
			
			$colArr[$i]['defaultContent'] = 'NULL';
			$i++;
		}
		
		// 如果第一列是选择列
		if ($thList[0] == '选择') {
			array_unshift($colArr, array('data'=>'NULL', 'defaultContent'=>'', 'className'=>'select-checkbox'));;
		}
		
		$colStr = json_encode($colArr, JSON_UNESCAPED_UNICODE);
		
		// 表格是否需要导出功能
		$exportHtml = '';
		if ($is_export) {
			$exportHtml = <<<str
			"oTableTools": {
				"sSwfPath": "http://119.28.19.213:8004/copy_csv_xls_pdf.swf",
				"aButtons": [{
					"sExtends":"xls","sButtonText":"数据导出"
				}]
			},
str;
			
		}
		
		
		$checkHtml = '';
		$buttons = '';
		$checkJs = '';
		// 表格是否需要编辑功能
		if (!empty($curl)) {
			$i18n = '';
			$cButton = '';
			$rButton = '';
			$eButton = '';
			// 获取按钮的部分信息
			foreach ($curl as $key=>$value) {
				switch ($key) {
					case 'create':
						$cname = isset($value['name']) ? $value['name'] : '创建';
						$i18n .= 'create:{button:"'.$cname.'",title:"'.$cname.'",submit: "确认?",},';
						$cButton = '{ extend: "create", editor: editor },';
					break;
					case 'remove':
						$cname = isset($value['name']) ? $value['name'] : '删除';
						$i18n .= 'remove:{button:"'.$cname.'",title:"'.$cname.'",submit: "确认?",confirm: {_: "确认删除?",1: "确认删除?",}},';
						$rButton = '{ extend: "remove", editor: editor }';
					break;
					case 'edit':
						$cname = isset($value['name']) ? $value['name'] : '编辑';;
						$i18n .= 'edit:{button:"'.$cname.'",title:"'.$cname.'",submit: "确认?",confirm: {_: "确认删除?",1: "确认删除?",}},';
						$eButton = '{ extend: "edit",   editor: editor },';
					case 'fields':
						$fields = json_encode($value, JSON_UNESCAPED_UNICODE);
						$fields = str_replace('"[{', '[{', $fields);
						$fields = str_replace('}]"', '}]', $fields);
						$fields = str_replace('"', '\'', $fields);
						//D($fields);
						//$fields = '[{"label":"角色名","name":"name"},{"label":"权限","name":"role","type":"checkbox","separator":"|","options":[{ label: "参数一", value: 1 },{ label: "参数一", value: 1 }]}]';
						//D($fields);
					break;
				};
			}
			
			
			$checkHtml = 'select:{style:"os",selector: "td:first-child"},';
			$checkJs = <<<checkJs
			editor = new $.fn.dataTable.Editor( {
				ajax: "$this->ajax_curl_url",
		        table: "#$tableId",
		        i18n: {
	        		$i18n
		 	        error:{
		 	        	system: "处理失败,请联系程序员哥哥!"
		 	        }
				},
			    fields: $fields
	    		});
checkJs;
		$buttons = <<<str
	        buttons: [
	            $cButton
	            $rButton
	            $eButton
	     	],
str;
			
		}
		
		$this->html .= <<<endStr
			<div class="row">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="dataTable_wrapper">
						<div class="dataTables_wrapper form-inline dt-bootstrap no-footer">
							<table id="$tableId" class="display table table-striped table-bordered table-hover dataTable no-footer">
								<thead>
									<tr>
										$thStr
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		
			<script>
				var req = getReqJson('$this->search_row_id');

				var editor;
				
				$checkJs
				
				$('#$tableId').on( 'click', 'tbody td.editable', function (e) {
        			editor.inline( this, {
            			onBlur: 'submit'
       				});
    			});
				
				var $tableId = $("#$tableId").DataTable({
					"dom": 'Bfrtip',
					$exportHtml
					"order":[[0, "asc"]],
					"displayLength": 25,
					ajax : {
						'url':"$this->ajax_data_url",
						'type':'POST',
						'data':function(d){
							d.extra_search =[
								{
									'req':req
								}
							];
						},
					},
					"language": {
		                "lengthMenu": "每页 _MENU_ 条记录",
		                "zeroRecords": "没有找到记录",
		                "info": "第 _PAGE_ 页 ( 总共 _PAGES_ 页 )",
		                "infoEmpty": "无记录",
		                "infoFiltered": "(从 _MAX_ 条记录过滤)",
          		    },
					columns:$colStr,
					$checkHtml
					$buttons
				});
				
				
				function $this->js_reload(){
					$tableId.ajax.reload();
				};
				</script>
				</div>
endStr;
	
		return $this;
	}
	
	public function done(){
		echo <<<html
	<div class="tab-pane fade active in" id="$this->div_id">
		$this->html
	<div>	
html;
		
	}
}