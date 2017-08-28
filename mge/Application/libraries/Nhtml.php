<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-1-7
 */
class NHtml{
	/**
	 * excel导出功能辅助文件URL
	 */
	const EXCEL_URL = 'http://119.28.19.213:8004/copy_csv_xls_pdf.swf';
	
	/**
	 * 页面标识
	 */
	public $tab = '';
	
	/**
	 * 控制器名
	 */
	public $method = '';
	
	/**
	 * 搜索栏id
	 */
	public $rowSearchId = '';
	
	/**
	 * 主体行id
	 */
	public $rowBodyId = '';
	
	/**
	 * 主体内容
	 */
	public $body = '';
	
	/**
	 * 检索内容
	 */
	public $search = '';

	/**
	 * 游戏选择栏name属性
	 */
	public $domGidName = '';
	
	/**
	 * 资产分布页面所需下拉列表domid
	 */
	public $domUserLoginId = '';
	
	/**
	 * 表格动态更新数据的url
	 */
	public $ajaxTable = '';
	
	/**
	 * 饼状图重绘的url
	 */
	public $ajaxPidChart = '';
	
	/**
	 * 饼状图id
	 */
	public $domPidChartId = '';
	
	/**
	 * 表格id
	 */
	public $domTableId = '';
	
	/**
	 * 点击按钮会触发的函数
	 */
	public $jsMethod = '';
	
	/**
	 * 构造函数,初始化页面各Dom的id和name属性和生成部分URL
	 * @param array $param
	 */
	public function __construct(array $param){
		$this->method = isset($param['method']) ? $param['method'] : error_report(1, 'method');
		$action = isset($param['action']) ? $param['action'] : error_report(1, 'action');
		$tab = isset($param['tab']) ? $param['tab'] : '';

		$this->tab = $tab;
		
		$this->rowSearchId = $this->tab.'_row_search';
		$this->rowBodyId = $this->tab.'_row_body';
		
		$this->ajaxPidChart = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'/'.$this->method.'/ajaxPidChart';
	}

