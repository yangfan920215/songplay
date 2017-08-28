<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-8-29
 */

class PropsBuyRecord extends CI_Controller{
	
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
								'name'=>'props_id',
								'desc'=>'道具id,多个请用英文“,”分割。不支持中文',
						),
						array(
								'name'=>'order_id',
								'desc'=>'订单ID',
						),
						array(
								'type'=>'sedate',
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
								'thList'=>array('所属游戏', '支付类型', '渠道id', '数量子渠道id', '商品名称', '价格（元）', '使用时间', '订单编号'),
								'colList'=>array('gname', 'pay_name', 'client_id', 'client_id_sub', 'pname', 'amounts', 'post_datetime', 'order_id'),
						),
				)
		)->done();
	}
	
	public function ajaxTable(){
		$userType = get_extra('userType');
		$key = get_extra('key');
		$props_id = get_extra('props_id');
		$order_id = get_extra('order_id');
		$sDate = get_extra('sDate');;
		$eDate = get_extra('eDate');
		
		require $this->config->item('sys_libs_dir') . 'http.class.php';
		require $this->config->item('sys_libs_dir') . 'gasapp.class.php';
		require $this->config->item('sys_libs_dir') . 'gameapp.class.php';
		
		$gasapp = gasapp::init();
		
		$uid = getUid($userType, $key, $gasapp, $this->config->item('userType'));

        $uid = $uid == '' ? 0 : $uid;
		$data = $this->dbapp->manage_sp_charge_s($uid, $order_id, $sDate, $eDate, $props_id, $_SESSION['authId']);
		
		convGname($data, $this->config->item('gameList'), 'game_id');
		
		$pay_id_conf = $this->dbapp->config_sp_type_pay_s_simple();
		$pay_conf = array();
		foreach ($pay_id_conf as $key1=>$value1) {
			if (isset($value1['id']) && isset($value1['name'])) {
				$pay_conf[$value1['id']] = $value1['name'];
			}
		}
		
		$play_id_conf = $this->dbapp->config_sp_play_s(-1, '');
		$play_conf = array();
		foreach ($play_id_conf as $key1=>$value1) {
			if (isset($value1['id']) && isset($value1['name'])) {
				$play_conf[$value1['id']] = $value1['name'];
			}
		}
		
		// 全部礼包
		$gifts = $this->dbapp->manage_sp_goods_s_all();
		
		convPname($data, $gifts, 'goods_id');
		
		foreach ($data as $key=>&$value) {
			$value['pay_name'] = '未知支付';
			if (isset($value['pay_id'])) {
				$value['pay_name'] = isset($pay_conf[$value['pay_id']]) ? $pay_conf[$value['pay_id']] : '未知支付(' .'payid=' . $value['pay_id'] . ')';
			}
			
			$value['play_name'] = '未知玩法';
			if (isset($value['play_id'])) {
				$value['play_name'] = isset($play_conf[$value['play_id']]) ? $play_conf[$value['play_id']] : '未知玩法(' .'play_id=' . $value['play_id'] . ')';
			}
			
			$value['amounts'] = isset($value['amounts']) ? intval($value['amounts'])/100 : null;
			
			$value['post_datetime'] = isset($value['post_date']) && isset($value['post_time']) ? $value['post_date'] . ' ' . $value['post_time'] : '未知时间';
		}
		
		echo json_encode(array('data'=>$data));
	}
}