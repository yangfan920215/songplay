<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Html{
	
	/**
	 * 
	 */
	const EXCEL_URL = 'http://119.28.19.213:8004/copy_csv_xls_pdf.swf';
	
	/**
	 * 控制器名
	 */
	public $method = '';
	
	/**
	 * 页面唯一标签
	 */
	public $tab = '';
	
	/**
	 * 检索内容
	 */
	public $search = '';
	

	/**
	 * 时间框个数标识
	 */
	public $timei = 0;
	
	/**
	 * 主体内容
	 */
	public $body = '';
	
	/**
	 * 拓展内容,主要是一些全局js函数
	 * @var unknown
	 */
	public $ext = '';
	
	public $html = '';
	
	public $tableReqId = array();
	
	/**
	 * 构造函数,赋值tab
	 * @param unknown $param
	 */
	public function __construct($param){
		$this->method = isset($param['method']) ? $param['method'] : '';
		$action = isset($param['action']) ? $param['action'] : '';
		$tab = isset($param['tab']) ? $param['tab'] : '';
		
		if ($action == 'ajaxData') {
			$action = 'index';
		}
		
		$this->tab = $tab;
	}
	
	/**
	 * 输出页面
	 */
	public function done(){
		$this->html .= '<div class="tab-pane fade active in" id="' . $this->tab .'" style="margin-top: 10px;">'."\n";
		
		// 检索栏生成
		$this->html .= '<div class="row"  id="'.$this->tab.'_row_search">'."\n";
		$this->html .= $this->search."\n";
		$this->html .= '</div></br></br>'."\n";
		
		// 页面主体生成
		$this->html .= '<div class="row" id="'.$this->tab.'_row_body">'."\n";
		$this->html .= $this->body."\n";
		$this->html .= '</div>'."\n";
		
		$this->html .= $this->ext."\n";
		
		echo $this->html;
	}
	
	/**
	 * 设置下拉游戏列表
	 */
	public function setSelectGid(){
		$gameList = $GLOBALS['CFG']->config['gameListv1'];
		$this->search .= '<div class="col-sm-2">'."\n";
		$this->search .= '<select name="gList" id="'.$this->tab.'_gList" class="form-control">'."\n";
		foreach ($gameList as $k=>$v) {
			$id = isset($v['id']) ? $v['id'] : error_report(2);
			$name = isset($v['name']) ? $v['name'] : error_report(2);
			// 根据配置选择
			$this->search .= isset($v['check']) ? '<option selected="selected" value="'.$id.'">'.$name.'</option>'."\n" : '<option value="'.$id.'">'.$name.'</option>'."\n";
		}
		$this->search .= '</select>'."\n";
		$this->search .= '</div>'."\n";
		
		$this->tableReqId['gid'] = $this->tab.'_gList';
		return $this;
	}
	
	public function setSelectUserLogin(){
		$UserLoginConf = array('所有用户', '最近7天有登陆', '最近1个月有登陆', '最近3个月有登陆');
		
		$this->search .= '<div class="col-sm-2">'."\n";
		$this->search .= '<select name="'.$this->tab.'_userLogin" id="'.$this->tab.'_userLogin" class="form-control">'."\n";
		foreach ($UserLoginConf as $key=>$value) {
			$this->search .= '<option value="'.$key.'">'.$value.'</option>'."\n";
		}
		$this->search .= '</select>'."\n";
		$this->search .= '</div>'."\n";
		
		return $this;
	}
	
	/**
	 * 设置时间输入框
	 * @param number $timeInter
	 * @return Html
	 */
	public function setInputTime($timeInter = 1){
		$timei_str = (string)$this->timei;
	
		// 默认日期值设置
		$dateDef = $timeInter == 0 ? date('Y-m-d') : date('Y-m-d', strtotime('-'.$timeInter.' day'));
		$id = 'dtp_'.(string)$this->timei;
	
		$this->search .= '<div class="col-sm-2">'."\n";
		$this->search .= '<div class="input-group date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-field="'.$this->tab.'_dtp_'.$timei_str.'" data-link-format="yyyy-mm-dd">'."\n";
		$this->search .= '<input class="form-control" size="16" name="'.$id.'" id="'.$id.'" type="text" value="'.$dateDef.'">'."\n";
		$this->search .= '<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>'."\n";
		$this->search .= '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>'."\n";
		$this->search .= '<input type="hidden"   value="'.$dateDef.'" />'."\n";
		$this->search .= '</div>'."\n";
		$this->search .= '</div>'."\n";

		// 渲染脚本
		$this->search .= '<script type="text/javascript">'."\n";
		$this->search .= '$(\'.form_date\').datetimepicker({'."\n";
		$this->search .= 'language:  \'zh-CN\','."\n";
		$this->search .= 'weekStart: 1,'."\n";
		$this->search .= 'todayBtn:  1,'."\n";
		$this->search .= 'autoclose: 1,'."\n";
		$this->search .= 'todayHighlight: 1,'."\n";
		$this->search .= 'startView: 2,'."\n";
		$this->search .= 'minView: 2,'."\n";
		$this->search .= 'forceParse: 0'."\n";
		$this->search .= '});'."\n";
		$this->search .= '</script>'."\n";
		
		$this->timei++;
		$this->tableReqId[$id] = $id;
		
		return $this;
	}
	
	/**
	 * 设置按钮
	 */
	public function setButton(){
		$this->search .= '<div class="col-sm-2">'."\n";
		$this->search .= '<button onclick="'.$this->tab.'_ajaxData()" type="button" class="btn btn-outline btn-default">查询</button>'."\n";
		$this->search .= '</div>'."\n";
		return $this;
	}
	
	public function setPieChart($date, $data, $count){
		$total = 0;
		// 统计总额,实在没找到flot有什么适配总额的,自己算先
		foreach ($data as $value) {
			$total += isset($value['data']) ? $value['data']/100 : 0;
		}
		
		$data = json_encode($data, JSON_UNESCAPED_UNICODE);
		
		switch ($count) {
			case 0:
				return '';
			break;
			case 1:
				$style = '12';
			break;
			case 2:
				$style = '6';
			break;
			case 3:
				$style = '4';
			break;
			case 4:
				$style = '3';
			break;
			default:
				$style = '3';;
			break;
		}
		
		
		$this->body .= '<div class="col-sm-'.$style.'">'."\n";
		$this->body .= '<div class="panel panel-default">'."\n";
		$this->body .= '<div class="panel-heading">'."\n";
		$this->body .= $date.'——￥'.$total."\n";
		$this->body .= '</div>'."\n";
		
		$this->body .= '<div class="panel-body">'."\n";
		$this->body .= '<div class="flot-chart">'."\n";
		$this->body .= '<div class="flot-chart-content" id="flot-pie-chart'.$date.$this->tab.'" style="padding: 0px; position: relative;">'."\n";
		$this->body .= '</div>'."\n";
		$this->body .= '</div>'."\n";
		$this->body .= '</div>'."\n";
		$this->body .= '</div>'."\n";
		$this->body .= '</div>'."\n";
		
		$this->body .= '<script>'."\n";
		$this->body .= '$(function() {'."\n";
		
		$this->body .= 'var data ='.$data.';'."\n;";
		
		$this->body .= 'var plotObj = $.plot($("#flot-pie-chart'.$date.$this->tab.'"), data, {'."\n";
		$this->body .= 'series: {'."\n";
		$this->body .= 'pie: {'."\n";
		$this->body .= 'show: true'."\n";
		$this->body .= '}'."\n";
		$this->body .= '},'."\n";
		$this->body .= 'grid: {'."\n";
		$this->body .= 'hoverable: true'."\n";
		$this->body .= '},'."\n";
		$this->body .= 'tooltip: true,'."\n";
		$this->body .= 'tooltipOpts: {'."\n";
		$this->body .= 'content: "%p.0%, %s", // show percentages, rounding to 2 decimal places'."\n";
		$this->body .= 'shifts: {'."\n";
		$this->body .= 'x: 20,'."\n";
		$this->body .= 'y: 0'."\n";
		$this->body .= '},'."\n";
		$this->body .= 'defaultTheme: false'."\n";
		$this->body .= '}'."\n";
		$this->body .= '});'."\n";
		$this->body .= '});'."\n";

		// 更新数据源脚本
		$this->body .= '</script>'."\n";	
		
		return $this;
	}
	
	/**
	 * 清空body内部的HTML
	 */
/* 	public function emptyRow(){
		echo '<script>'."\n";
		echo '$("#'.$this->tab.'_row_body").empty();'."\n";
		echo '</script>'."\n";
		return $this;
	} */
	
	/**
	 * 动态更新pie图表
	 * @return Html
	 */
	public function ajaxPieChart(){
		$this->ext .= '<script>'."\n";
		$this->ext .= 'function '.$this->tab.'_ajaxData(){'."\n";
		$this->ext .= 'var reqData = getReqJson(\''.$this->tab.'_row_search'.'\');'."\n";
		$this->ext .= '$.ajax({'."\n";
		$this->ext .= 'type:"POST",'."\n";
		$this->ext .= 'url:"http://'.$_SERVER['SERVER_NAME'].'/index.php/analysis/'.$this->method.'/ajaxData",'."\n";
		$this->ext .= 'data:{"reqData":reqData},'."\n";
		$this->ext .= 'success:function(data){'."\n";
		$this->ext .= '$("#'.$this->tab.'_row_body").empty();'."\n";
		$this->ext .= '$("#'.$this->tab.'_row_body'.'").append(data);'."\n";
		$this->ext .= '}'."\n";
		$this->ext .= '});'."\n";
		$this->ext .= '}'."\n";
		$this->ext .= '</script>'."\n";
		return $this;
	}
	

	/**
	 * 设置表格
	 * @param unknown $thList
	 * @param unknown $colList
	 * @param string $isCheck
	 * @return Html
	 */
	public function setTable(array $thList, array $colList, $isCheck = FALSE){
		// 表格ID
		$tableId = $this->tab.'_table';
		
		$thStr = '';
		foreach ($thList as $value) {
			$thStr .= '<th>'.$value.'</th>';
		}
		
		// 列
		$colArr = array();
		foreach ($colList as $k=>$v) {
			$colArr[$k]['data'] = $v;
			$colArr[$k]['defaultContent'] = 'NULL';
		}
		
		
		if ($isCheck) {
			$colArr = array_unshift($colArr, array('data'=>'NULL', 'defaultContent'=>'NULL', 'className'=>'select-checkbox'));
		}
		$colStr = json_encode($colArr, TRUE);
		
		$this->body .= '<div class="panel panel-default">'."\n";
		$this->body .= '<div class="panel-body">'."\n";
		$this->body .= '<div class="dataTable_wrapper">'."\n";
		$this->body .= '<div class="dataTables_wrapper form-inline dt-bootstrap no-footer">'."\n";
		$this->body .= '<table id="'.$tableId.'" class="display table table-striped table-bordered table-hover dataTable no-footer">'."\n";
		$this->body .= '<thead>'."\n";
		$this->body .= '<tr>'."\n";
		$this->body .= $thStr."\n";
		$this->body .= '</tr>'."\n";
		$this->body .= '</thead>'."\n";
		$this->body .= '</table>'."\n";
		$this->body .= '</div>'."\n";
		$this->body .= '</div>'."\n";
		$this->body .= '</div>'."\n";
		$this->body .= '</div>'."\n";
		
		$this->body .= '<script>'."\n";
		$this->body .= 'var reqData = getReqJson(\''.$this->tab.'_row_search'.'\');'."\n";
		$this->body .= 'var '.$this->tab.'_table = $("#'.$tableId.'").DataTable({'."\n";
		$this->body .= '"sDom": \'lfr<"a"T>tip\','."\n";
		$this->body .= '"oTableTools": {'."\n";
		$this->body .= '"sSwfPath": "'.self::EXCEL_URL.'",'."\n";
		$this->body .= '"aButtons": ['."\n";
		$this->body .= '{"sExtends":"xls","sButtonText":"数据导出"}],'."\n";
		$this->body .= '},'."\n";
		$this->body .= '"order":[[0, "asc"]],'."\n";
		$this->body .= '"displayLength": 25,'."\n";
		$this->body .= 'ajax : {'."\n";
		$this->body .= '\'url\':"http://'.$_SERVER['SERVER_NAME'].'/index.php/analysis/'.$this->method.'/ajaxDataTable",'."\n";
		$this->body .= '\'type\':\'POST\','."\n";
		$this->body .= '\'data\':function(d){'."\n";
		$this->body .= 'd.extra_search =['."\n";
		$this->body .= '{\'reqData\':reqData}];},},'."\n";
		$this->body .= ''."\n";
		$this->body .= ''."\n";
		$this->body .= 'columns:'.$colStr.','."\n";
		$this->body .= $isCheck ? 'select:{style:"os",selector: "td:first-child"},'."\n" : '';
		$this->body .= '});';
		
		$this->body .= 'function '.$this->tab.'_ajaxData(){reqData = getReqJson(\''.$this->tab.'_row_search\');'."\n";
		$this->body .= 	$this->tab.'_table.ajax.reload();'."\n";
		$this->body .= '}'."\n";
		
		$this->body .= '</script>';
		return $this;
	}
	
/* 	
	$this->ext .= 'function '.$this->tab.'_ajaxData(){'."\n";
	$this->ext .= 'var reqData = getReqJson(\''.$this->tab.'_row_search'.'\');'."\n";
	$this->ext .= '$.ajax({'."\n";
	$this->ext .= 'type:"POST",'."\n";
	$this->ext .= 'url:"http://'.$_SERVER['SERVER_NAME'].'/index.php/'.$this->method.'/ajaxData",'."\n";
	$this->ext .= 'data:{"reqData":reqData},'."\n";
	$this->ext .= 'success:function(data){'."\n";
	$this->ext .= '$("#'.$this->tab.'_row_body").empty();'."\n";
	$this->ext .= '$("#'.$this->tab.'_row_body'.'").append(data);'."\n";
	$this->ext .= '}'."\n";
	$this->ext .= '});'."\n";
	$this->ext .= '}'."\n"; */
	
	
	/**
	 * 设置多选框
	 */
	public function setCheckBox($gidList){
		$this->headMain .= '<div class="col-sm-2">'."\n";
	
		$this->headMain .= '<div class="form-group">'."\n";
	
		foreach ($gidList as $v) {
			$id = isset($v['id']) ? $v['id'] : exit('config_app gameList error!');
			$name = isset($v['name']) ? $v['name'] : 'unname game';
				
			$this->headMain .= '<label class="checkbox-inline">'."\n";
			$this->headMain .= '<input type="checkbox" name="gidList" checked="checked" value="'.$id.'" show="'.$name.'">'.$name."\n";
			$this->headMain .= '</label>'."\n";
		}
		$this->headMain .= '</div>'."\n";
		$this->headMain .= '</div>'."\n";
		return $this;
	}
	
	
	/**
	 * 画area类型的报表图
	 * @param json $data
	 * @param unknown $ykeys
	 * @param unknown $labels
	 * @param string $xkey
	 * @return Html
	 */
	public function setAreaChart($data, $ykeys, $labels, $xkey = 'post_date'){
		$this->body .= '<div class="row">'."\n";
		$this->body .= '<div class="panel panel-default">'."\n";
		$this->body .= '<div class="panel-body">'."\n";
		$this->body .= '<div id="'.$this->tab.'_AreaChart"></div>'."\n";
		$this->body .= '</div>'."\n";
		$this->body .= '</div>'."\n";
		$this->body .= '</div>'."\n";
		// 设置脚本
		$this->body .= '<script>';
		$this->body .= 'var '.$this->tab.' = Morris.Area({'."\n";
		$this->body .= 'element: '.$this->tab.'_AreaChart,'."\n";
		$this->body .= 'data: '.$data.','."\n";
		$this->body .= 'xkey: "'.$xkey.'",'."\n";
		$this->body .= 'ykeys: '.$ykeys.','."\n";
		$this->body .= 'labels: '.$labels.','."\n";
		$this->body .= 'behaveLikeLine: true,'."\n";
		$this->body .= 'pointSize: 2,'."\n";
		$this->body .= 'hideHover: "auto",'."\n";
		$this->body .= 'resize: true'."\n";
		$this->body .= '});';
		$this->body .= '</script>';
		return $this;
	}
	
}
