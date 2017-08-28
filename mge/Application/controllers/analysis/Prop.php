<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prop extends CI_Controller{
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		// 道具列表
		$this->data['pList'] = $this->getPList();
		
		$colData = $this->getCol();
		
		$this->data['thStr'] = $colData['thStr'];
		$this->data['colObj'] = $colData['colObj'];
		
		$this->parser->parse('prop.html', $this->data);
	}
	
	/**
	 * 获取需要查询的道具
	 * @return unknown
	 */
	private function getPList(){
		$result = $this->dbmge->mge_sp_table_s('mge', 'config_prop_list');
		return $result;
	}
	
	/**
	 * 获取列名和列数据对象
	 * @return multitype:string
	 */
	private function getCol(){
		$result = $this->dbapp->manage_sp_type_config_s('mge', 'config_prop', 'sid', 1);
		$thStr = '<th>日期</th>';
		$colObj = array(array('data'=>'post_date', 'defaultContent'=>'NULL'));
		foreach ($result as $value) {
			if (isset($value['gname']) && isset($value['tid'])) {
				$thStr .= '<th>'.$value['gname'].'</th>';
				$colObj[] = array('data'=>'quantity_'.$value['tid'], 'defaultContent'=>0);
			}
		}
		// 其他列
/* 		$colObj[] = array('data'=>'quantity_100', 'defaultContent'=>0);
		$thStr .= '<th>其他</th>'; */
		// 转换为json字符串
		$colObj = json_encode($colObj, TRUE);
		
		return array('colObj'=>$colObj, 'thStr'=>$thStr);
	}
	
	public function ajaxData(){
		$pid = isset($_REQUEST['extra_search'][0]['pid']) ? $_REQUEST['extra_search'][0]['pid'] : 10006;
		$sDate = isset($_REQUEST['extra_search'][1]['sDate']) ? $_REQUEST['extra_search'][1]['sDate'] : date('Y-m-d', strtotime('-8 day'));
		$eDate = isset($_REQUEST['extra_search'][2]['eDate']) ? $_REQUEST['extra_search'][2]['eDate'] : date('Y-m-d', strtotime('-1 day'));

        $pid = $pid == '' ? 0 : '';
		$result = $this->dbapp->mge_sp_prop_date_s($pid, $sDate, $eDate);
		
		// 修改重复的字段名
		$result = $this->dataext->calData($result,array(),
				$cals = array(
						array('field'=>'type_id',
								'valType'=>'custom',
								'action' =>'$val[\'quantity_\'.$val[\'type_id\']] = $val[\'quantity\'];',
						))
		);
		
		// 删掉多余的字段
		$result = $this->dataext->calData($result,array('quantity'=>false, 'type_id' => false));
		
		//合并所有数据
		$result = $this->dataext->tableDataMerge('post_date',array('sort'=>'post_date desc','sortType'=>'date'), $result);
		
		echo json_encode(array('data'=>$result));
	}
}