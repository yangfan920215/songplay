<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 订单数据汇总
 * @author yangf@songplay.cn
 * @date 2015-12-16
 */


class ClientOrder extends CI_Controller{
	
	/**
	 * 初始化
	 */
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 入口
	 */
	public function index(){
		// 重写开始时间和结束时间默认数据,因为该逻辑需要datetime而不是date格式的时间数据
		$this->data['sDate'] = $this->data['sDate'];
		$this->data['eDate'] = date('Y-m-d');;
		
		$this->parser->parse('clientOrder.html', $this->data);
	}
	
	public function ajaxData(){
		$gid = isset($_REQUEST['extra_search'][0]['gid']) ? $_REQUEST['extra_search'][0]['gid'] : 44;
		$ptId = isset($_REQUEST['extra_search'][1]['ptId']) ? $_REQUEST['extra_search'][1]['ptId'] : -1;
		$sDate = isset($_REQUEST['extra_search'][2]['sDate']) ? $_REQUEST['extra_search'][2]['sDate'] : date('Y-m-d', strtotime('-8 day'));
		$eDate = isset($_REQUEST['extra_search'][3]['eDate']) ? $_REQUEST['extra_search'][3]['eDate'] : date('Y-m-d', strtotime('-1 day'));
		// 1查询成功订单,二查询失败订单
		$status = isset($_REQUEST['extra_search'][4]['status']) ? $_REQUEST['extra_search'][4]['status'] : 0;
		
		echo json_encode(array('data'=>$this->getdata($ptId, $gid, $sDate, $eDate, $status)));
	}
	
	private function getdata($ptId, $gid, $sDate, $eDate, $status){
		// 参数
		$param = array($ptId, $gid, $sDate, $eDate);
		
		switch ($status) {
			case 0:
				$charge = $this->dbapp->manage_sp_charge_check_s($ptId, $gid, $sDate, $eDate);
			break;
			case 1:
				$charge = $this->dbapp->manage_sp_report_pay_track0($ptId, $gid, $sDate, $eDate);
			break;
			default:
				exit;
			break;
		}
		// 道具id转换为道具名,第二个TRUE表示函数将返回数据库对象
		$pidList = $this->dbapp->manage_sp_goods_s_all();
		convPname($charge, $pidList, 'goods_id');
		
		return $charge;
	}
}