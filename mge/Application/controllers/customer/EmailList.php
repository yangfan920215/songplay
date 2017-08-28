<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class emailList extends CI_Controller{
	
	private $emailState = array(
		0=>'领取删除',
		1=>'过期删除',
		2=>'未领取',	
	);
	
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
/* 						array(
								'type'=>'select',
								'name'=>'state',
								'list'=>array(
								
								),
						), */
						array(
							'name'=>'uid',
							'desc'=>'用户uid',
								
						),
	
						array(
								'type'=>'button',
								'col'=>1,
								'onclick'=>array(
										'type'=>'reload',
								),
						),

						array(
								
								'type'=>'button',
								'col'=>1,
								'desc'=>'删除',
								'onclick'=>array(
									'type'=>'delete',
								),
						),
				)
		)->setRow(
				array(
						array(
								'type'=>'table',
								'thList'=>array('选择', '用户ID','日期', '邮件内容', '携带礼包id', '携带礼包名字',),
								'colList'=>array('uid', '_Date','Msg', 'GoodsID', 'pname',),
						),
				)
		)->done();
	}
	
	public function ajaxTable(){
		$gid = get_extra('gid');
		$uid = get_extra('uid');
		
		if ($uid == '') {
			echo json_encode(array('data'=>array()));
			exit;
		}
		
		require $this->config->item('sys_libs_dir') . 'http.class.php';
		require $this->config->item('sys_libs_dir') . 'gameapp.class.php';
		
		$data = gameapp::init($uid)->queryUserMail();
		
		if (!isset($data['mails']) || $data['mails'] == '') {
			echo json_encode(array('data'=>array()));
			exit;
		}

		$email = json_decode(urldecode($data['mails']), true);
		
/* 		$email = $this->dbapp->manage_sp_report_email($uid, $gid); */
		
		// 道具id转换为道具名,第二个TRUE表示函数将返回数据库对象
		$pidList = $this->dbapp->config_sp_gifts_s('', -1, '');

		convPname($email, $pidList, 'GoodsID');
		
		foreach ($email as &$value) {
			$value['uid'] = $uid;
			$value['_Date'] =  isset($value['Date']) ? date('Y-m-d H:i:s', $value['Date']) : null;
		}
		
		echo json_encode(array('data'=>$email));
	}
	
	public function delete(){
		require $this->config->item('sys_libs_dir') . 'http.class.php';
		require $this->config->item('sys_libs_dir') . 'gameapp.class.php';
		
		$tData = isset($_POST['tData']) ? $_POST['tData'] : _exit('请选中一行数据');
		$msg = '';
		
		foreach ($tData as $key => $value) {
			if (isset($value['Date']) && isset($value['uid'])) {
				
				$ret = gameapp::init($value['uid'])->deleteUserMail($value['Date']);
				
				if (!isset($ret['status']) || $ret['status'] != 0) {
					$msg = isset($value['Msg']) ? '邮件' . $value['Msg'] . '删除失败.' : '邮件' . $value['Date'] . '删除失败.';
				}
				continue;
			};
		}
		$msg = $msg == '' ? '删除成功': $msg;
		
		_exit($msg);
	}
}