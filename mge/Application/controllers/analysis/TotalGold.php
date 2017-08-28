<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-5-31
 */

class TotalGold extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		$this->parser->parse('customer/totalGold.html', $this->data);
	}
	
	public function ajaxTable(){
		$gid = get_extra('gid');
		$sDate = get_extra('sDate');
		$eDate = get_extra('eDate');
		
		$data = $this->dbapp->manage_sp_report_clientGold($gid, $_SESSION[$this->config->item('USER_AUTH_KEY')], $sDate, $eDate);
		
		// 汇总数据
		$data_totle = array();
		
		foreach ($data as $key=>$value) {
			// 汇总金币
			if (empty($data_totle)) {
				$data_totle[0]['post_date'] = $value['post_date'];
				$data_totle[0]['client_id'] = '全部渠道';
				$data_totle[0]['quantity'] = $value['quantity'];
			}else{
				$has = true;
				
				foreach ($data_totle as $key3=>$value3) {
					if ($value3['post_date'] == $value['post_date']) {
						$data_totle[0]['quantity'] += $value['quantity'];
						$has = false;
						break;
					}
				}
				
				if ($has) {
					$count = count($data_totle);
					$data_totle[$count]['post_date'] = $value['post_date'];
					$data_totle[$count]['client_id'] = '全部渠道';
					$data_totle[$count]['quantity'] = $value['quantity'];
				}

			}
			
		}
		
		foreach ($data_totle as $value2) {
			$data[] = $value2;
		}
		
		echo json_encode(array('data'=>$data));
	}
}