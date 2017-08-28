<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-2-18
 */

class RoleManage extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		
		$this->load->database();
	}
	
	public function index(){
		$thList = array('选择', '角色名', '角色状态', '创建时间', '最后更新时间');
		
		$colList = array('name', 'status', 'create_time', 'update_time');

        $role_data = [];

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
														'type'=>'select',
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
								'thList'=>$thList,
								'colList'=>$colList,
						),
				)
		)->done();
/* 		
		$node_code = array();
		foreach ($_SESSION['node_auth'] as $key=>$value) {
			if ($value['controller'] == '') {
				continue;
			}
			$node_code[$key]['label'] = $value['name'];
			$node_code[$key]['value'] = $value['id'];
		}		
		// 重新排序
		$node_code = array_values($node_code);
		$node_code = json_encode($node_code, JSON_UNESCAPED_UNICODE);
		$node_code = str_replace('"', '\'', $node_code);
		
		$this->view->setTable($thList, $colList, array(
				'create'=>array('name'=>'创建角色'),
				'remove'=>array('name'=>'删除角色'),
				'fields'=>array(
					array('label'=>'角色名', 'name'=>'name'),
					array('label'=>'权限', 'name'=>'role', 'type'=>'checkbox', 'separator'=> "|", 'options'=>$node_code, ),
				),
		), false)->done(); */
	}
	
	public function ajaxTable(){
        echo json_encode(array('data'=>[]));
        exit;
		$role = exec_sp_sql('sp_get_system_role', array(), $this->db);
		convDT($role);
		echo json_encode(array('data'=>$role));
	}
	
	public function ajaxCurl(){
		$action = isset($_POST['action']) ? $_POST['action'] : exit('非正常访问');
		$data = isset($_POST['data']) ? $_POST['data'] : exit('数据异常');
		
		switch ($action) {
			case 'edit':
				$userid = key($data);
				$role_id = isset($data[$userid]['role']) ? $data[$userid]['role'] : exit('数据异常');
				
				$result = exec_sp_sql('sp_sys_reset_role', array($userid, $role_id), $this->db, false);
				
				if (!$result) {
					exit('更新失败,请刷新页面');
				}
				$userinfo = $this->getUserInfo($userid);
				echo json_encode(array('data'=>array($userinfo)));
			break;
			case 'create':
				if (is_array($data)) {
					$name = isset($data[0]['name']) ? $data[0]['name'] : exit('缺失参数name');
					$role = isset($data[0]['role']) ? $data[0]['role'] : exit('缺失参数role');
					$role = str_replace('|', ',', $role);
					$result = exec_sp_sql('sp_sys_add_role', array($name, $role), $this->db, false);
					if (!$result) {
						exit($role.'新增失败');
					}
					echo json_encode(array('data'=>array(array('name'=>$name, 'status'=>1, 'create_time'=>date('Y-m-d H:i:s'), 'update_time'=>date('Y-m-d H:i:s'),))));
				}
			break;
			case 'remove':
				foreach ($data as $key=>$value) {
					$result = exec_sp_sql('sp_sys_delete_role', array($key), $this->db, false);
					if (!$result) {
						exit($role.'新增失败');
					}
				}
				echo '{"data":[]}';
			break;
			default:
			break;
		}
	}
	
}
