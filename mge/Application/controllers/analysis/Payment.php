<?php
/**
 * @author yangf@songplay.cn
 * @date 2016-1-4
 */

class Payment extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		$this->html->setSelectGid()->setInputTime($this->config->item('dateInterval')['eDate'])->setInputTime($this->config->item('dateInterval')['sDate'])->setButton();
		
		$result = $this->data(44, $this->data['sDate'], $this->data['eDate']);
		$count = count($result);
		foreach ($result as $key=>$value) {
			$this->html->setPieChart($key, $value, $count);
		}
		$this->html->ajaxPieChart()->done();
	}
	
	public function ajaxData(){
		$reqData = isset($_POST['reqData']) ? json_decode($_POST['reqData'], true) : error_report(1, 'reqData');
		$gid = isset($reqData['payment_index_gList']) ? $reqData['payment_index_gList'] : error_report(1, 'payment_index_gList');
		$sDate = isset($reqData['payment_index_dtp_0']) ? $reqData['payment_index_dtp_0'] : error_report(1, 'payment_index_dtp_0');
		$eDate = isset($reqData['payment_index_dtp_1']) ? $reqData['payment_index_dtp_1'] : error_report(1, 'payment_index_dtp_1');
		
		// 若只传了一个日期
		if ($sDate == '') {
			$sDate = $eDate;
		}else if($eDate == ''){
			$eDate = $sDate;
		}
		
		$result = $this->data($gid, $sDate, $eDate);
		$count = count($result);
		foreach ($result as $key=>$value) {
			$obj = $this->html->setPieChart($key, $value, $count);
		}
		echo $obj->body;
	}
	
	private function data($gid, $sDate, $eDate){
		// 各支付方式收入
		$data = $this->dbapp->manage_sp_report_income_s_by_pay_id(-1, $gid, -1, $sDate, $eDate);
		
		//全部支付方式
		$pay = $this->dbapp->config_sp_type_pay_s();
		
		// 全部支付方式
		$typePay = array();
		foreach ($pay as $key => $val) {
			$typePay[$val['id']] = $val;
		}
		
		// 结果数据
		$result = array();
		foreach ($data as $key => $val) {
			$amount = isset($val['amounts']) ? $val['amounts'] : 0;
			$pay_name = isset($typePay[$val['pay_id']]) ? $typePay[$val['pay_id']]['name'] : '未知';
			$result[$val['post_date']][] = array('data'=>$amount, 'label'=>$pay_name);
		}
		
		return array_reverse($result);
	}
}