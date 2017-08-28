<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-3-4
 */

class UserProp extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		$this->data['userType'] = $this->config->item('userType');
		$this->data['ptype'] = $this->config->item('ptype');

        $this->load->library('app/client', array($_SESSION['authId']));

        if ($this->client->getLevel() !== 0) {
            unset($this->data['ptype'][0], $this->data['ptype'][1], $this->data['ptype'][2]);
        }
		
		$this->data['json'] = json_encode($this->getAllProp($this->config->item('ptype')), JSON_UNESCAPED_UNICODE);
		
		$this->data['getPropType'] = $this->config->item('getPropType');
		
		$this->parser->parse('userProp.html', $this->data);
	}
	
	public function ajaxMorris(){
		$pList = isset($_POST['tData']) ? $_POST['tData'] : exit(json_encode(array('status'=>-1, 'data'=>'参数异常1')));
		
		$num = isset($_POST['propNum']) ? $_POST['propNum'] : exit(json_encode(array('status'=>-1, 'data'=>'参数异常3')));
		
		if (!is_array($pList)) {
			exit(json_encode(array('status'=>-1, 'data'=>'参数异常4')));
		}

		
		require $this->config->item('sys_libs_dir') . 'http.class.php';
		require $this->config->item('sys_libs_dir') . 'gameapp.class.php';
		$errmsg_total = '';
		$errmsg = '';
		
		foreach ($pList as $value) {
			$userPnum = isset($value['count']) ? $value['count'] : exit(json_encode(array('status'=>-1, 'data'=>'参数异常4')));
			$uid = isset($value['uid']) ? $value['uid'] : exit(json_encode(array('status'=>-1, 'data'=>'参数异常5')));
			$pid = isset($value['pid']) ? $value['pid'] : exit(json_encode(array('status'=>-1, 'data'=>'参数异常6')));
			$kid = isset($value['typeid']) ? $value['typeid'] : exit(json_encode(array('status'=>-1, 'data'=>'参数异常6')));
			$gid = isset($value['gid']) ? $value['gid'] : exit(json_encode(array('status'=>-1, 'data'=>'参数异常7')));
			$goods_name = isset($value['goods_name']) ? $value['goods_name'] : exit(json_encode(array('status'=>-1, 'data'=>'参数异常8')));
			
			// 传入GAS的参数
			$editNum = intval($num - $userPnum);
			
			$ret = gameapp::init($uid, $gid)->modifyUserProp($pid, $editNum, 0, $kid);
			
			
			if($ret['status'] != 0) {
				$errmsg .= $goods_name.'发送失败,' ;
			}
		}
		
		if ($errmsg_total != '') {
			echo json_encode(array('status'=>-1, 'data'=>$errmsg_total));
			exit;
		}
		
		
		$this->wLog($gid, 'uid', $uid, $pid, $editNum, 0);
		echo json_encode(array('status'=>0, 'data'=>'道具修改成功'));
	}
	
	public function ajaxData(){
		$gid = isset($_REQUEST['extra_search'][0]['req']['gameId']) ? $_REQUEST['extra_search'][0]['req']['gameId'] : null;
		$userkey = isset($_REQUEST['extra_search'][0]['req']['userkey']) ? $_REQUEST['extra_search'][0]['req']['userkey'] : null;
		$usertype = isset($_REQUEST['extra_search'][0]['req']['usertype']) ? $_REQUEST['extra_search'][0]['req']['usertype'] : null;

		if (!isset($gid, $userkey, $usertype)) {
			exit(json_encode(array('data'=>array())));
		}
		
		echo $this->data($gid, $usertype, $userkey);
	}
	
	public function addModel(){
		// 游戏id
		$gameID = get_comm_req('gameId');
		// 用户类型
		$usertype = get_comm_req('usertype');
		// 用户列表
		$uids = get_comm_req('uids');
		// 道具类型id
		$proptype = get_comm_req('proptype');

		// 渠道商不允许发放金币外的物品
        $this->load->library('app/client', array($_SESSION['authId']));
        if ($this->client->getLevel() !== 0 && $proptype != 3) {
            exit(json_encode(array('status'=>-1, 'data'=>'权限不足,发放失败')));
        }

		// 如果不是编辑金币数目
		if ($proptype != 3) {
			// 道具id		
			$propid = get_comm_req('propid');
		}
		// 道具数目
		$propNum = get_comm_req('propNum');
		// 过期时间
		$expi_time = get_comm_req('expi_time');
		if ($expi_time == '') {
			$expi_time = 0;
		}
		
		// 发放理由
		$desc = get_comm_req('type_id');
		
		// 选择自定义理由
		if ($desc === 0) {
			$desc = get_comm_req('sum');
		}
		
		
		$id = explode(',', $uids);
		if(count($id) > 50) {
			exit(json_encode(array('status'=>-1, 'data'=>'用户过多')));
		}
		
		require $this->config->item('sys_libs_dir') . 'http.class.php';
		require $this->config->item('sys_libs_dir') . 'gasapp.class.php';
		require $this->config->item('sys_libs_dir') . 'gameapp.class.php';
		
		// 如果不是uid,则查询uid
		if ($usertype != 'uid') {
			foreach ($id as $key => $val) {
				//调用GAS查询用户ID
				$gasapp =  gasapp::init();
				$res = $this->_getUid($usertype, $val, $gasapp);
				if($res['success'] == true) {
					$id[$key] = $res['errors'];
				}else{
					exit(json_encode(array('status'=>-1, 'data'=>'用户uid获取失败')));
				}
			}
		}
		
		$arr = '';
		foreach($id as $key => $val) {
			// 防止空格之类的数据
			$val = trim($val);
			
			$app = gameapp::init(trim($val), $gameID);
			
/* 			if($pid == 0) {
				$result = $app->modifyUserInfo(0, $count, 0 , 5, $sum);
			} else {
     		   	
			} */
			
			
			$log_prop_id = 0;
			$log_prop_num = 0;
			$log_gold = 0;
			
			if ($proptype != 3) {
				$result = $app->modifyUserPropV1($propid, $propNum, $expi_time, $proptype, $desc);
				
				$log_prop_id = intval($propid);
				$log_prop_num = intval($propNum);
			}else{
				// 发送金币
				$result = $app->modifyUserInfo(0, $propNum,  0);
				$log_gold = intval($propNum);
			}
			
     			
     		if($result['status'] != 0) {
	    		$arr .= $val.',' ;
	    	}else{
	    		// 发送成功,记录日志
	    		@$this->wLog($gameID, 'uid', $val, $log_prop_id, $log_prop_num, $log_gold);
	    	}
		}
		
		if(!empty($arr)) {
			$errors = '发送道具'.$arr.'通知服务器失败';
			echo json_encode(array('status'=>1, 'data'=>$errors));
		}else{
			_exit('道具发送成功', 0);
		}
		
	}
	
	private function wLog($game_id, $user_type, $user_key,$log_prop_id, $log_prop_num, $log_gold){
		$this->dbapp->manage_sp_web_log_i($game_id, $user_type, $user_key, $_SESSION[$this->config->item('USER_AUTH_KEY')], $_SESSION['email'], $log_prop_id, $log_prop_num, $log_gold);
	}
	
	private function data($gid, $userType, $userKey){
		// 加载公共gas,game,http
		require $this->config->item('sys_libs_dir') . 'http.class.php';
		require $this->config->item('sys_libs_dir') . 'gasapp.class.php';
		require $this->config->item('sys_libs_dir') . 'gameapp.class.php';
		//游戏名称列表
		$typeGameList = $this->dbapp->config_sp_type_game_s();
		
		// 转置数据
		$typeGameList = $this->dataext->toSelectData($typeGameList,'id','name');
		$rows = 0;
		$list = array();
		$errors = '';
		//调用GAS查询用户ID
		$gasapp =  gasapp::init();
		$res = $this->_getUid($userType, $userKey, $gasapp);
		$uid = $userType == 0 ? intval($userKey) : $res['errors'];
		
		if($res['success'] == true) {
			//调用GAMEAPP查询用户背包
			//uid=>用户id，gameid=>游戏id
			$gameapp = Gameapp::init($uid, $gid);
			$game_data = $gameapp->getUserProp();
			
			$userInfo = $gameapp->getUserInfo();
			
			$gold_count = isset($userInfo['status']) && $userInfo['status'] == 0 && isset($userInfo['count']) ? intval($userInfo['count']) : 'unknow1';
			
			//D($game_data);
			if($game_data['status'] == 0) {
				if (isset($game_data['data']) && count($game_data['data']) > 0) {
					$list = $game_data['data'];
					$rows = count($game_data['data']);
					//获取所有道具
					$prop = array();
					$res = $this->dbapp->config_sp_prop_s('', -1, '');
					if(isset($res) && count($res)) {
						foreach($res as $_res){
							$prop[$_res['id']] = trim($_res['name']);
						}
					}
					unset($res);
					//获取所有礼包
					$gifts = array();
					$res = $this->dbapp->config_sp_gifts_s('', -1, '');
					if(isset($res) && count($res)) {
						foreach($res as $_res){
							$gifts[$_res['id']] = trim($_res['name']);
						}
					}
					unset($res);
					//获取所有物品
					$goods = array();
					$res = $this->dbapp->config_sp_goods_s_simple(-1);
					if(isset($res) && count($res)) {
						foreach($res as $_res){
							$goods[$_res['id']] = trim($_res['name']);
						}
					}
					unset($res);
					if(isset($list) && count($list)) {
						foreach($list as $k=>$v) {
							$list[$k]['gid'] = $gid;
							$list[$k]['gold'] = $gold_count;
							$list[$k]['expi'] = date('Y-m-d H:i:s',$v['expi']);
							$list[$k]['id'] = $k;
							if($v['typeid'] == 0) {
								if (isset($prop[$v['pid']])) {
									$list[$k]['goods_name'] = $prop[$v['pid']];
								}else{
									unset($list[$k]);
								}
							} elseif($v['typeid'] == 1) {
								if (isset($gifts[$v['pid']])) {
									$list[$k]['goods_name'] = $gifts[$v['pid']];
								}else{
									unset($list[$k]);
								}
							} elseif($v['typeid'] == 2) {
								if (isset($goods[$v['pid']])) {
									$list[$k]['goods_name'] = $goods[$v['pid']];
								}else{
									unset($list[$k]);
								}
							}
						}
					}
					// $this->ptid2ptname($list);
				}
			}else{
				//$this->alert($game_data['msg'],'ERROR',false);
				$errors = $game_data['msg'];
			}
		} else {
			//$this->alert('用户信息不存在','ERROR', false);
			$errors = '用户信息不存在';
		}
		$list = array_values($list);
		convDT($list);

		return json_encode(array('data'=>$list), JSON_UNESCAPED_UNICODE);
		
	}
	
	private function getAllProp($ptype){
		$res = $this->dbapp->config_sp_prop_s('', -1, '');
		
		if(isset($res) && count($res)) {
			foreach($res as $_res){
				$ptype[0]['child'][$_res['id']] = trim($_res['name']);
			}
		}
		unset($res);
		//获取所有礼包
		$res = $this->dbapp->config_sp_gifts_s('', -1, '');
		if(isset($res) && count($res)) {
			foreach($res as $_res){
				$ptype[1]['child'][$_res['id']] = trim($_res['name']);
			}
		}
		unset($res);
		//获取所有物品
		$res = $this->dbapp->config_sp_goods_s_simple(-1);
		if(isset($res) && count($res)) {
			foreach($res as $_res){
				$ptype[2]['child'][$_res['id']] = trim($_res['name']);
			}
		}
		return $ptype;
	}
	
	
/* 	private function ptid2ptname(&$list){
		$ptype = $this->config->item('ptype');
		foreach ($list as $key => &$value) {
			if (isset($value['typeid']) && isset($ptype[$value['typeid']])) {
				$value['pname'] = $ptype[$value['typeid']];
			}
			continue;			
		}
	}
 */	
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