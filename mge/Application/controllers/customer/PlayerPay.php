<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-8-29
 */

class PlayerPay extends CI_Controller{
	
	private $payType = array(
		'-1'=>'所有状态',
		'0'=>'生成订单',
		'1'=>'获取签名信息成功',
		'2'=>'获取签名信息失败',
		'3'=>'确认购买',
		'4'=>'充值成功',
		'5'=>'充值失败',
		'6'=>'订单处理完成',
		'7'=>'订单处理失败',
	);
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
				echo $this->views->setRow(
				array(
						array(
								'type'=>'select',
								'name'=>'userType',
								'list'=>$this->config->item('userType1'),
						),
						array(
								'name'=>'key',
								'desc'=>'用户key'
						),
						array(
								'type'=>'select',
								'name'=>'gid',
								'list'=>$this->config->item('gameList'),
						),
						array(
								'type'=>'sedate',
						),
						array(
								'type'=>'select',
								'name'=>'payType',
								'list'=>$this->payType,
						),
				)
		)->setRow(
				array(
						array(
								'type'=>'button',
								'col'=>1,
								'onclick'=>array(
										'type'=>'reload',
								),
						),
				)
		)->setRow(
				array(
						array(
								'type'=>'table',
								'thList'=>array('时间', 'GUID','昵称', '订单金额(分)', '对应商品'),
								'colList'=>array('last_update_time', 'guid', 'nickname', 'amount', 'pname'),
						),
				)
		)->done();		
	}
	
	public function ajaxTable(){
		$userType = get_extra('userType');
		$key = get_extra('key');
		$gid = get_extra('gid');
		$payType = get_extra('payType');
		$cid = get_extra('agent_id');
		$sDate = get_extra('sDate');;
		$eDate = get_extra('eDate');
		
		require $this->config->item('sys_libs_dir') . 'http.class.php';
		require $this->config->item('sys_libs_dir') . 'gasapp.class.php';
		require $this->config->item('sys_libs_dir') . 'gameapp.class.php';
		
		$gasapp = gasapp::init();
		
		if ($key == '') {
			$uid = -1;
		}else{
			$uid = getUid($userType, $key, $gasapp, $this->config->item('userType'));
		}
		
		$data = $this->dbapp->manage_sp_pay_track_s($uid, $gid, '', '', $payType, $sDate,$eDate, '', $_SESSION['authId']);
		
		$pidList = $this->dbapp->manage_sp_goods_s_all();
		convPname($data, $pidList, 'goods_id');
		
		$pidList = $this->dbapp->config_sp_prop_s_all();
		convPname($data, $pidList, 'goods_id');
		
		$pay_id_conf = $this->dbapp->config_sp_type_pay_s_simple();
		
		$pay_conf = array();
		foreach ($pay_id_conf as $key1=>$value1) {
			if (isset($value1['id']) && isset($value1['name'])) {
				$pay_conf[$value1['id']] = $value1['name'];
			}
		}
		
		foreach ($data as $key=>&$value) {
			
			$value['_status'] = isset($value['status']) && isset($this->payType[$value['status']]) ? $this->payType[$value['status']] : '未知类型';
			
			$value['pay_name'] = '未知支付';
			if (isset($value['pay_id'])) {
				$value['pay_name'] = isset($pay_conf[$value['pay_id']]) ? $pay_conf[$value['pay_id']] : '未知支付(' .'payid=' . $value['pay_id'] . ')';
			}
		}
		
		echo json_encode(array('data'=>$data));
	}
}