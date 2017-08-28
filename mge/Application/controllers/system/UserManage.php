<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 用户管理
 * @author yangf@songplay.cn
 * @date 2016-2-15
 */

class UserManage extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		$role = $this->dbmge->sp_get_userRole($_SESSION[$this->config->item('USER_AUTH_KEY')]);
		
		// 提取所需的数据
		$role_data = array();
		foreach ($role as $key=>$val) {
			if (isset($val['id']) && isset($val['name'])) {
				$role_data[$val['id']] = $val['name'];
			}
		}
		
		echo $this->views->setRow(
				array(
						array(
								'type'=>'button',
								'desc'=>'新增',
								'col'=>1,
								'onclick'=>array(
										'type'=>'add',
										'field'=>array(
												array(
														'name'=>'acount',
														'desc'=>'账户',
												),
												array(
														'name'=>'password',
														'desc'=>'密码',
												),
												array(
														'name'=>'remark',
														'desc'=>'备注',
												),
												array(
														'desc'=>'角色：',
														'type'=>'checkbox',
														'name'=>'role_id',
														'list'=>$role_data,
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
						array(
								'type'=>'button',
								'desc'=>'编辑',
								'col'=>1,
								'onclick'=>array(
										'type'=>'edit',
										'field'=>array(
												array(
														'name'=>'remark',
														'desc'=>'备注',
												),
												array(
														'type'=>'checkbox',
														'name'=>'role_id',
														'list'=>$role_data,
												),
										),
								),
						),
				)
		)->setRow(
				array(
						array(
								'type'=>'table',
								'thList'=>array('选择', '账户名',  '密码', '角色', '账户创建时间','最后登录时间', '最后登录IP', '登录次数', '备注'),
								'colList'=>array('acount', 'password', 'name','create_time', 'last_login_time', 'last_login_ip','login_count', 'remark'),
						),
				)
		)->done();
	}
	
	public function ajaxTable(){
		$user = $this->dbmge->sp_get_system_user($_SESSION[$this->config->item('USER_AUTH_KEY')]);
		
		foreach ($user as &$value) {
			$value['DT_RowId'] = $value['id'];
		}
		
		echo json_encode(array('data'=>$user), JSON_UNESCAPED_UNICODE);
	}
	
	public function add(){
		$acount = reqData('acount', '不允许空的账户');
		$password = reqData('password', '不允许空的密码');
		$remark = reqData('remark');
		$role_id = reqData('role_id');
		
		$role_id = str_replace('_', ',', substr($role_id, 0, -1));
		
		$result = $this->dbmge->mge_sp_sys_register_user($acount, $password, $remark, $role_id);
		
		if (isset($result['status']) && $result['status'] != 0) {
			if (isset($result['msg'])) {
				_exit($result['msg']);
			}
			_exit('未知错误,用户新增失败');
		}
		
		_exit('新增用户成功', 0);
	}
	
	public function edit(){
		$uid = isset($_POST['tData'][0]['id']) ? $_POST['tData'][0]['id'] : execExit('请选中一行数据');
		$role_id = isset($_POST['role_id']) ? $_POST['role_id'] : execExit('请选择角色');
		
		$role_id = str_replace('_', ',', substr($role_id, 0, -1));
		
		$result = $this->dbmge->sp_sys_reset_role($uid, $role_id);
		
		if (!isset($result['status']) || $result['status'] != 0) {
			execExit('修改失败');
		}
		_exit('角色修改成功', 0);
	}
	
	public function delete(){
		$tData = isset($_POST['tData']) ? $_POST['tData'] : execExit('请选中一行数据');
		$msg = '';
		
		$msg = '删除成功';
		foreach ($tData as $key => $value) {
			if (isset($value['id'])) {
				$ret = $this->dbmge->mge_sp_sys_delete_user($value['id']);
				if (!isset($ret['status']) || $ret['status'] != 0) {
					$msg .= $value['acount'] . '删除失败';
				}
			};
		}
		execExit($msg, 0);
	}
	
}