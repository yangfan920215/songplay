<?php
/**
 * @author yangf@songplay.cn
 * @date 2016-1-6
 */
class PropSale extends CI_Controller{
	public function __construct(){
		parent::__construct();
		
		$this->load->database('manage');
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
		$reqData = isset($_POST['reqData']) ? $_POST['reqData'] : execExit('reqData');
		$gid = isset($reqData['gList']) ? $reqData['gList'] : execExit('gList');
		$sDate = isset($reqData['dtp_0']) ? $reqData['dtp_0'] : execExit('propSale_index_dtp_0');
		$eDate = isset($reqData['dtp_1']) ? $reqData['dtp_1'] : execExit('propSale_index_dtp_1');
	
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
		
		if ($count != 0) {
			echo $obj->body;
		}
	}
	
	private function data($gid, $sDate, $eDate){
		$data = $this->dbapp->manage_sp_report_income_s_by_goods_id($gid, $sDate, $eDate);
		$totalIncome = $this->dbapp->manage_sp_report_charge_s(-1, $gid, -1, -1, $sDate, $eDate);
		
		$goods = $this->dbapp->config_sp_goods_s_simple($gid);
		foreach ($goods as $k=>$v) {
			$goods_info[$v['id']] = $v;
		}
		
		// 组装数据
		foreach ($data as &$v) {
			$v['goods_name'] = isset($goods_info[$v['goods_id']]['name']) ? $goods_info[$v['goods_id']]['name'] : null;
			$v['goods_price'] = isset($goods_info[$v['goods_id']]['price']) ? $goods_info[$v['goods_id']]['price'] : null;
		}
		
		$total = array();//总收入
		foreach ($totalIncome as $key => $value) {
			$total[$value['post_date']] = $value;
		}
		
		foreach ($data as $key => $val) {
			if(isset($total[$val['post_date']])) {
				$data[$key]['total'] = $total[$val['post_date']]['amounts'] / 100;
			}
			$data[$key]['amounts'] = $val['amounts'] / 100;
			$data[$key]['goods_price'] = $val['goods_price'] / 100;
			$data[$key]['ratio'] = $val['ratio'] * 100 . '%';
		}
		// 结果数据
		$result = array();
		foreach ($data as $key => $val) {
			$amount = isset($val['amounts']) ? $val['amounts'] * 100 : 0;
			$goods_name = isset($val['goods_name']) && isset($val['goods_price']) ? $val['goods_name'].'-￥'.$val['goods_price'] : '未知道具';
			$result[$val['post_date']][] = array('data'=>$amount, 'label'=>$goods_name);
		}
		
		return array_reverse($result);
	}
}