	/**
	 * 设置下拉游戏列表
	 */
	public function setSelectGid(){
		$this->domGidName = $this->tab.'_gid';
		$gameList = $GLOBALS['CFG']->config['gameList'];
		
		$this->search .= <<<endStr
		<div class="col-sm-2">
		<select name="$this->domGidName" class="form-control">
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
	 * 输出页面
	 */
	public function done(){
		echo <<<endStr
		
			<div class="tab-pane fade active in" id="$this->tab" style="margin-top: 10px;">
			<div class="row"  id="$this->rowSearchId">
			$this->search
			</div></br></br>
			<div class="row" id="$this->rowBodyId">
			$this->body
			</div>
		
endStr;
	}
	
	/**
	 * 设置资产分布页面所需的下拉列表
	 * @return NHtml
	 */
	public function setSelectUserLogin(){
		$this->domUserLoginId = $this->tab.'_user_login';
		
		$UserLoginConf = array('所有用户', '最近7天有登陆', '最近1个月有登陆', '最近3个月有登陆');
		$this->search .= <<<endStr
		
			<div class="col-sm-2">
			<select name="$this->domUserLoginId" class="form-control">
endStr;
		
		foreach ($UserLoginConf as $key=>$value) {
			$this->search .= '<option value="'.$key.'">'.$value.'</option>'."\n";
		}
$this->search .= <<<endStr
		
			</select>
			</div>
endStr;
	
		return $this;
	}
	
	/**
	 * 设置按钮,要在设置完表格或者报表后使用,不然无法触发动态更新数据
	 */
	public function setButton(){
		// javaScript要执行的函数
		//$onclickStr = implode(';', $this->jsMethod);
		
		$this->search .= <<<endStr
			<div class="col-sm-2">
				<button onclick="assetsDistribution_index_ajaxData()" type="button" class="btn btn-outline btn-default">查询</button>
			</div>
endStr;
		return $this;
	}
	
	/**
	 * 设置饼状图表
	 * @param array $data	数据
	 * @param unknown $count	所需绘制的饼状图表个数,用来确定样式
	 * @param string $title		该饼状图标题
	 * @param string $extid		图表id
	 * @return string|NHtml
	 */
	public function setPieChart(array $data, $count, $title = '', $extid = NULL){
		$this->domPidChartId = $this->tab.'_pid_chart';

		// 统计总额,实在没找到flot有什么适配总额的,自己算先
		$total = 0;
		foreach ($data as $value) {
			$total += isset($value['data']) ? $value['data'] : 0;
		}

		// 数据转化为饼状图所需格式
		$data = json_encode($data, JSON_UNESCAPED_UNICODE);
	
		// 根据所需绘制的饼状图个数设置图片宽度
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
	
		// 因为可能一个页面有很多张报表图,所以id需要互相识别
		if (isset($extid)) {
			$this->domPidChartId .= '_'.$extid;
		}
		
		$this->body .= <<<endStr
		
			<div id="" class="col-sm-$style">
				<div class="panel panel-default">
					<div class="panel-heading">
						$title
					</div>
				<div class="panel-body">
					<div class="flot-chart">
						<div class="flot-chart-content" id="$this->domPidChartId" style="padding: 0px; position: relative;">
						</div>
					</div>
				</div>
			</div>
								
			<script>
				$(function() {
					var data = $data;
					var plotObj = $.plot($("#$this->domPidChartId"), data, {
						series: {
							pie: {
								show: true
							}
						},
						grid: {
							hoverable: true
						},
						tooltip: true,
						tooltipOpts: {
							content: "%p.0%, %s",
							shifts: {
								x: 20,
								y: 0
							},
							defaultTheme: false
						}
					});
				});
			</script>
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
	public function setTable(array $thList, array $colList, $isCheck = FALSE){
		// 表格ID
		$this->domTableId = $this->tab.'_table';
		// URL
		$this->ajaxTable = 'http://'.$_SERVER['SERVER_NAME'].'/index.php/'.$this->method.'/ajaxTable';
		// 调用的javaScript
		$reloadJsName = $this->tab.'_ajaxData()';
		// 赋值给查询按钮触发
		$this->jsMethod[] = $reloadJsName;
	
		// 生成表头HTML代码
		$thStr = '';
		foreach ($thList as $value) {
			$thStr .= '<th>'.$value.'</th>';
		}
	
		// 生成列配置javaScript代码
		$colArr = array();
		foreach ($colList as $k=>$v) {
			$colArr[$k]['data'] = $v;
			$colArr[$k]['defaultContent'] = 'NULL';
		}
	
		$checkHtml = '';
		if ($isCheck) {
			$colArr = array_unshift($colArr, array('data'=>'NULL', 'defaultContent'=>'NULL', 'className'=>'select-checkbox'));
			$checkHtml = 'select:{style:"os",selector: "td:first-child"},';
		}
		
		$colStr = json_encode($colArr, TRUE);
	
		$this->body .= <<<endStr
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="dataTable_wrapper">
						<div class="dataTables_wrapper form-inline dt-bootstrap no-footer">
							<table id="$this->domTableId" class="display table table-striped table-bordered table-hover dataTable no-footer">
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
				var req = getReqJson('$this->rowSearchId');
				var $this->domTableId = $("#$this->domTableId").DataTable({
					"sDom": 'lfr<"a"T>tip',
					"oTableTools": {
						"sSwfPath": "http://119.28.19.213:8004/copy_csv_xls_pdf.swf",
						"aButtons": [
							{"sExtends":"xls","sButtonText":"数据导出"}
						]
					},
					"order":[[0, "asc"]],
					"displayLength": 25,
					ajax : {
						'url':"$this->ajaxTable",
						'type':'POST',
						'data':function(d){
							d.extra_search =[
								{
									'req':req
								}
							];
						},
					},
					columns:$colStr,
					$checkHtml
				});
				
				function $reloadJsName{
					$this->domTableId.ajax.reload();
				};
				</script>
endStr;
	
		return $this;
	}
}
