<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exchange extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		echo $this->views->setRow(
				array(
						array(
								'type'=>'button',
								'desc'=>'处理',
								'col'=>1,
								'onclick'=>array(
										'type'=>'delete',
								),
						),
				)
		)->setRow(
				array(
						array(
								'type'=>'table',
								'thList'=>array('选择', '游戏', '道具', '手机号码', '姓名', '身份信息', 'uid', '联系地址', '提交时间'),
								'colList'=>array('gname', 'pname', 'phone', 'name', 'identity_card', 'guid', 'adress', 'wtime'),
						),
				)
		)->done();		
	}
	
	/**
	 * 处理兑换记录
	 */
	public function delete(){
		// 删除操作
		foreach ($this->datarece->edit() as $val){
			$result = $this->dbapp->manage_sp_user_exchange_u($val);
		}
		
		echo execExit('处理成功');
	}
	
	public function ajaxTable(){
		$exchange = $this->dbapp->manage_sp_user_exchange_s();
		
		foreach ($exchange as &$value) {
			if (strlen($value['phone']) == 9) {
				$value['phone'] = '0' . $value['phone'];
			}
		}
		
		// 游戏id转换为游戏名
		convGname($exchange, $this->config->item('gameList'));
		// 数据id转换为渲染字段
		convDT($exchange);
		
		// 道具id转换为道具名,第二个TRUE表示函数将返回数据库对象
		$pidList = $this->dbapp->config_sp_prop_s_all();
		convPname($exchange, $pidList);
		
		
		echo json_encode(array('data'=>$exchange, 'options', 'files'));
	}
	
}