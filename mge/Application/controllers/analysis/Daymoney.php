<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Daymoney extends CI_Controller{
	public function __construct(){
		parent::__construct();
	}
	
	
	public function index(){
		$this->data['date1'] = date('Y-m-d');
		$this->data['date2'] = date('Y-m-d', strtotime('-1 day'));
		$this->data['date3'] = date('Y-m-d', strtotime('-2 day'));
		$this->data['date4'] = date('Y-m-d', strtotime('-3 day'));
		
		$dateList = array($this->data['date1'], $this->data['date2'], $this->data['date3'], $this->data['date4']);
		$dataStr = $this->formatFilterDateNew($dateList);
		$this->data['morris_data'] = $this->getMorrisData($dataStr);
		
		$this->parser->parse('daymoney/index.html', $this->data);
	}
	
	public function echoMorrisDate(){
		$gid = isset($_REQUEST['gid']) ? $_REQUEST['gid'] : 44;
		$date1 = isset($_REQUEST['date1']) ? $_REQUEST['date1'] : '';
		$date2 = isset($_REQUEST['date2']) ? $_REQUEST['date2'] : '';
		$date3 = isset($_REQUEST['date3']) ? $_REQUEST['date3'] : '';
		$date4 = isset($_REQUEST['date4']) ? $_REQUEST['date4'] : '';
		
		$dateList = array($date1, $date2, $date3, $date4);
		$dataStr = $this->formatFilterDateNew($dateList);
		
		echo $this->getMorrisData($dataStr, $gid);
	}
	
	/**
	 * 获取报表数据
	 * @param string $dateStr
	 * @param number $gid
	 * @return string
	 */
	private function getMorrisData($dateStr= '', $gid = 44){
		$result = $this->dbapp->manage_sp_online_money_s_day($gid, $dateStr);
		
		$tempData = array();
		if(!empty($result)) {
			foreach ($result as $key => $val) {
			 	
				if(!isset($tempData[$val['post_date']])) {
					$tempData[$val['post_date']] = array();
				}
		
				$val['amount'] = $val['amount'] / 100;
				$tempData[$val['post_date']][] = $val;
			}
		}
		
		
		$result_data = array();
		$i = 0;
 		foreach ($tempData as $key=>&$val) {
 			foreach ($val as $k=>&$v) {
				if (!isset($v['amount'], $v['post_date'], $v['post_time'])) {
					continue;
				}
				
				$post_date_str = str_replace('-', '', $v['post_date']);
				$mount_str = $post_date_str.'mount';
				
				$v[$mount_str] = $v['amount'];
				$v['post_time'] = date('Y-m-d').' '.$v['post_time'];
				
				unset($v['post_date'], $v['amount']);
			}
			
			if ($i == 0) {
				$result_data = $val;
			}else{
				$result_data = $this->dataext->tableDataMerge('post_time', array('sort'=>'post_time asc','sortType'=>'date'), $result_data, $val);
			}
			$i++;
		}
		
		return json_encode($result_data, true);
	}
	
	/**
	 * 日期数组拼成字符串
	 * @param unknown $data
	 * @return Ambigous <string, unknown>
	 */
	private function formatFilterDateNew($data) {
		$date_1 = isset($data['0']) ? $data['0'] : '';
		$date_2 = isset($data['1']) ? $data['1'] : '';
		$date_3 = isset($data['2']) ? $data['2'] : '';
		$date_4 = isset($data['3']) ? $data['3'] : '';
	
		$date = [];
		if($date_1) $date[] = $date_1;
		if($date_2) $date[] = $date_2;
		if($date_3) $date[] = $date_3;
		if($date_4) $date[] = $date_4;
		if(empty($date)) $date[] = date('Y-m-d');
		if(count($date) > 1) {
			$date_str = "'";
			$date_str .= implode("','",$date);
			$date_str .= "'";
		} else {
			$date_str = $date[0];
		}
	
		return $date_str;
	}
	
}