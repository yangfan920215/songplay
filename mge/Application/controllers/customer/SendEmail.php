<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-3-31
 */

class SendEmail extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		$this->data['userType'] = $this->config->item('userType');
		
		//获取所有礼包
		$this->data['pList'] = $this->dbapp->config_sp_gifts_s('', -1, '');
		
		$this->parser->parse('SendEmail.html', $this->data);
	}
	
	
	public function ajaxMorris(){
		$req = $_POST;
		$userkey = isset($req['userkey']) ? explode(',', $req['userkey']) : exit(json_encode(array('status'=>-1, 'data'=>'参数异常2')));
		$pid = isset($req['pid']) && $req['pid'] != '' ? trim($req['pid']) : 0;
		$pnum = isset($req['pnum']) && $req['pnum'] != '' ? trim($req['pnum']) : 0;
		
		
/* 支持发送空礼包邮件		
 * if (empty($pnum)) {
			exit(json_encode(array('status'=>-1, 'data'=>'礼包个数异常')));
		} */
		
		$send_time = isset($req['send_time']) ? strtotime($req['send_time']) : exit(json_encode(array('status'=>-1, 'data'=>'参数异常5')));
		$usertype = isset($req['usertype']) ? trim($req['usertype']) : exit(json_encode(array('status'=>-1, 'data'=>'参数异常6')));
		$desc = isset($req['desc']) ? trim($req['desc']) : exit(json_encode(array('status'=>-1, 'data'=>'参数异常7')));
		
		require $this->config->item('sys_libs_dir') . 'http.class.php';
		require $this->config->item('sys_libs_dir') . 'gasapp.class.php';
		require $this->config->item('sys_libs_dir') . 'gameapp.class.php';
		
		// 假如输入的ID非uid
		if ($usertype != 0) {
			foreach ($userkey as $key => $val) {
				//调用GAS查询用户ID
				$gasapp =  gasapp::init();
				$res = $this->_getUid($usertype, $val, $gasapp);
				if($res['success'] == true) {
					$userkey[$key] = $res['errors'];
				}else{
					exit(json_encode(array('status'=>-1, 'data'=>'用户uid获取失败')));
				}
			}
		}
		
		$arr = '';
		foreach($userkey as $key => $val) {
			$result = gameapp::init($val, 44)->personalMessage($pid, $pnum, $send_time, $desc);	
	/* 		D($result); */
					
			if($result['status'] != 0) {
				$arr .= $val.',' ;
			}
		}
		
		if(!empty($arr)) {
			$errors = '发送给'.$val.'的'.$pid.'失败';
			echo json_encode(array('status'=>1, 'data'=>$errors), JSON_UNESCAPED_UNICODE);
		}else{
			echo json_encode(array('status'=>0, 'data'=>'邮件发送成功'));
			exit;
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