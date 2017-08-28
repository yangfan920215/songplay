<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GoldRecovery extends CI_Controller{
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		$query1 = $this->dbapp->manage_sp_gold_typpe_config_s(2);
		
		// 新增日期列和其他列
		array_unshift($query1, array('gname'=>'日期'));
		array_push($query1, array('gname'=>'总消耗', 'tid'=>'total'));
		
		// 获取渲染字符串和对象
		$this->data['thList'] = $this->getThObj($query1);
		$this->data['colObj'] = $this->getColObj($query1);
		
		$this->parser->parse('gold/recovery.html', $this->data);
	}
	
	/**
	 * 获取th字符串
	 * @param unknown $data
	 * @return string
	 */
	private function getThObj($data){
		$thStr = '';
		foreach ($data as $value) {
			$gname = isset($value['gname']) ? $value['gname'] : '未定义列';
			$thStr .= '<th>'.$gname.'</th>';
		}
		return $thStr;
	}
	
	/**
	 * 获取Col数据对象
	 * @param unknown $data
	 * @return string
	 */
	private function getColObj($data){
		$colObj = array();
		foreach ($data as $k=>$value) {
			$tid = isset($value['tid']) ? $value['tid'] : 'post_date';
			$colObj[$k]['data'] = $tid != 'post_date' ? 'quantity_'.$tid : $tid;
			$colObj[$k]['defaultContent'] = 0;
		}
		return json_encode($colObj, True);
	}
	
	/**
	 * 表格ajax数据源
	 */
	public function ajaxGetData(){
		$gid = isset($_REQUEST['extra_search'][0]['gameId']) ? $_REQUEST['extra_search'][0]['gameId'] : 44;
		$sDate = isset($_REQUEST['extra_search'][1]['sDate']) ? $_REQUEST['extra_search'][1]['sDate'] : date('Y-m-d', strtotime('-8 day'));
		$eDate = isset($_REQUEST['extra_search'][2]['eDate']) ? $_REQUEST['extra_search'][2]['eDate'] : date('Y-m-d', strtotime('-1 day'));
		$type = isset($_REQUEST['extra_search'][3]['type']) ? $_REQUEST['extra_search'][3]['type'] : -1;
		
		// echo json_encode($this->getData($type, $sDate, $eDate, $gid), TRUE);
		
		switch ($type) {
			// 金币发放
			case 1:
				$sendData = $this->getData($type, $sDate, $eDate, $gid);
				
				$this->getTotalData($sendData, 'quantity_total');
				
				$elseData = $this->getData(0, $sDate, $eDate, $gid);
				// 其他列的标记为quantity_10000
				$this->getTotalData($elseData, 'quantity_10000', TRUE);
				
				$result = $this->dataext->tableDataMerge('post_date',array('sort'=>'post_date desc','sortType'=>'date'), $sendData, $elseData);
			break;
			case 2:
				$result = $this->getData($type, $sDate, $eDate, $gid);
				$this->getTotalData($result, 'quantity_total');
			break;
			default:
				;
			break;
		}
		// 格式化数据
		foreach ($result as $key=>&$value) {
			foreach ($value as $k=>&$v) {
				if ($k == 'post_date') {
					continue;
				};
				$v = number_format($v);
			}
		}
		echo json_encode(array('data'=>$result));
	}
	
	/**
	 * 调用存储过程获取数据
	 * @param string $sDate
	 * @param string $eDate
	 * @param number $gid
	 * @return unknown
	 */
	private function getData($type = -1, $sDate = '', $eDate = '', $gid = 44){
		$sDate = $sDate == '' ?  date('Y-m-d', strtotime('-8 day')) : $sDate;
		$eDate = $eDate == '' ?  date('Y-m-d', strtotime('-1 day')) : $eDate;
		
		// 存储过程参数,第二个参数-1指的是获取全部流水类型
		$param = array($gid, $type, $sDate, $eDate);
		
		$result = $this->dbapp->manage_sp_report_output_goods_sn($gid, $type, $sDate, $eDate);
		
		// 修改重复的字段名
		$result = $this->dataext->calData($result,array(),
				$cals = array(
						array('field'=>'out_type',
								'valType'=>'custom',
								'action' =>'$val[\'quantity_\'.$val[\'out_type\']] = $val[\'quantity\'];',
				))
		);
		
		// 删掉多余的字段
		$result = $this->dataext->calData($result,array('quantity'=>false, 'out_type' => false));
		
		//合并所有数据
		$result = $this->dataext->tableDataMerge('post_date',array('sort'=>'post_date desc','sortType'=>'date'), $result);

		// 台费展现为负数
		foreach ($result as &$value) {
			if (isset($value['quantity_99'])) {
				$value['quantity_99'] = '-'.$value['quantity_99'];
			}
		}
		
		// $this->getElseData($result, $param);
		
		return $result;
	}
	
	private function getTf(){
		
	}
	
	/**
	 * 获取总数,第二列为新总数列的列名,第三名表示是否删除其他列
	 * @param unknown $data
	 * @param string $colName
	 * @param string $isDel
	 */
	private function getTotalData(&$data, $colName = '', $isDel = FALSE){
		foreach ($data as $key=>&$value) {
			$data[$key][$colName] = 0;
			
			foreach ($value as $k=>&$v) {
				if ($k == 'post_date' || $k == $colName || $k == 'quantity_85') {
					continue;
				};
				$data[$key][$colName] += $v;
				
				if ($isDel) {
					unset($data[$key][$k]);
				}
			}
		}
	}
}