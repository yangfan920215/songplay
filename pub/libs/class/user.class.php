<?php
/**
 * 玩家用户类
 * @package Class
 * @author Haver.Guo
 * @version 1.0.0
 + build date 2012-08-23
 */

class user {
	
	/**
	 * 用户登录信息（Cookie）
	 * @var string
	 */
	public static $_sid = '';
	
	/**
	 * 初始化
	 */
	public function __construct(){}

	/**
	 * 用户登录
	 * @param string $username 用户账号
	 * @param string $password 用户密码
	 * @return bool 是否登录成功
	 */
	public static function login($username ,$password ,$plat=0 ,$clientid=10) {
		
		//调用接口
		$api = new api_interface();
		$backinfo = $api -> login($username, $password ,$plat ,$clientid);
		
		$arr = array();
		
		/* 登录成功后，改用调用app提取用户
		 * 2013.4.26 Haver.Guo
		 * 
		if (self::interfacebackCheck(&$backinfo, &$arr) && $arr['uid']>0) {
		*/
		$arrback = json_decode($backinfo,true);
		
		if(!empty($arrback) && isset($arrback['uid']) && $arrback['uid']>0) {	
			$arr = gasapp::init($arrback['uid'])->getUidInfo();
		} else {
		    return false;
		}
		
		/*end*/
		
		if(isset($arr['uid']) && $arr['uid']>0) {
			//成功则进行登录
			//return self::tokenlogin($arr['token']);
			$sid = self::sidEncrypt($arr['uid'], $arr['uname'],$arr['guid'] ,3 ,0);
			//写cookie
			if($GLOBALS['ini_cookie']) {
				cookie::set(COOKNAME_USERLOGIN,$sid);
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
		$api = null;
	}
	
	/**
	 * 获取用户资料(从APP缓存)
	 * @param int $weuid	游戏ID
	 * @return string json 接口返回数据
	 */
	public static function getuserinfo($weuid) {
	    /* -- 修改为从APP缓存中获取
		require_once SROOT.'/source/api/api_interface.php';
		$api = new api_interface();
		$info = $api -> getinfo($weuid);
		$api = null;
		*/
	    $cols = 'uid,uname,nick_name,real_name,pwd,token,sex,sign,area_code,guid,client_id';
	    $info = gasapp::init($weuid)->getUserInfo($weuid, $cols, 'uid');
	    if(isset($info['result']) && $info['result'] == 0) {
	    	return $info['data'];
	    } else {
	    	return null;
	    }
	}
	
	/**
	 * 使用token进行登录，如通过URL跳转
	 * @param string $strtoken 未解密的token
	 */
	public static function tokenlogin($strtoken) {
		//成功则进行登录
		$myinfo = self::tokenDecrypt($strtoken);//var_dump($myinfo);die;
		
		//sync_writelog('testlog',json_encode($myinfo).' -- '.$strtoken."\r\n");
		if(!empty($myinfo) && is_array($myinfo) && isset($myinfo['uid']) && $myinfo['uid']>0) {
			$sid = self::sidEncrypt($myinfo['uid'], $myinfo['userName'],'' ,$myinfo['platId'] ,0);
			//写cookie
			if($GLOBALS['ini_cookie']) {
				cookie::set(COOKNAME_USERLOGIN,$sid);
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	
	/**
	 * /**
	 * 旧平台过来的，通过参数验证后直接登录	 
	 * @param int $uid 用户游戏id
	 * @param long $ts 时间戳
	 * @param string $verify 验证串
	 * @return boolean
	 */
	public static function oldplat_login($uid=0 ,$ts=0 ,$verify='') {
		$key_oldplat = '87299596b8d9d642922a9a659aa70723';		
		$uid = empty($uid) ? get('uid') : $uid;
		$ts = empty($ts) ? get('ts') : $ts;
		$verifystring_get = empty($verify) ? get('verifystring') : $verify;		
		if(empty($uid) || empty($ts) || empty($verifystring_get)) {
		    return false;
		}
		
		$verifystring = md5('uid='.$uid.'&ts='.$ts.'&key='.$key_oldplat);		
		//验证通过，则生成用户登录信息
		if ($uid > 0 && $verifystring == $verifystring_get) {
		    $userinfo = self::getuserinfo($uid);
		    if(isset($userinfo['guid'])) {
				$sid = self::sidEncrypt($uid, $userinfo['uname'],$userinfo['guid'] ,3 ,0);
				//写cookie
				if($GLOBALS['ini_cookie']) {
					cookie::set(COOKNAME_USERLOGIN, $sid);
					return true;
				}
		    }
		    return false;
		} else {
		    return false;
		}
	}
	
	/**
	 * 2013.8.15 ttoken登录
	 * @author Haver.Guo
	 * @param string $ttoken 加密串，格式 userid|timespan|(userid+timespan+TTOKEN_CHECK)%256
	 * @return bool
	 */
	public static function ttoken_login($ttoken) {
	    //老版本加密密钥
	    $key_oldplat = '87299596b8d9d642922a9a659aa70723';
	    //加载AES128
	    require_once SROOT.'/source/lib/aes128.class.php';
	    $aes = new aes128();
	    //取出拆分成组成，解码后格式为 userid|timespan|
	    $newtoken = explode('|' ,$aes -> decrypt($ttoken, TTOKEN_KEY));
	    $aes = null;
	    if(!empty($newtoken) 
	       && is_array($newtoken) 
	       && count($newtoken) >= 3
	       && ($newtoken[0] + $newtoken[1] + TTOKEN_CHECK) % 256 == $newtoken[2]
	       && (time() - $newtoken[1]) < LOGONTIMEOUT
	    ) {
	        $verifystring = md5('uid='.$newtoken[0].'&ts='.$newtoken[1].'&key='.$key_oldplat);
	        return self::oldplat_login($newtoken[0] ,$newtoken[1] ,$verifystring);
	    } else {
	        return false;
	    }
	}
	

	/**
	 * 检查用户是否登录
	 * @return bool true=已登录，false=未登录
	 */
	public static function islogin() {
		if(!$GLOBALS['ini_cookie']) {
			return false;
		}
		$sid = cookie::get(COOKNAME_USERLOGIN);
		$arrsid = self::sidDecrype($sid);
		if(!empty($arrsid) && is_array($arrsid) && isset($arrsid['weuid']) && $arrsid['weuid']>0) {
			return true;
		} else {
			return false;
		}
	}	
	
	/**
	 * 获取用户登录数据（即解密用户登录信息)
	 * @return
	 * 		未登录时 返回 false
	 *  	已登录返回array()
	 */
	public static function getlogininfo() {
		$arrsid = array();
		if(!$GLOBALS['ini_cookie'] && empty(self::$_sid)) {
			return false;
		}
		if(empty(self::$_sid)) {
			self::$_sid = cookie::get(COOKNAME_USERLOGIN);
		}
		$arrsid = self::sidDecrype(self::$_sid);
		return $arrsid;
	}
	
	/**
	 * 获取登录用户ID
	 * @return int
	 */
	public static function getweuid() {
		$arrsid = self::getlogininfo();
		if(!empty($arrsid)) {
			return $arrsid['weuid'];
		} else {
			return null;
		}
	}
	
	/**
	 * 获取保存的通过手机客户端传入的手机信息
	 * @return 正确时返回数组
	 */
	public static function get_client_ua() {
		if(!$GLOBALS['ini_cookie']) {
			return null;
		}
		return json_decode(base64_decode(cookie::get(COOKNAME_MOBILE)));
	}
	
	/**
	 * 设置用户客户端信息
	 * @param string $generic base64_encode(g)见《客户端入口调用说明文档》
	 * @return bool
	 */
	public static function set_client($generic) {
		if(!$GLOBALS['ini_cookie']) {
			return false;
		}
		cookie::set(COOKNAME_CLIENT,$generic);
		return true;
	}
	
	/**
	 * 获取用户客户端信息
	 * @return array()
	 */
	public static function get_client() {
		if(!$GLOBALS['ini_cookie']) {
			return array();
		}
		$ck = base64_decode(cookie::get(COOKNAME_CLIENT));
		require_once SROOT.'/source/api/urlparameter.php';
		//自定义的URL处理类
		$urldispose = new urlparameter();
		//取出常规参数各属性
		return $urldispose -> split_client_generic($ck);
	}
	
	
	/**
	 * 获取用户的手机信息（品牌、型号、系统、分辨率数据）主要用于匹配下载
	 * 包含两部分
	 * 	一、通过手机客户端口传入的手机数据(优先)
	 * 	二、通过UA信息中抓取的手机数据
	 *  @return array('client'=>array(brand,model,resoluwidth,resoluheight,os,version),'wapua'=>array());
	 */
	public static function get_mymobileinfo() {
		//定义返回的数据格式
		$mobileinfo = array('client'=>array(),'wapua'=>array());
		$arrclient = self::get_client_ua();
		$arrwapua = self::get_wapua();

		
		//如果有客户商品数据
		if(!empty($arrclient) && is_array($arrclient)) {
			//品牌
			$mobileinfo['client']['brand'] = isset($arrclient['b']) ? $arrclient['b'] : '';
			//型号
			$mobileinfo['client']['model'] = isset($arrclient['m']) ? $arrclient['m'] : '';
			//分辨率宽度
			$mobileinfo['client']['resoluwidth'] = isset($arrclient['sw']) ? $arrclient['sw'] : '';
			//分辨率高度
			$mobileinfo['client']['resoluheight'] = isset($arrclient['sh']) ? $arrclient['sh'] : '';
			//手机系统
			$mobileinfo['client']['os'] = isset($arrclient['sys']) ? $arrclient['sys'] : '';
			//系统版本
			$mobileinfo['client']['osversion'] = isset($arrclient['sv']) ? $arrclient['sv'] : '';
		}
		
		//如果有wap获取到的UA数据
		if(!empty($arrwapua) && is_array($arrwapua) && isset($arrwapua['product_info']['brand_name']) && $arrwapua['product_info']['brand_name']!='generic web browser') {
			//品牌
			$mobileinfo['wapua']['brand'] = isset($arrwapua['product_info']['brand_name']) ? $arrwapua['product_info']['brand_name']: '';
			//型号
			$mobileinfo['wapua']['model'] = isset($arrwapua['product_info']['model_name']) ? $arrwapua['product_info']['model_name'] : '';
			//分辨率宽度
			$mobileinfo['wapua']['resoluwidth'] = isset($arrwapua['display']['resolution_width']) ? $arrwapua['display']['resolution_width'] : '';
			//分辨率高度
			$mobileinfo['wapua']['resoluheight'] = isset($arrwapua['display']['resolution_height']) ? $arrwapua['display']['resolution_height'] : '';
			//手机系统
			$mobileinfo['wapua']['os'] = isset($arrwapua['product_info']['device_os']) ? $arrwapua['product_info']['device_os'] : '';
			//系统版本
			$mobileinfo['wapua']['osversion'] = isset($arrwapua['product_info']['device_os_version']) ? $arrwapua['product_info']['device_os_version'] : '';

			//排除从通用库里取出的手机信息
			if(strpos(strtolower($mobileinfo['wapua']['brand']), 'generic')>=0) {
				$mobileinfo['wapua']['brand'] = '';
			}
		}
		
		return $mobileinfo;
	}

	/**
	 * 生成老平台附带的用户登录参数
	 * @param string $url	需要跳转的URL
	 * @param array $arrparam	附加的参数
	 * @return string 返回需要前往的URL，附带参数
	 */
	public static function go_oldplat_userpram($url = '' ,$arrparam=array()) {
		//设置密钥
		$key = '87299596b8d9d642922a9a659aa70723';
		//定义要附加的参数
		$addparam = '';
		if(!empty($arrparam) && is_array($arrparam)) {
			//如果在参数表中已经附加了uid、ts、verifystring参数(原站点带入)，则重组后返回
			if(isset($arrparam['uid']) && isset($arrparam['ts']) && isset($arrparam['verifystring'])) {
				//组合参数串排除用于原平台附带的验证参数，生成新验证参数
				foreach ($arrparam as $k=>$v) {
					if ($k != 'uid' && $k != 'ts' && $k != 'verifystring') {
						$addparam = $addparam ? ($k.'='.$v) : ('&'.$k.'='.$v);
					}
				}

				//验证是否合法
				if($arrparam['verifystring'] == md5('uid='.$arrparam['uid'].'&ts='.$arrparam['ts'].'&key='.$key)) {
					$ts = time();
					$pram =  'uid='.$arrparam['uid'].'&ts='.$ts.'&verifystring=';
					$pram .= md5('uid='.$arrparam['uid'].'&ts='.$ts.'&key='.$key);
					return ($url.'?'.$pram).($addparam ? '&'.$addparam : $addparam);
				}
			} else {
				//组合参数串
				foreach ($arrparam as $k=>$v) {
					$addparam = $addparam ? ($k.'='.$v) : ('&'.$k.'='.$v);
				}
			}
		}
		
		//检查用户是否登录
		if(self::islogin()) {
			//默写密钥(KEY是固定的）
			$weuid = self::getweuid();
			if($weuid > 0) {
				$ts = time();
				$pram =  'uid='.$weuid.'&ts='.$ts.'&verifystring=';
				$pram .= md5('uid='.$weuid.'&ts='.$ts.'&key='.$key);
				return ($url.'?'.$pram).($addparam ? '&'.$addparam : $addparam);
			} else {
				return $url.($addparam ? '?'.$addparam : $addparam);
			}
		} else {
			return $url.($addparam ? '?'.$addparam : $addparam);
		}
	}
	
	/**
	* 加密用户token
	* @param string $str
 	* @param string $weuid 用户ID
 	* @param string $nickname 昵称
	* @param string $pid 平台ID
 	* @param string $puid 平台那边的用户ID
	* @return string 加密后的串
	*/
	private static function tokenEncrypt($weuid,$nickname,$pid,$puid) {
		$tokenObj = new token(TOKEN_CKEY, TOKEN_TKEY);
		$time = time();
		if($puid=='') $puid=0;
		return $tokenObj->encypt_data($weuid, $pid, $puid, $time, $nickname);
	}
	
	/**
	 * 解密用户token
	 * @param string $token
	 * @return array 解密后的数组
	 */
	private static function tokenDecrypt($token) {	
		$tokenObj = new token(TOKEN_CKEY, TOKEN_TKEY);
		
		// 解密数据
		$arrToken = $tokenObj->decrypt_data($token);
		$newtoken = array();
		//由于客户端将tk参数进行了二次编码，导致无法解开token的情况，问题修复前，检查不合法后进行二次解码
		if(empty($arrToken) || !is_array($arrToken) || !isset($arrToken['uid']) || $arrToken['uid']==0) {
			$arrToken = $tokenObj->decrypt_data(urldecode($token));
		}
		//由于客户端编码的原因，直接从接口获取数据
		if(!empty($arrToken) && is_array($arrToken) && isset($arrToken['uid']) && $arrToken['uid']>0) {
			$api_interface = new api_interface();
			$userinfo = json_decode($api_interface -> getinfo($arrToken['uid'],'weuid,username,nickname'),true);			
			$api_interface = null;
			if(!empty($userinfo) && is_array($userinfo) && isset($userinfo['status']) && $userinfo['status']==0) {
				$newtoken['uid'] = $userinfo['weuid'];
				$newtoken['platId'] = gbk2utf($arrToken['platId']);
				$newtoken['puId'] = gbk2utf($arrToken['puId']);
				$newtoken['time'] = time();
				$newtoken['userName'] = $userinfo['username'];
			}
		}
		
		if(empty($newtoken) || !is_array($newtoken)) {
			$newtoken = $arrToken;
		}
		
		/*//将串转为utf8（跳转进来的tk可能编码不一致[可能是GBK编码]）
		if(!empty($arrToken) || is_array($arrToken)) {
			foreach ($arrToken as $k=>$v) {
				$arrToken[$k] = gbk2utf($v);
			}
		}*/
		//var_dump($arrToken,$newtoken);die;
		return $newtoken;
	}

	/**
	 * 生成用户登录标识
	 * @param int $weuid	游戏ID
	 * @param string $username	用户名
	 * @param string $guid	GUID
	 * @param int $platid	平台标识
	 * @param int $clientid		渠道标识
	 * @return string	AES加密后的字符串
	 */
	private static function sidEncrypt($weuid ,$username ,$guid='' ,$platid=0 ,$clientid=0) {
		//加载AES组件
		
		//级成JSON
		//$json = '{"weuid":'.$weuid.',"guid":"'.$guid.'","nickname":"'.$nickname.'","platid":'.$platid.',"clientid":'.$clientid.',"time":'.time().'}';
		//拼JSON有时候会成乱码SID解码时会出错，直接使用数组，再转无问题
		$arr = array('weuid'=>$weuid,'guid'=>$guid,'username'=>$username,'platid'=>$platid,'clientid'=>$clientid,'time'=>time());
		
		//加密
		//exit($json);
		/*原加密方法，出现乱码
		require_once SROOT.'/source/api/aes2.php';
		$aes = new aes2(AES_SIDKEY);
		$sid = $aes -> encrypt(base64_encode($json));
		$aes = null;
		*/
		/*
		//使用新的AES类
		require SROOT.'/source/api/aes3.php';
		require SROOT.'/source/api/aesctr.php';
		$sid = AesCtr::encrypt(json_encode($arr), AES_SIDKEY, 256);
		*/
		$des = new des(DES_SIDKEY);
		self::$_sid = $des->encrypt(json_encode($arr));
		return self::$_sid;
	}
	
	/**
	 * 解密用户登录信息
	 * @param string $sid 用户加密串
	 * @return array
	 */
	private static function sidDecrype($sid) {
		//加载AES组件 
		
		//require SROOT.'/source/api/aes3.php';
		//require SROOT.'/source/api/aesctr.php';
		
		
		/*原加密方法有时会有乱码
		require_once SROOT.'/source/api/aes2.php';
		$aes = new aes2(AES_SIDKEY);
		$ensid = $json = $aes -> decrypt($sid);
		$aes = null;
		*/
		/*
		//使用新的AES类
		require SROOT.'/source/api/aes3.php';
		require SROOT.'/source/api/aesctr.php';
		

		$ensid = AesCtr::decrypt($sid, AES_SIDKEY, 256);

		return json_decode($ensid,true);
		*/
		$sid_arr = array();
		if ($sid) {
			$des = new des(DES_SIDKEY);
			$sid_info = $des->decrypt(urldecode($sid));
			$sid_arr = json_decode($sid_info,true);
		}
		return $sid_arr;
	}
	
	/**
	 * 检查是否正常返回
	 * @param string $json	返回数据
	 * @param string $arr	返回数据转换成数组
	 * @return boolean
	 */
	private static function interfacebackCheck(&$json, &$arr=array()) {
		$arr = json_decode($json,true);		
		if(empty($arr) || !is_array($arr) || !isset($arr['uid'])) {			
			return false;
		} else {
			return true;
		}
	}
}
?>