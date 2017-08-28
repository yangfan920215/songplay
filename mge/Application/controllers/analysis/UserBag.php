<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-7-22
 */

class UserBag extends CI_Controller{
	
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
						'name'=>'pid',
						'desc'=>'道具id',
						
					),
					array(
						'name'=>'ext',
						'desc'=>'排名',
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
								'thList'=>array( '玩家uid', '玩家渠道', '道具名称', '道具数目', '玩家最近一次登录时间', '玩家最近7天玩牌时长'),
								'colList'=>array('uid', 'client_id', 'pname', 'quantity', 'post_date', 'playtime'),
						),
					)
			)->done();
	}
	
	public function ajaxTable(){
		$gid = get_extra('gid');
		$pid = get_extra('pid');
		$ext = get_extra('ext');
		
		$reslet = $this->dbapp->manage_sp_report_usersBag($gid, $pid, $ext);
		
		$pidList = $this->dbapp->config_sp_prop_s_all();
		convPname($reslet, $pidList, 'goods_id');
		
		echo json_encode(array('data'=>$reslet));
	}
	
}