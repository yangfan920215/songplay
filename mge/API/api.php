<?php
ini_set('display_errors',1);

require dirname(__FILE__) . '/db_mysql.php';
require dirname(__FILE__) . '/../../pub/libs/class/xml.class.php';
require dirname(__FILE__) . '/../../pub/libs/class/redisconfig.class.php';
require dirname(__FILE__) . '/../../pub/libs/class/inter.class.php';
require dirname(__FILE__) . '/../../pub/libs/class/http.class.php';
require dirname(__FILE__) . '/../../pub/libs/class/gasapp.class.php';
require dirname(__FILE__) . '/../../pub/libs/class/gameapp.class.php';

class h_Tgs_m {
	
	var $userType = array(
						array('id'=>0, 'name'=>'UID', 'pname'=>'uid'),
						array('id'=>1, 'name'=>'GUID', 'pname'=>'guid'),
						array('id'=>2, 'name'=>'UNAME', 'pname'=>'uname'),
						array('id'=>3, 'name'=>'EMAIL', 'pname'=>'email'),
					);
	
	private function _getUid($chkid, $id, $app) {
		$output = array('success' => true,'errors' => '');
		
		switch ($chkid) {
			case 0:
				$output['errors'] = $id;
				break;
	
			case 1:
			case 2:
			case 3:
				$type = $this->userType;
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
	
	public function deposit($id,$propNum) { //$propNum 正為+ 負為-
		
		$gameID = 44;
		//usertype:uid=>0,gid=>1,uname=>2,email=>3
		$usertype = 1; 
		
		//调用GAS查询用户ID
		$gasapp =  gasapp::init();
		$res = $this->_getUid($usertype, $id, $gasapp);
		if($res['success'] == true) {
			$id = $res['errors'];
		}else{
			exit(json_encode(array('status'=>-1, 'data'=>'用户uid获取失败')));
		}
		
		$val = trim($id);
			
		$app = gameapp::init(trim($val), $gameID);
		
		$result = $app->modifyUserInfo(0, $propNum,  0);
		
		return $result;
	}
	
	public function getUserInfo($userKey) { //userKey
		//usertype:uid,gid,uname,email
		$userType = "guid";
		$data = gasapp::init()->getUserInfo($userKey, 'uid,uname,nick_name,real_name,avatar_id,sex,money,guid,mobile,email,idcard,area_code,mobile,register_time,logon_times,last_logon_time,logon_ip,last_charge_time,vip_level,vip_expiration,client_id,client_id_sub,plat_id,muid,disable_account,is_temp_account,safe_level', $userType);
		
		return $data;
	}
	
	private function config_sp_type_game_s(){
		$spName = 'sp_type_game_s';
		$dbName = 'config';
		$param = array(
				-1, '',
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	protected function execSp($spName, $param = array(), $dbName = 'mge'){
		// 链接数据库
		//$dbName == 'mge' ? $this->db = $this->CI->load->database('mge', true) : $this->changDb($_SESSION['dbconfig'][$dbName]);
	
		// 拼凑存储过程sql
		if ($param == array()) {
			$sql = 'CALL '.$spName.'();';
		}else{
			$sql_p = is_array($param) ? implode('","', $param) : $param;
			$sql = 'CALL ' . $spName . '("' . $sql_p . '")';
		}
		echo $sql;exit;
		// 执行存储过程,并获取返回数据
		$result = load_data($sql);//$this->db->query($sql)->result_array();
		// 重新链接,不然再次查询会报错
		//$this->db->reconnect();
		
		// 若属于insert或update类型的存储过程,按照约定解析
		/*if (isset($result[0]['status']) && isset($result[0]['msg']) && count($result) == 1) {
			return $result[0];
		}*/
	
		return $result;
	}
	
	 /**
	 * 转换成下拉菜单需要的数据类型
	 * @param  [type] $data  [description]
	 * @param  [type] $field [description]
	 * @param  [type] $name  *:代表全部,其余是key字符
	 * @return [type]        [description]
	 */
	private function toSelectData($data,$field = 'id',$name = 'name') {
		$output = array();
		foreach ($data as $key => $val) {
			$f = $field == null ? $key : $val[$field];
			if($name == '*') {
				$output[$f] = $val;
			} else {
				$output[$f] = $val[$name];
			}
		}
		return $output;
	}
	
	public function getGameInfo($uid) {
		//游戏清单
		$games = array(array("id"=>"44","name"=>"德州撲克"));//$this->config_sp_type_game_s();
		//print_r($games);exit;
		
		// 转置数据
		$games = $this->toSelectData($games, 'id','name');
		
		$gameApp = gameapp::init()->setUid($uid);
		
		return $gameApp->setGid(44)->getUserBean();
		
		foreach ($games as $gameid=>$gamename) {
			$gameTime = $gameApp->setGid($gameid)->getUserInfo(7);//7为游戏时长
			
			
			//$gameTime['msg'] = isset($gameTime['status']) && $gameTime['status'] == 0 && isset($gameTime['count']) ? $this->formatdate($gameTime['count']) : 0;
			
			$currency[$gameid] = array('name'=>$gamename ,'currency' => $gameApp->setGid($gameid)->getUserBean(), 'diam'=>$gameApp->setGid($gameid)->getUserDiam(),'gametime' => $gameTime);
		}
		
		return $currency;
	}
}

header("Content-type: text/html; charset=utf-8");
$a = new h_Tgs_m;
//$result = $a->deposit('3020124',-1);
//print_r($result); //Array ( [status] => 0 [count] => 1 )

$result = $a->getUserInfo('3020124');
if ($result['result']!=0) {
	echo "找不到會員";
	exit;
}else {
	$result['data']['nick_name'] = isset($result['data']['nick_name']) ? base64_decode($result['data']['nick_name']) : '';
	$result['data']['disable_account'] = (isset($result['data']['disable_account']) && $result['data']['disable_account'] == 1) ? '已封' : '激活';
	
	$result['data']['currency'] = $a->getGameInfo($result['data']['uid']);
	//print_r($gameinfo);exit;
	
	echo "guid:".$result['data']['guid']."<br />";
	echo "用戶名:".$result['data']['uname']."<br />";
	echo "用戶指標:".$result['data']['uid']."<br />";
	echo "註冊/激活時間:".date('Y-m-d H:i:s',$result['data']['register_time'])."<br />";
	echo "暱稱:".$result['data']['nick_name']."<br />";
	echo "最後登入IP:".$result['data']['logon_ip']."<br />";
	echo "最後登入時間:".date('Y-m-d H:i:s',$result['data']['last_logon_time'])."<br />";
	echo "渠道:".$result['data']['client_id']."<br />";
	echo "子渠道:".$result['data']['client_id_sub']."<br />";
	echo "是否封號:".$result['data']['disable_account']."<br />";
	echo "德撲餘額:".$result['data']['currency']."<br />";
}
/*
Array ( [result] => 0 [data] => Array ( [uid] => 2001018 [uname] => aby01018 [nick_name] => 57Gz6Jmr [real_name] => [avatar_id] => 7 [sex] => 1 [money] => 0 [guid] => 3020124 [mobile] => [email] => [idcard] => [area_code] => 0 [register_time] => 1460706626 [logon_times] => 149 [last_logon_time] => 1493287018 [logon_ip] => 218.161.115.68 [last_charge_time] => 1473225207 [vip_level] => 0 [vip_expiration] => 0 [client_id] => 10227 [client_id_sub] => 0 [plat_id] => 301 [muid] => 0 [disable_account] => 0 [is_temp_account] => 0 [safe_level] => 0 ) [msg] => 成功 )
*/
//print_r($result); //Array ( [status] => 0 [count] => 1 )
?>