<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Onlinemoney extends CI_Controller{
	public function __construct(){
		parent::__construct();

		$this->load->helper(array('func'));
		$this->load->library('DataExt');
		// 链接数据库
		$this->load->database('db2');
	}
	
	
	public function index(){
		// 新增全部游戏选项
		// D($this->getMorrisData());
		$this->data['morris_data'] = $this->getMorrisData();
		
		$this->parser->parse('onlinemoney/index.html', $this->data);
	}
	
	public function getMorrisData($gidList = array(44, 100), $sDate = '', $eDate = ''){
		if (isset($_POST['gidList'])) {
			$gidList = $this->getGidList($_POST['gidList']);
		}
		
		$sDate = isset($_POST['sDate']) ? $_POST['sDate'] : date('Y-m-d', strtotime('-15 day'));;
		$eDate = isset($_POST['eDate']) ? $_POST['eDate'] : date('Y-m-d', strtotime('-1 day'));;
		
		$data = array();
		
		foreach ($gidList as $value) {
			$data[$value] = $this->dbapp->manage_sp_online_money_s_total($value, $sDate, $eDate);
		}
		
		// ykeys
		
		if(!empty($data)) {
			foreach ($data as $key => $val) {
				$data[$key] = $this->dataext->calData($val,array('amount'=>'game_'.$key),
						array(array('field'=>'game_'.$key,
								'valType'=>'number',
								'action'=>'/',
								'changeVal'=>100
						)));
				$actionStr[] = "\$val['game_".$key."']";
				$actionKey = 'game_'.$key;
			}
		}
		
		
		$actionStr = "@\$val['game_total'] = ".implode("+", $actionStr);
		// 合并数据
		$data = $this->dataext->tableDataMerge2('post_date',array('sort'=>'post_date desc',
				'calData'=> array(
						array(//'field'=>$actionKey,
								'valType'=>'custom',
								'action'=>$actionStr,
								'changeVal'=>100
						)
				)), $data);
		
		foreach ($data as $k=>&$v) {
			unset($v['currency_id'], $v['game_total']);
		}
		
		handleData(json_encode($data, true), 'gidList');
		return json_encode($data, true);
	}
	
	/**
	 * 获取gidList
	 * @param unknown $gidList
	 * @return multitype:
	 */
	private function getGidList($gidList){
		return explode('&', str_replace('gidList=', '', $gidList));
	}
	
}