<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-3-2
 */

class UserInfo extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		
		$this->load->database('manage');
	}
	
	public function index(){
		
		$this->parser->parse('userInfo.html', $this->data);
	}
	
	
	public function ajaxData(){
		$req = isset($_POST['req']) ? $_POST['req'] : exit(getErrorJson('数据异常'));
		$userType = isset($req['usertype']) ? $req['usertype'] : exit(getErrorJson('数据异常'));
		$userKey = isset($req['userkey']) ? $req['userkey'] : exit(getErrorJson('数据异常'));
		echo $this->data($userType, $userKey);
	}
	
	private function data($userType, $userKey){
		//游戏币清单
		$currency = array();
		
		// 加载公共gas,game,http
		require $this->config->item('sys_libs_dir') . 'http.class.php';
		require $this->config->item('sys_libs_dir') . 'gasapp.class.php';
		require $this->config->item('sys_libs_dir') . 'gameapp.class.php';
		
		$data = gasapp::init()->getUserInfo($userKey, 'uid,uname,nick_name,real_name,avatar_id,sex,money,guid,mobile,email,idcard,area_code,mobile,register_time,logon_times,last_logon_time,logon_ip,last_charge_time,vip_level,vip_expiration,client_id,client_id_sub,plat_id,muid,disable_account,is_temp_account,safe_level', $userType);
		
		// 对nick_name进行解码
		$data['data']['nick_name'] = isset($data['data']['nick_name']) ? base64_decode($data['data']['nick_name']) : '';
		
		if($data['result'] == 0) {

			// 非该渠道用户其他渠道账户无法查看
			$hasAuth = false;
			
			
			$this->load->library('app/client', array($_SESSION['authId']));
			
			// 非渠道用户可以查询任何用户
			if ($this->client->getLevel() === 0) {
				$hasAuth = true;
			}
			
			$ret = $this->dbapp->manage_sp_web_get_clientsub($_SESSION['authId']);
			
			if (is_array($ret) && isset($data['data']['client_id'])){
				foreach ($ret as $key=>$value) {
					if (isset($value['clientid']) && $value['clientid'] == $data['data']['client_id']) {
						$hasAuth = true;
						break;
					};
					continue;
				}
			}else{
				execExit('找不到登录账户所属的渠道商信息');
			}
			
			if (!$hasAuth) {
				execExit('用户不存在');
			}
			
			//游戏清单
			$games = $this->dbapp->config_sp_type_game_s();
			
			// 转置数据
			$games = $this->dataext->toSelectData($games, 'id','name');
			if(isset($data['data']['uid']) && $data['data']['uid'] > 0) {
				$data['data']['sex'] = (isset($data['data']['sex']) && $data['data']['sex'] == 0) ? '女士' : '先生';
				$data['data']['disable_account'] = (isset($data['data']['disable_account']) && $data['data']['disable_account'] == 1) ? '已封' : '激活';
				$data['data']['is_temp_account'] = (isset($data['data']['is_temp_account']) && $data['data']['is_temp_account'] == 1) ? '不是' : '是';
				$gameApp = gameapp::init()->setUid($data['data']['uid']);
				foreach ($games as $gameid=>$gamename) {
					$gameTime = $gameApp->setGid($gameid)->getUserInfo(7);//7为游戏时长
					
					
					$gameTime['msg'] = isset($gameTime['status']) && $gameTime['status'] == 0 && isset($gameTime['count']) ? $this->formatdate($gameTime['count']) : 0;
					
					$currency[$gameid] = array('name'=>$gamename ,'currency' => $gameApp->setGid($gameid)->getUserBean(), 'gametime' => $gameTime);
				}
			}
			
			// 地区转换
			if( isset($data['area_code']) && $data['area_code'] != 0 ) {
				$arraName = $this->dbapp->config_sp_area_s_detail($data['area_code']);
				if(!empty($arraName[0]['city'])) {
					$arraName = $arraName[0]['city'];
				} else {
					$arraName = '不存在该地区';
				}
			} else {
				$arraName = '全国';
			}
			
			// 推广信息
			$tuiguang = $this->dbapp->manage_sp_report_promoterInfo($data['data']['uid']);
			// 推广返利
			$data['data']['amounts'] = isset($tuiguang[0]['amounts']) ? $tuiguang[0]['amounts'] : 0;
			// 推广人数
			$data['data']['num'] = isset($tuiguang[0]['num']) ? $tuiguang[0]['num'] : 0;
			// 推广人id
			$data['data']['touid'] = isset($tuiguang[0]['touid']) ? $tuiguang[0]['touid'] : null;
			// 被推广时间
			$data['data']['post_date'] = isset($tuiguang[0]['post_date']) ? $tuiguang[0]['post_date'] : null;
			
			// 第三方登录类型和绑定id
			$bindInfo = $this->dbapp->manage_sp_report_bind($data['data']['uid']);
			$data['data']['bind_id'] = isset($bindInfo[0]['bind_id']) ? $bindInfo[0]['bind_id'] : null;
			$data['data']['third_id'] = isset($bindInfo[0]['third_id']) ? $bindInfo[0]['third_id'] : null;
			
			
			$data['data']['arraName'] = $arraName;
			$data['data']['currency'] = $currency;
			
			$data['data']['last_logon_time'] = isset($data['data']['last_logon_time']) ? date('Y-m-d H:i:s', $data['data']['last_logon_time']) : null;
			$data['data']['register_time'] = isset($data['data']['register_time']) ? date('Y-m-d H:i:s', $data['data']['register_time']) : null;
				
			$data = json_encode(array('status'=>0, 'data'=>$data['data']), JSON_UNESCAPED_UNICODE);
			
			
		} else {
			execExit($data['msg']);
		}
		
		return $data;
	}
	
	private function formatdate($t){
		$t=intval($t);
	
		$d=floor($t/(60*60*24));
		$h=floor(($t-$d*60*60*24)/(60*60));
		$M=floor(($t-$d*(60*60*24)-$h*60*60)/60);
		$s=floor($t-$d*(60*60*24)-$h*60*60-$M*60);
		$str="";
		if($d>0){
			$str=$str.$d."天";
		}
		if($h>0){
			$str=$str.$h."小时";
		}
		if($M>0){
			$str=$str.$M."分";
		}
		if($s>0){
			$str=$str.$s."秒";
		}
		return $str;
	}
}