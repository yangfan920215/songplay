<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-6-29
 */

class WebLog extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		echo $this->views->setRow(
			array(
					array(
							'type'=>'select',
							'name'=>'gid',
							'list'=>$this->config->item('gameList'),
					),
					array(
							'type'=>'sedate',
					),
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
								'thList'=>array('uid', '发送者账户', '发送时间', '道具', '数目', '金币数'),
								'colList'=>array('user_key', 'acount', 'sendtime', 'prop_name', 'prop_num', 'gold'),
						),
				)
		)->done();
	}
	
	public function ajaxTable(){
		$gid = get_extra('gid');
		$sDate = get_extra('sDate') . ' 00:00:00';
		$eDate = get_extra('eDate') . ' 23:59:59';
		
		$list = $this->dbapp->mge_sp_web_log_s($gid, $sDate, $eDate);
	
		$prop = array();
		$res = $this->dbapp->config_sp_prop_s('', -1, '');
		if(isset($res) && count($res)) {
			foreach($res as $_res){
				$prop[$_res['id']] = $_res['name'];
			}
		}
		unset($res);
		//获取所有礼包
		$gifts = array();
		$res = $this->dbapp->config_sp_gifts_s('', -1, '');
		if(isset($res) && count($res)) {
			foreach($res as $_res){
				$gifts[$_res['id']] = $_res['name'];
			}
		}
		unset($res);
		//获取所有物品
		$goods = array();
		$res = $this->dbapp->config_sp_goods_s_simple(-1);
		if(isset($res) && count($res)) {
			foreach($res as $_res){
				$goods[$_res['id']] = $_res['name'];
			}
		}
		unset($res);
		if(isset($list) && count($list)) {
			foreach($list as $k=>$v) {
					if (isset($prop[$v['prop_id']])) {
						$list[$k]['prop_name'] = $prop[$v['prop_id']];
						continue;
					}
					if (isset($gifts[$v['prop_id']])) {
						$list[$k]['prop_name'] = $gifts[$v['prop_id']];
						continue;
					}
					if (isset($goods[$v['prop_id']])) {
						$list[$k]['prop_name'] = $goods[$v['prop_id']];
						continue;
					}
			}
		}
		
		echo json_encode(array('data'=>$list));
		exit;
	}
	
	
}