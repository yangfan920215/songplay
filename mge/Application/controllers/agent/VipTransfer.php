<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-6-29
 */

class VipTransfer extends CI_Controller{
	
	private $ths = array(
		
	);
	
	private $cols = array(
			
	);
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		
		echo $this->views->setRow(
				array(
				array(
						'name'=>'agent_id',
						'desc'=>'渠道id',
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
							'type'=>'button',
							'col'=>3,
							'desc'=>'转移会员',
							'onclick'=>array(
									'type'=>'edit',
									'field'=>array(
											array(
												'name'=>'agentId',
												'desc'=>'新渠道ID',
											),
									),
							),
					),
				)
		)->setRow(
				array(
						array(
								'type'=>'table',
								'thList'=>array('选择', '渠道号', 'guid', '昵称', '绑定渠道时间', '最后登录时间'),
								'colList'=>array('client_id', 'guid', 'nickname', 'post_date_client', 'post_date'),
						),
				)
		)->done();
	}
	
	public function ajaxTable(){
		$agent_id = isset($_REQUEST['extra_search'][0]['req']['agent_id']) ? $_REQUEST['extra_search'][0]['req']['agent_id'] : execExit('渠道id不存在');
		
		if ($agent_id == '') {
			echo json_encode(array('data'=>array()));
			exit;
		}
		
		$data = $this->dbapp->manage_sp_report_clientUser_all($agent_id);
		
		echo json_encode(array('data'=>$data));
		exit;
	}
	
	public function edit(){
		if (!isset($_REQUEST['tData']) || $_REQUEST['tData'] < 1) {
			_exit('请选中一行数据!');
		}	
		
		$client = isset($_REQUEST['agentId']) && intval($_REQUEST['agentId']) > 0 ? intval($_REQUEST['agentId']) : _exit('请输入一个正常的渠道号!');
		
		// 验证渠道id是否存在
		$checkClient = $this->dbapp->manage_sp_web_clientidExist($client);
		
		if (!isset($checkClient['status']) || $checkClient['status'] != 0) {
			_exit('该渠道不存在!');
		}
		
		require $this->config->item('sys_libs_dir') . 'http.class.php';
		require $this->config->item('sys_libs_dir') . 'gasapp.class.php';
		
		$msg = '';
		$gasapp = gasapp::init();
		foreach ($_REQUEST['tData'] as $value) {
			if (!isset($value['uid']) || !isset($value['client_id'])) {
				continue;
			};
			
			$gasapp->setUid($value['uid']);
			$ret = $gasapp->moduserinfo(array('client_id'=>$client));
			
			if (!isset($ret['result']) || $ret['result'] != 0) {
				$msg .= $value['uid'] . '修改渠道号失败;';
			}else{
				$this->dbapp->manage_sp_winlist_new_i(217, $value['uid'], $value['client_id'], $client);
			}
		}
		
		if ($msg == '') {
			_exit('修改成功,大概1~20秒内生效!', 0);
		}
		
		_exit($msg);
	}
	
	
}