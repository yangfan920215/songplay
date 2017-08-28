<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-4-27
 */

class AgentManage extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){		
		echo $this->views->setRow(
			array(
				array(
					'type'=>'button',
					'desc'=>'编辑',
					'col'=>1,
					'onclick'=>array(
						'type'=>'edit',
						'field'=>array(
							array(
								'name'=>'name',
								'desc'=>'公司名',
							),	
							array(
								'name'=>'remark',
								'desc'=>'姓名',
							),
							array(
								'name'=>'email',
								'desc'=>'邮箱',
							),
							array(
								'name'=>'intoration',
								'desc'=>'分成比例',
							),
						),
					),
				),
				array(
					'type'=>'button',
					'desc'=>'删除',
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
					'thList'=>array('选择', '公司名称', '渠道号', '角色类型', '联系人', '手机电话', '账户密码','分成比例', '备注', '创建时间'),
					'colList'=>array('name', 'clientid', 'level', 'remark', 'mobile', 'password','intoratio', 'remark', 'create_time'),
				),
		)
		)->done();
	}
	
	public function ajaxTable(){
		echo json_encode(array('data'=>$this->dbapp->manage_sp_web_report_company($_SESSION['authId'])));
	}
	
	public function edit(){
		$name = $this->datarece->post('name', true, '不允许空的公司名');
		$remark = $this->datarece->post('remark', true, '不允许空的姓名');
		$email = $this->datarece->post('name', true, '不允许空的邮箱');
		$intoration = $this->datarece->post('intoration', true, '不允许空的分成比例')/100;
		
		$client_id = isset($_POST['tData'][0]['clientid']) ? $_POST['tData'][0]['clientid'] : exit_msg('请选中一行数据');


		// 如果某个输入框为空
		if (!isset($name, $remark, $email, $intoration)) {
			execExit('不允许空的输入框');
		}


		$this->dbapp->manage_sp_web_company_u($client_id, $name, $remark, $email, $intoration);
		
		execExit('修改成功', 0);
	}
	
	public function delete(){
		$tData = isset($_POST['tData']) ? $_POST['tData'] : exit_msg('请选中一行数据');
		$msg = '';
		
		foreach ($tData as $key => $value) {
			if (isset($value['clientid'])) {
				$ret = $this->dbapp->mge_sp_sys_delete_client($value['clientid']);
				if (isset($ret['status']) && $ret['status'] == 0) {
					$ret = $this->dbapp->manage_sp_web_delete_client($value['clientid']);
					if (isset($ret['status']) && $ret['status'] == 0) {
						execExit('删除成功', 0);							
					}else{
						$msg .= $value['clientid'] . '用户删除失败';
					}
					
				}else{
					$msg .= $value['clientid'] . '渠道商删除失败,';
				}
				continue;
			};
		}
		execExit($msg);
	}
	
}