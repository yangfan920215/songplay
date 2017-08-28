<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-6-1
 */

class UserLock extends CI_Controller{
	
	/**
	 * 反馈处理状态配置
	 * @var unknown
	 */
	private $isLock = array(
		array('id'=>-1, 'name'=>'全部'),
		array('id'=>0, 'name'=>'封号'),
		array('id'=>1, 'name'=>'解号'),
	);
	
	/**
	 * 31天的时间戳
	 * @var int
	 */
	private $lockDayList = array(
			array('id'=>1, 'name'=>'封号1天'),
			array('id'=>3, 'name'=>'封号3天'),
			array('id'=>7, 'name'=>'封号7天'),
			array('id'=>30, 'name'=>'封号30天'),
			array('id'=>0, 'name'=>'永久'),
	);
	
	private $action = array(
		array('value'=>1, 'desc'=>'禁止聊天', 'name'=>'stalk'),
		array('value'=>2, 'desc'=>'禁止喇叭', 'name'=>'slaba'),
		array('value'=>4, 'desc'=>'禁止互动道具', 'name'=>'shudong'),
		array('desc'=>'踢出房间', 'name'=>'killRoom'),
	);
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		
		$this->data['userType'] = $this->config->item('userType');
		$this->data['isLock'] = $this->isLock;
		$this->data['lockDayList'] = $this->lockDayList;
		$this->data['action'] = $this->action;
		
