<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 
 * @author yangf@songplay.cn
 * @date 2016-4-20
 */

class AgentAnalyByHour extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		$this->parser->parse('AgentAnalyByHour.html', $this->data);
	}
	
	
	public function ajaxTable(){
		$date = get_extra_v1('sDate');
		$cid = get_extra_v1('cid') == '' ? 0 : get_extra_v1('cid');
		
		$data = $this->dbapp->manage_sp_report_hour_client($cid, $date);
		
		if ($cid == '-1') {
			if (!in_array($_SESSION['email'], $this->config->item('ADMIN'))) {
				echo json_encode(array('data'=>array()));
				exit;
			}
			
			$data_ret = array();
			for ($i = 0; $i < 24; $i++) {
				$data_ret[$i]['post_date'] = $date;
				$data_ret[$i]['post_time'] = $i < 10 ? '0' . $i . ':00:00' : $i . ':00:00';
			}
			
			$i = 0;
			foreach ($data as $key=>$value) {
				foreach ($data_ret as &$value1) {
					if ($value1['post_time'] == $value['post_time']) {
						$value1['charge'] = isset($value1['charge']) ? $value1['charge'] + $value['charge'] : $value['charge'];
						$value1['chargenum'] = isset($value1['chargenum']) ? $value1['chargenum'] + $value['chargenum'] : $value['chargenum'];
						$value1['newcagnum'] = isset($value1['newcagnum']) ? $value1['newcagnum'] + $value['newcagnum'] : $value['newcagnum'];
						$value1['play1'] = isset($value1['play1']) ? $value1['play1'] + $value['play1'] : $value['play1'];
						$value1['register'] = isset($value1['register']) ? $value1['register'] + $value['register'] : $value['register'];
					}
				};
			}
			
			if ($cid != $_SESSION['email'] && !in_array($_SESSION['email'], $this->config->item('ADMIN'))) {
				echo json_encode(array('data'=>array()));
				exit;
			}

			echo json_encode(array('data'=>$data_ret));
		}else{
			echo json_encode(array('data'=>$data));
		}
	}
}