<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-8-29
 */

class PropsUseRecord extends CI_Controller{
	
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
								'thList'=>array('所属游戏', '背包类型', '类别', '名称', '数量', '操作前数量', '操作后数量', '使用时间', '简要描述'),
								'colList'=>array('gname', 'bag_type_name', 'kind_name', 'pname', 'quantity', 'quantity1', 'quantity2', 'post_datetime', 'summary'),
						),
				)
		)->done();
	}
	
	public function ajaxTable(){
		$userType = get_extra('userType');
		$key = get_extra('key');
		$props_id = get_extra('props_id');
		$sDate = get_extra('sDate');;
		$eDate = get_extra('eDate');
		
		require $this->config->item('sys_libs_dir') . 'http.class.php';
		require $this->config->item('sys_libs_dir') . 'gasapp.class.php';
		require $this->config->item('sys_libs_dir') . 'gameapp.class.php';
		
		$gasapp = gasapp::init();
		
		$uid = getUid($userType, $key, $gasapp, $this->config->item('userType'));
		
		$data = $this->dbapp->manage_sp_bag_s_use($uid,$sDate,$eDate,$props_id, $_SESSION['authId']);

		if(isset($data[0]['status'])){
			echo json_encode(array('data'=>array()));
			exit;
		}

		convGname($data, $this->config->item('gameList'), 'game_id');
		
		$bag_type = $this->dbapp->manage_sp_type_bag_s();
		
		$bag_conf = array();
		foreach ($bag_type as $key=>$value) {
			if (isset($value['id']) && isset($value['name'])) {
				$bag_conf[$value['id']] = $value['name'];
			}
		}
		
		$ptype = $this->config->item('ptype');
		
		$p_conf = array();
		foreach ($ptype as $key=>$value) {
			if (isset($value['id']) && isset($value['name'])) {
				$p_conf[$value['id']] = $value['name'];
			}
		}
		
		// 全部商品
		$goods = $this->dbapp->manage_sp_goods_s_all();

		// 全部道具
		$props = $this->dbapp->config_sp_prop_s_all();
		
		// 全部礼包
		$gifts = $this->dbapp->config_sp_gifts_s('', -1, '');
		
		$pays = array_merge($goods, $props, $gifts);
		
		convPname($data, $pays, 'goods_id');
		
		foreach ($data as $key=>&$value) {
			$value['bag_type_name'] = isset($value['type_id']) && isset($bag_conf[$value['type_id']]) ? $bag_conf[$value['type_id']] : '未知类型';
			$value['post_datetime'] = isset($value['post_date']) && isset($value['post_time']) ? $value['post_date'] . ' ' . $value['post_time'] : '未知时间';
			
			
			if (isset($value['kind_id']) && isset($value['goods_id'])) {
				$value['kind_name'] = isset($p_conf[$value['kind_id']]) ? $p_conf[$value['kind_id']] : '未知类型';
				
/* 				switch ($value['kind_id']) {
					case 0:
						if ($value['goods_id'] == 0) {
							$value['pname'] = '金币0';
						}else{
							convPname($props, $pidList, 'goods_id');
						};
						break;
					case 1:
						
					break;
					
					case 2:
						
					break;
					default:
						;
						break;
				} */
			}

			
		}	
		
		echo json_encode(array('data'=>$data));
	}
}