		$this->parser->parse('customer/UserLock.html', $this->data);
	}
	
	public function ajaxTable(){
		$chkid = get_extra_v2('usertype');
		$isLock = get_extra_v2('isLock');
		$gid = get_extra_v2('gid');
		$sDate = get_extra_v2('sDate');
		$eDate = get_extra_v2('eDate');
		
		$id = get_extra_v2('userkey');
		
		
		// 加载公共gas,game,http
		require $this->config->item('sys_libs_dir') . 'http.class.php';
		require $this->config->item('sys_libs_dir') . 'gasapp.class.php';
				
		$app = Gasapp::init();
		
		if (!empty($id)) {
			//如果为UID查一次GAS
			if ($chkid == 0) {
				$rs = $app->getUserInfo($id,'uid', $chkid);
				if($rs['result'] == 0) {
					$res['success'] = true;
					$res['errors'] = $rs['data']['uid'];
					 
				} else {
					$res['success'] = false;
					$res['errors'] = $rs['msg'];
				}
			}else{
				$res = $this->_getUid($chkid, $id, $app);
			}
		
			if ($res['success']) {
				$uid = $res['errors'];
			} else {
				exit(json_encode(array('data'=>array())));
			}
		}else{
			$uid = -1;
		}
		
		$data = $this->dbapp->manage_sp_user_lock_new_s($uid, $gid, $isLock, $sDate, $eDate);
		
		convGname($data, $this->config->item('gameList'), 'game_id');
		
		echo json_encode(array('data'=>$data));
	}
	
	public function ajaxMorris_1(){
		$userkey = $this->datarece->post('userkey', true, '用户不存在');
		$usertype = $this->datarece->post('usertype', true, '请选择用户类型');
		$date = $this->datarece->post('date', true, '请选择用户类型');
		
		// 时间必须比当前时间长一分钟
		$date = strtotime($date);
		if ($date + 60 < time()) {
			execExit('最少封禁一分钟');
		}
		
		$killRoom = $this->datarece->post('killRoom', false);
		$msg = '';
		
		// 加载公共gas,game,http
 		require $this->config->item('sys_libs_dir') . 'http.class.php';
		require $this->config->item('sys_libs_dir') . 'gameapp.class.php';
		require $this->config->item('sys_libs_dir') . 'gasapp.class.php';
		
		
		$gas = Gasapp::init();

			//如果为UID查一次GAS
		$uid = $this->convUid($userkey, $usertype, $gas);
		
		$game = Gameapp::init($uid);
		
		if ($uid === false) {
			execExit('该用户不存在');
		}
		
		if (isset($killRoom)) {
			// 踢出房间
			$ret = $game->kickUser();
			
			$msg .= isset($ret['status']) && $ret['status'] == 0 ? '用户踢出成功;' : '用户踢出失败;';
		}
			
		$par_var = 0;
		foreach ($this->action as $value) {
			if (isset($_REQUEST[$value['name']]) && $_REQUEST[$value['name']] == 'on_') {
				$val  = isset($value['value']) ? $value['value'] : 0;
				$par_var += $val;
				
				// 执行封禁操作
				if ($par_var != 0) {
					$ret = $game->modifyuserstate($par_var, $date);
				
					$msg .= isset($ret['status']) && $ret['status'] == 0 ? $value['desc'] . '成功;' : $value['desc'] . '失败;';
				}
			};
		}
		
		execExit($msg, 0);
	}
	
	private function convUid($userKey, $userType, &$app){
		if ($userType == 0) {
			return $userKey;
		}else{
			$res = $app->getUserInfo($userKey, 'uid', $this->config->item('userType')[$userType]['pname']);

			if(isset($res['result']) && isset($res['data']['uid']) && $res['result'] == 0) {
				return $res['data']['uid'];

			} else {
				return false;
			}
		}
		
		return false;
	}
	
	public function ajaxMorris(){
		// 加载公共gas,game,http
		require $this->config->item('sys_libs_dir') . 'http.class.php';
		require $this->config->item('sys_libs_dir') . 'gasapp.class.php';
		$app = Gasapp::init();
		
		if (isset($_REQUEST['tData'][0]['uid'])) {
			$uid = $_REQUEST['tData'][0]['uid'];
			$gid = $_REQUEST['tData'][0]['game_id'];
			$desc = $_REQUEST['tData'][0]['summary'];
			
			$app->setUid($uid);
			
			$res = $app->modUserInfo(array('disable_account'=>0));
			
			
			
			if(isset($res['result']) && $res['result'] == 0) {
				$res = $this->dbapp->manage_sp_user_lock_iu('u', $uid, $this->session->email, $this->input->ip_address(), $gid, $desc, '1900-12-31 23:59:59');
				if(isset($res[0]['status']) && $res[0]['status'] == 0) {
					execExit('解禁成功');
				}
			} else {
				execExit('写入流水失败');
			}
		}
		
		$gid = $this->datarece->post('gid', true, '游戏不存在');
		$id = $this->datarece->post('userkey', true, '玩家id不存在');
		$chkid = $this->datarece->post('usertype', true, '玩家id类型不存在');
		$desc = $this->datarece->post('desc', false);
		$locktime = $this->datarece->post('locktime', true, '必须填写封号时间');

		//如果为UID查一次GAS
		if ($chkid == 0) {
			$rs = $app->getUserInfo($id,'uid', $chkid);
			if($rs['result'] == 0) {
				$res['success'] = true;
				$res['errors'] = $rs['data']['uid'];
	
			} else {
				$res['success'] = false;
				$res['errors'] = $rs['msg'];
			}
		}else{
			$res = $this->_getUid($chkid, $id, $app);
		}
	
		if ($res['success']) {
			$uid = $res['errors'];
		} else {
			execExit('用户不存在');
		}
		
		$app->setUid($uid);
		
		$st= $app->getUidInfo('disable_account');
		// 用户处于未封号状态
		$res = $app->modUserInfo(array('disable_account'=>0));
		if( isset($st['result']) && $st['result'] == 0 && isset($st['data']['disable_account']) && $st['data']['disable_account'] == 0) {
			$res = $app->modUserInfo(array('disable_account'=>1));
			
			//如果返回成功，写操作记录,调用存储过程
			if(isset($res['result']) && $res['result'] == 0) {
				$res = $this->dbapp->manage_sp_user_lock_iu('i', $uid, $this->session->email, $this->input->ip_address(), $gid, $desc, '2099-12-31 23:59:59');
				
				if(isset($res[0]['status']) && $res[0]['status'] == 0) {
					execExit('封禁成功');
				} 
			} else {
				execExit('写入流水失败');
			}
		
		} else {
			execExit('用户已处于封号状态');
		}
	}
	
	private function _getUid($chkid, $id, $app) {
		$output = array('success' => true,'errors' => '');
	
		switch ($chkid) {
			case 0:
				$output['errors'] = $id;
				break;
	
			case 1:
			case 2:
			case 3:
				$type = $this->config->item('userType');
				$res = $app->getUserInfo($id, 'uid', $type[$chkid]['pname']);
				if($res['result'] == 0) {
					$output['errors'] = $res['data']['uid'];
	
				} else {
					$output['success'] = false;
					$output['errors'] = $res['msg'];
				}
				break;
			default:
				$output['success'] = false;
				$output['errors'] = '参数错误';
				break;
		}
	
		return $output;
	}
}