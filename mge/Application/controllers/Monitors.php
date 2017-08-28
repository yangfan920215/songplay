<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2015-12-29
 */

class Monitors extends CI_Controller{
	public function __construct(){
		parent::__construct();
		
		$this->load->database();
	}
	
	
	public function index(){
		$this->data['sites'] = $this->config->item('site');
		
		// 重写开始时间和结束时间默认数据,因为该逻辑需要datetime而不是date格式的时间数据
		$this->data['sDate'] = $this->data['sDate'].' 00:00:00';
		$this->data['eDate'] = date('Y-m-d H:i:s');;
		
		$this->parser->parse('monitors.html', $this->data);
	}	
	
	
	public function ajaxData(){
		// 默认查询当天五天内数据
		$dname = isset($_REQUEST['extra_search'][0]['dname']) ? $_REQUEST['extra_search'][0]['dname'] : 44;
		$sDate = isset($_REQUEST['extra_search'][1]['sDate']) ? $_REQUEST['extra_search'][1]['sDate'] : date('Y-m-d', strtotime('-8 day'));
		$eDate = isset($_REQUEST['extra_search'][2]['eDate']) ? $_REQUEST['extra_search'][2]['eDate'] : date('Y-m-d', strtotime('-1 day'));
		
		$data = $this->getData($dname, $sDate, $eDate);
		
		echo json_encode(array('data'=>$data));
	}
	
	
	private function getData($dname, $sDate, $eDate){
		$param = array($dname, $sDate, $eDate);
		
		$data = exec_sp_sql('sp_log_s', $param, $this->db);
		
		return $data;
	}
}