<?php
/**
 * 游戏信息请求类
 * @package application
 * @since 1.0.0 (2013-07-17)
 * @version 1.0.0 (2013-07-17)
 * @author jun <huanghaijun@mykj.com>
 */

defined('SROOT') || define('SROOT', dirname(dirname(__DIR__)));
//日志path
defined('LOG') || define('LOG', SROOT.'/data/log/system/');

class gameapp {
	/**
	 * 请求gameapp通知服务器url的接口url
	 * @var tring
	 */
	const REQURL = 'http://interface.mykj.com/data.php?method=appurl&format=json';
	
    /**
	 * 单例对象
	 * @var object
	 */
	static $gameapp = null;
	
	/**
	 * 日志级别(0不写日志，1只写错误日志，2写错误及请求日志)
	 * @var int
	 */
    private $_loglevel = 2;
    
    /**
     * 使用礼包后到用户得到的道具信息
     * @var unknown
     */
    static $_pInfo = array();
    
    /**
     * 用户uid
     * @var int
     */
    private $_uid = 0;
    
    /**
     * 游戏gid
     * @var int
     */
    private $_gid = 0;
    
    /**
     * 接口验证签字token
     * @var String
     */
    private $_token = '';

    /**
	 * 单例模式，防止对象在外面被new出来，构造函数
	 */
	private function __construct() {}

	/**
	 * 单例模式，防止对象被克隆
	 */
    private function __clone() {}
    
    /**
     * 初始化
     * @param int $uid	用户id
     * @param int $gid	游戏id
     * @param string $token	登陆token(预留)
     * @return object
     */
	static public function init($uid = 0, $gid = 0, $token = '') {
		if(!isset(self::$gameapp)) {
			self::$gameapp = new gameapp();
		}
        if(!empty($uid)) {
            self::$gameapp->setUid($uid);
        }
        if(!empty($gid)) {
            self::$gameapp->setGid($gid);
        }
        if($token !== '') {
            self::$gameapp->setToken($token);
        }
		return self::$gameapp;
	}
	
    /**
     * 设置用户UID
     * @param int $uid
     * @return object
     */
    public function setUid($uid) {
        $this->_uid = $uid;
        return $this;
    }

    /**
     * 设置用户UID
     * @param int $gid
     * @return object
     */
    public function setGid($gid) {
        $this->_gid = $gid;
        return $this;
    }
    
    /**
     * 修改用户状态
     * @param unknown $value	1:禁止聊天,2:禁止喇叭,3:禁止互动道具
     * @param number $expi	封禁到何时
     * @param number $type	20
     * @return Ambigous <multitype:number string , unknown, multitype:>
     */
    public function modifyuserstate($value, $expi = '', $type = 20) {
    	$param = array(
    			'uid'=>$this->_uid
    			,'value'=>$value
    			,'type'=>$type
    			,'expi'=>$expi
    	);
    	return $this->_run('ModifyUserState.app', $param, 'get');
    }
    
    /**
     * 获取用户所在房间
     * @return Ambigous <multitype:number string , unknown, multitype:>
     */
    public function getUserPath() {
    	$param = array(
    			'uid'=>$this->_uid
    	);
    	return $this->_run('GetUserPath.app', $param, 'get');
    }
    
    /**
     * 踢出用户
     * @return Ambigous <multitype:number string , unknown, multitype:>
     */
    public function kickUser(){
    	$roomInfo = $this->getUserPath();
    
    	if (!isset($roomInfo['status']) || $roomInfo['status'] != 0 || !isset($roomInfo['roomid'])) {
    		return false;
    	}
    	 
    	$param = array(
    			'uid'=>$this->_uid
    			,'roomid'=>$roomInfo['roomid'],
    	);
    	return $this->_run('KickUser.app', $param, 'get');
    }

    /**
     * 设置用户TOKEN
     * @param string $token
     * @return object
     */
    public function setToken($token) {
        $this->_token = $token;
        return $this;
    }

    /**
     * 获取用户游戏相关属性
     * @param type $type 查询类型(0－乐豆, 1－积分, 2－胜局, 3－输局, 4－和局, 5－逃局, 最后登录IP, 6－最后登录日期)
     * @return array
     */
    public function getUserInfo($type = 0) {
        $param = array(
            'uid'=>$this->_uid,
            'type'=>$type
		);
        return $this->_run('GetUserInfo.app', $param, 'get');
    }


    
    /**
     * 修改用户属性（增，改）
     * @param int $type 增加修改的属性类型: 0－乐豆, 1－积分, 2－胜局, 3－输局, 4－和局, 5－逃局, 6－最后登录日期, 7－玩游戏时间（秒）
     * @param int $count	数量正数表示增加， 负数表示减少
   	 * @param $item 活动id,WEB自定义
     * @param $bill 流水大类型DB分配置, 对应着type_gold配置表
     * @return array
     */
	 public function modifyUserInfo($type = 0, $count,  $item , $bill = 5) {
	     $param = array(
		     'uid' => $this->_uid,
			 'type' => $type,
			 'para'=> $count,
	     	 'bill'=>$bill,
	     	 'item'=>$item
		 );
		 return $this->_run('ModifyUserInfo.app', $param, 'get');
	 }
	 
	 public function getUserLastOnlineTimes(){
	 	$param = array(
	 		'uid' => $this->_uid
	 	);
	 	return $this->_run('GetUserLastOnlineTimes.app', $param, 'get');
	 }
	 
	 /**
	  * 通知用户属性刷新
	  */
	 public function refreshUserInfo() {
	 	$param = array(
	 			'uid'=>$this->_uid,
	 	);
	 	return $this->_run('RefreshUserInfo.app', $param, 'get');
	 }
	 
	 /**
	  * 通知用户属性刷新
	  */
	 public function queryUserMail() {
	 	$param = array(
	 			'uid'=>$this->_uid,
	 	);
	 	return $this->_run('QueryUserMail.app', $param, 'get');
	 }
	
	 public function deleteUserMail($expi) {
	 	$param = array(
	 			'uid'=>$this->_uid,
	 			'expi'=>$expi,
	 	);
	 	return $this->_run('DeleteUserMail.app', $param, 'get');
	 }
	 
	 /**
	  * 获取游戏币操作
	  */
	 public function getUserBean() {
	 	$bean = 0;
	 	$u = $this->getUserInfo();
	 	if(isset($u['count'])) {
	 		$bean = (int)$u['count'];
	 	}
	 	return $bean;
	 }
	 
	 /**
	  * 扣除用户游戏币
	  * @param int $count	扣除的数量
   	  * @param $item 活动id,WEB自定义
      * @param $bill 流水大类型DB分配置, 对应着type_gold配置表
	  * @return boolean
	  */
	 public function delUserBean($count = 0, $item , $bill = 12) {
	 	$back = $this->modifyUserInfo(0, -$count, $item , $bill);
	 	if(isset($back['status']) && $back['status'] == 0) {
	 		return true;
	 	} else {
	 		return false;
	 	}
	 }
	 
	 /**
	  * 添加用户乐豆
	  * @param int $count
   	  * @param $item 活动id,WEB自定义
      * @param $bill 流水大类型DB分配置, 对应着type_gold配置表
	  * @return boolean
	  */
	 public function addUserBean($count = 0, $item , $bill = 12) {
	 	$back = $this->modifyUserInfo(0, $count, $item , $bill);
	 	if(isset($back['status']) && $back['status'] == 0) {
	 		return true;
	 	} else {
	 		return false;
	 	}	
	 }

    /**
     * 支付通知
     * @param int $succeed
     * @param int $touid 接受人编号(不填默认与用户ID标识一样)
     * @param int $payid 支付方式
     * @param int $pseudo 手机支付伪码
     * @param int $clientid 渠道号
     * @param int $clientsubid 子渠道号
     * @param int $money 用户剩余本位币数据
     * @param int $recharge 本次充值本位币
     * @param string $serialNumber 定单流水号
     * @param int $wareid 商品ID标识
     * @param int $count 商品数量信息
     * @param string $ordernumber 订单号
     * @param int $result 0表示成功，1表示失败
     * @return array
     */
    public function payNotice($succeed, $touid, $payid, $pseudo, $clientid, $clientsubid, $money, $recharge, $serialNumber, $wareid, $count, $ordernumber, $result) {
        $param = array(
            'uid'=>$this->_uid,
            'succeed'=>$succeed,
            'touid'=>$touid,
            'payid'=>$payid,
            'pseudo'=>$pseudo,
            'clientid'=>$clientid,
            'clientsubid'=>$clientsubid,
            'money'=>$money,
            'recharge'=>$recharge,
            'serialnumber'=>$serialNumber,
            'wareid'=>$wareid,
            'count'=>$count,
            'ordernumber'=>$ordernumber,
            'result'=>$result,
		);
        return $this->_run('PayNotice.app', $param, 'get');
    }

    /**
     * 封号通知
     * @param string $desc 封号原因
     * @return array
     */
    public function closeAccount($desc) {
        $param = array(
            'uid'=>$this->_uid,
            'desc'=>$desc
		);
        return $this->_run('CloseAccount.app', $param, 'get');
    }

    /**
     * 大厅配置更新
     * @param int $ld 大厅ID(不填则更新所有), tbus的ID, 格式一个ip地址，如果字符里出现*表示刷新所有房间，#表示大厅，不需要传值
     * @param int $mode	需要更新的模块id，支持扩展
							0大厅配置（对应lobby表）
							1奖品配置（推广员）
							2礼包配置（不使用）
							3道具配置（对应prop prop_cast goods goods_ext gifts gifts_ext）
							4列表配置（对应node）
							5系统消息配置（对应msg）
							6 抽奖机配置
							7python脚本
	 * @param int $p1	可携带参数 1，mode(0-6)不需要，以后扩展用
	 * @param int $p2	可携带参数 2，mode(0-6)不需要，以后扩展用
	 * @param int $confirm	是否需要等待配置重载完成后返回，还是接到通知后立即返回，1为等待，0为立即返回。默认为0 
     * @return array|bool
     */
    public function updateLobbyConfig($ld = '', $mode = 7, $p1 = 0, $p2 = 0, $confirm = 1) {
        $param = array(
            'mode'=>$mode,
            'p1'=>$p1,
            'p2'=>$p2,
        	'confirm'=>$confirm
		);
        //$Id是一个正确的ip地址
		if(filter_var($ld, FILTER_VALIDATE_IP) !== false) {
			$param['ld'] = $ld;
		};   
        
        return $this->_run('UpdateLobbyConfig.app', $param, 'get');
    }

    /**
     * 房间配置更新
     * @param int $rid (不填则更新所有), tbus的ID, 格式一个ip地址，如果字符里出现*表示刷新所有房间，#表示大厅，不需要传值
     * @param int $mode	需要更新的模块id，支持扩展
							0房间配置（对应room表）
							1奖品配置（对应room_ext）
							2PY脚本
	* @param int $p1	可携带参数 1 Mode = 1时，需要携带改变的room_ext记录的id
	 * @param int $p2 可携带参数 2，mode(0-6)不需要，以后扩展用
     * @param int $confirm 同步或异步调用（是否需要等待配置重载完成后返回，还是接到通知后立即返回，1为等待，0为立即返回。默认为0）
     * @return array
     */
    public function updateRoomConfig($rid = '', $mode = 2, $p1= 0, $p2 = 0, $confirm = 1) {
        $param = array(
            'mode'=>$mode,
        	'p1'=>$p1,
        	'p2'=>$p2,
            'confirm'=>$confirm
		);
        //$rid是一个正确的ip地址
        if(filter_var($rid, FILTER_VALIDATE_IP) !== false) {
        	$param['rid'] = $rid;
        };
        return $this->_run('UpdateRoomConfig.app', $param, 'get');
    }

    /**
     * 列表配置更改
     * @param int $lid 大厅ID
     * @param int $gameid 游戏ID
     * @param int $confirm 是否需要等待配置重载完成后返回，还是接到通知后立即返回，1为等待，0为立即返回。默认为0
     * @return type
     */
    public function updateListConfig($lid, $gameid, $confirm = 0) {
        $param = array(
            'lid'=>$lid,
            'gameid'=>$gameid,
            'confirm'=>$confirm
		);
        return $this->_run('UpdateListConfig.app', $param, 'get');
    }

    /**
     * 修改用户道具(增， 删，改)
     * @param $pid 道具ID
     * @param $count 道具数量
     * @param $expi 道具到期时间，unix时间戳, 默认为0
     * @param $kid 道具类型 0道具1商品2礼包
     * @param $bill 流水大类型DB分配置, 对应着type_gold配置表
     * @param $item 活动id,WEB自定义
     * @return array
     */
    public function modifyUserProp($pid, $count, $expi = 0, $kid = 0, $bill = 7, $item = 0) {
        $param = array(
            'uid'=>$this->_uid,
            'pid'=>$pid,
            'count'=>$count,
            'expi'=>$expi,
            'kid'=>$kid,
        	'bill'=>$bill,
        	'item'=>$item
        );
        return $this->_run('ModifyUserProp.app', $param, 'get');
    }
    
    /**
     * 修改用户道具新(增， 删，改)
     * @param $pid 道具ID
     * @param $count 道具数量
     * @param $expi 道具到期时间，unix时间戳, 默认为0
     * @param $kid 道具类型 0道具1商品2礼包
     * @param $bill 流水大类型DB分配置, 对应着type_gold配置表
     * @param $item 活动id,WEB自定义
     * @return array
     */
    public function modifyUserPropV1($pid, $count, $expi = 0, $kid = 0, $desc, $bill = 7, $item = 0) {
    	$param = array(
    			'uid'=>$this->_uid,
    			'pid'=>$pid,
    			'count'=>$count,
    			'expi'=>$expi,
    			'kid'=>$kid,
    			'bill'=>$bill,
    			'item'=>$item,
    			'desc'=>$desc,
    	);
    	return $this->_run('ModifyUserProp.app', $param, 'get');
    }
    

    /**
     * 获取用户背包道具信息
     * @return array|bool
     */
    public function getUserProp() {
        $param = array(
            'uid'=>$this->_uid,
        );
        return $this->_run('GetUserProp.app', $param, 'get');
    }
    
    /**
     * 根据道具id获取道具
     * @param int $arr_pid 道具id支持多个, 例如：array(403, 402)
     * @return array
     */
    public function getUserPropByPid($arrpid) {
    	$prop = array();
    	//根据道具id获取用户道具信息
    	$prop_list = $this->getUserProp();
    	if(is_array($arrpid) && count($arrpid)) { 
    		foreach($arrpid as $val) {
		    	if(isset($prop_list['data'])) {
		    		$prop[$val]['count']  = 0;
		    		foreach($prop_list['data'] as $v) {
		    			if($v['pid'] == $val) {
		    				$prop[$val]['uid'] = $v['uid'];
		    				$prop[$val]['pid'] = $v['pid'];
		    				$prop[$val]['count'] += (int)$v['count'];
		    			}	
		    		}
		    	}
	    	}
    	}
    	return $prop;
    }
    
    /**
     * 批量删除道具
     * @param array $pidlist 删除道具列表，格式：array(id=>count)
     * @param $item 活动id,WEB自定义
     * @param $bill 流水大类型DB分配置, 对应着type_bag配置表
     * @param $kid 道具类型 0-道具,1-商品,2-礼包
     * @return boolean
     */
    public function delUserProp($pidlist, $item, $bill = 12, $kid = 0 ) {
    	$status = false;
    	if(is_array($pidlist) && count($pidlist)) {
    		$flag = 0;
    		foreach($pidlist as $id=>$count) {
    			$back = $this->modifyUserProp($id, -$count, 0, $kid, $bill, $item);
    			if($back['status'] == 0) {
    				$flag++;
    			}
    		}
    		if($flag == count($pidlist)) {
    			$status = true;
    		}
    	}
    	return $status; 	
    }
    
    /**
     * 批量添加道具
     * @param array $pidlist 删除道具列表，格式：array(id=>array('count'=>数量, 'type'=>类型,'expi'=>有效期))
     * @param $item 活动id,WEB自定义
     * @param $bill 流水大类型DB分配置, 对应着type_bag配置表
     * @param $kid 道具类型 0-道具,1-商品,2-礼包
     * @return boolean
     */
    public function addUserProp($pidlist, $item, $bill = 7, $kid = 0) {
    	$status = false;
    	if(is_array($pidlist) && count($pidlist)) {
    		$flag = 0;
    		foreach($pidlist as $id=>$p) {
    			if(isset($p['count']) && isset($p['type'])) {
    				$expi = isset($p['expi']) ? $p['expi'] : 0;
	    			$back = $this->modifyUserProp($id, $p['count'], $expi, $p['type'], $bill, $item);

	    			// 获取道具抽奖信息
	    			gameapp::$_pInfo = isset($back['propinfo']) ? json_decode(urldecode($back['propinfo']), true) : NULL;
	    		    
	    			if($back['status'] == 0) {
	    				$flag++;
	    			}
    			}
    		}
    		if($flag == count($pidlist)) {
    			$status = true;
    		}
    	}
    	return $status;
    }
    
    /**
     * 添加奖池奖品(后台使用）
	 * @param int $prizeid	奖池id
     * @param int $goodsid	奖池物品id
     * @param int $count	奖池物品数量
     * @param date $date 日期：例如2013-11-28
     * @param int $item 特殊的奖品类型才有对应的item的默认为0， 例如实物奖励对应为1
     * @return array 格式：array('status'=>0, 'Prizeid'=>11, 'Count'=>10))
     */
    public function addPrize($prizeid, $goodsid, $count, $date, $item = 0) {
    	$param = array(
    			'PrizeID'=>$prizeid,
    			'Item'=>$item,
    			'GoodsID'=>$goodsid,
    			'Count'=>$count,
    			'Date'=>$date
    	);
    	return $this->_run('AddPrize.app', $param, 'get');
    }

    /**
     * 增加(修改操作)奖池（后台修改)，必须调用了addPrize才能掉这个修改功能
     * @param int $prizeid	奖池id
     * @param int $goodsid	奖池物品id
     * @param int $count	奖池物品数量
     * @param date $date 日期：例如2013-11-28
     * @param int $item 特殊的奖品类型才有对应的item的默认为0， 例如实物奖励对应为1
     * @return array 格式：array('status'=>0, 'Prizeid'=>11, 'Count'=>10))
     */
    public function increasePrize($prizeid, $goodsid, $count, $date, $item = 0) {
		$param = array(
			 'PrizeID'=>$prizeid,
			 'Item'=>$item,
			 'GoodsID'=>$goodsid,
			 'Count'=>$count,
			 'Date'=>$date
        );
        return $this->_run('IncreasePrize.app', $param, 'get');
    }

    /**
     * 减少（修改操作）奖池奖品（活动奖池操作）
  	 * @param int $prizeid	奖池id
     * @param int $goodsid	奖池物品id
     * @param int $count	奖池物品数量
     * @param date $date 日期：例如2013-11-28
     * @param int $item 特殊的奖品类型才有对应的item的默认为0， 例如实物奖励对应为1
     * @return array 格式：array('status'=>0, 'Prizeid'=>11, 'Count'=>10))
     */
    public function decreasePrize($prizeid, $goodsid, $count =1, $date, $item = 0) {
        $param = array(
            'PrizeID'=>$prizeid,
        	'Item'=>$item,
            'GoodsID'=>$goodsid,
            'Count'=>$count,
            'Date'=>$date
        );
        return $this->_run('DecreasePrize.app', $param, 'get');
    }

    /**
     * 删除清除奖池里奖品（后台使用）
     * @param int $prizeid	奖池id
     * @param int $goodsid	奖池物品id
     * @param date $date 日期：例如2013-11-28
     * @param int $item 特殊的奖品类型才有对应的item的默认为0， 例如实物奖励对应为1
     * @return array 格式：array('status'=>0, 'Prizeid'=>11, 'Count'=>10))
     */
    public function deletePrize($prizeid, $goodsid, $date, $item = 0) {
        $param = array(
            'PrizeID'=>$prizeid,
            'Item'=>$item,
            'GoodsID'=>$goodsid,
            'Date'=>$date
        );
        return $this->_run('DeletePrize.app', $param, 'get');
    }

    /**
     * 查询奖池奖品
     * @param int $prizeid	奖池id
     * @param string $date 默认0表示不根据时间查
     * @param int $goodsid 默认0表示所有物品
     * @param int $item 默认0表示所非特殊类物品
     * @return array
     */
    public function queryPrize($prizeid, $date = 0, $goodsid = 0, $item = 0) {
       $param = array(
            'PrizeID'=>$prizeid,
            'Item'=>$item,
            'GoodsID'=>$goodsid,
            'Date'=>$date
        );
        $return = $this->_run('QueryPrize.app', $param, 'get');
        if(isset($return['bonus']) && $return['bonus'] != '') {
            $return['bonus'] = json_decode(urldecode($return['bonus']), true);
        }
        return $return;
    }

    /**
     * 登陆公告更新
     */
    public function updateLoginAnc() {
        return $this->_run('UpdateLoginAnc.app', array(), 'get');
    }

    /**
     * 游戏公告更新
     */
    public function updateGameAnc() {
        return $this->_run('UpdateGameAnc.app', array(), 'get');
    }

    /**
     * 全局广播消息更新
     */
    public function globalMessage($msg) {
        $param = array(
            'client_type'=>0,
            'game_id'=>$this->_gid,
            'msg'=>$msg,
        );
        return $this->_run('GlobalMessage.app', $param, 'get');
    }

    /**
     * 喜报消息更新
     */
    public function goodMessage($msg) {
        $param = array(
            'client_type'=>0,
            'game_id'=>$this->_gid,
            'msg'=>$msg,
        );
        return $this->_run('GoodMessage.app', $param, 'get', 30, true);
    }

    /**
     * 发送个人消息
     */
    public function personalMessage($pid, $num, $time, $msg, $fsize = 1, $fcolor = 0) {
        $param = array(
            'uid'=>$this->_uid,
            'msg'=>$msg,
            'fontsize'=>$fsize,
            'fontcolor'=>$fcolor,
        	'drid'=>0,
        	'mtype'=>0,
        	'pid'=>$pid,
        	'num'=>$num,
        	'time'=>$time,
        );
        return $this->_run('PersonalMessage.app', $param, 'get', 30, true);
    }

    /**
     * 获取推广员下线列表
     */
    public function getPromotedList() {
        $param = array(
            'uid'=>$this->_uid
        );
        return $this->_run('GetPromotedList.app', $param, 'get');
    }

    /**
     * 获取推广员领奖状态
     * @param type $euid 被推广用户标识
     */
    public function getPromoteStatus($euid) {
        $param = array(
            'uid'=>$this->_uid,
            'euid'=>$euid
        );
        return $this->_run('GetPromoteStatus.app', $param, 'get');
    }

    /**
     * 推广员领奖
     * @param type $euid 被推广用户标识
     * @param type $condid 满足条件标识
     */
    public function getPromoteGift($euid, $condid) {
        $param = array(
            'uid'=>$this->_uid,
            'euid'=>$euid,
            'condid'=>$condid
        );
        return $this->_run('GetPromoteGift.app', $param, 'get');
    }
    
    /**
     * 彩金池重置通知
     * @param int $hanselid 彩金池ID，不填则更新所有
     * @param int $confirm 是否需要等待配置重载完成后返回，1为等待，0为立即返回。默认为0
     */
    public function resetHansel($hanselid, $confirm = 0) {
        $param = array(
            'hanselid'=>$hanselid,
            'confirm'=>$confirm
        );
        return $this->_run('ResetHansel.app', $param, 'get');
    }
    
    /**
     * 彩金池配置修改通知
     * @param int $hanselid 彩金池ID，不填则更新所有
     * @param int $confirm 是否需要等待配置重载完成后返回，1为等待，0为立即返回。默认为0
     */
    public function modifyHansel($hanselid, $confirm=0) {
        $param = array(
            'anselid'=>$hanselid,
            'confirm'=>$confirm
        );
        return $this->_run('ModifyHansel.app', $param, 'get');
    }
    
    /**
     * 彩金池配置删除
     * @param int $hanselid 彩金池ID，不填则更新所有
     * @param int $confirm 是否需要等待配置重载完成后返回，1为等待，0为立即返回。默认为0
     */
    public function deleteHansel($hanselid, $confirm=0) {
        $param = array(
            'hanselid'=>$hanselid,
            'confirm'=>$confirm
        );
        return $this->_run('DeleteHansel.app', $param, 'get');
    }
    
    /**
     * 彩金池金额查询
     * @param int $hanselid 彩金池ID，不填则更新所有
     * @param int $confirm 是否需要等待配置重载完成后返回，1为等待，0为立即返回。默认为0
     */
    public function queryHanselAmount($hanselid, $confirm=0) {
        $param = array(
            'hanselid'=>$hanselid,
            'confirm'=>$confirm
        );
        return $this->_run('QueryHanselAmount.app', $param, 'get');
    }
    
    /**
     * 彩金池金额修改
     * @param int $hanselid 彩金池ID，不填则更新所有
     * @param int $amount 新的彩金池内金额
     * @param int $confirm 是否需要等待配置重载完成后返回，1为等待，0为立即返回。默认为0
     */
    public function updateHanselAmount($hanselid, $amount, $confirm=0) {
        $param = array(
            'hanselid'=>$hanselid,
            'amount'=>$amount,
            'confirm'=>$confirm
        );
        return $this->_run('UpdateHanselAmount.app', $param, 'get');
    }

    /**
     * HTTP请求处理流程函数
     * @param string $api 需要调用的接口
     * @param xml/string/array $param 调用的参数
     * @param string $method 传值方式
     */
    private function _run($api, $param=array(), $method='post', $timeout = 2,  $urlencode = false) {
        //获取到IP+port地址集
        $url_list = $this->_getGameAppUrl();
        $ips = isset($url_list[3]) ? $url_list[3] : array();	   //-web, 2-gas, 3-game 
        	
        // 兼容gid
        $gid = $this->_gid == 0 ? 44 : $this->_gid;
        
		if(!isset($ips[$gid][$api])){
            return array('status'=>1, 'msg'=>'game缓存'.$api.'接口ip和port组成url没有找到！');
        }
        $_ips = $ips[$gid][$api]; //一个真正的IP集 
        shuffle($_ips);
        //组装传递的参数， 注意http_build_query函数组装出来的参数是urlencode的，所以要转一下才能返回不乱码
        if($urlencode == false) {
        	$query = is_array($param) ? urldecode(http_build_query($param)) : $param;
        } else {
        	$query = is_array($param) ? http_build_query($param) : $param;
        }
        foreach($_ips as $_ip){
            $url = 'http://'.$_ip.'/'.$api;
            
/*             if ($api == 'DeleteUserMail.app') {
            	echo $url.'?'.$query;
            	exit;
            } */
            
            $rexml = (strtolower($method) == 'get') ? http::do_get($url.'?'.$query, $timeout) :http::do_post($url, $param, $timeout);
            
            $rearr = $this->_parseXml($rexml);  
            if($rearr !== false && isset($rearr['status']) && $rearr['status'] == 0) {
                break;
            }
        }
        //结果为error，这里需要商榷
        if(!$rearr) {
            return array('status'=>1, 'msg'=>'游戏服务器未找到！');
        }
        if($rearr['status'] == 0) { //success
            $this->_loglevel = 2;
        } else {
             $this->_loglevel = 1;
        }
        $this->_logsave( $this->_loglevel, $url.'?'.$query, $rexml);
        return $rearr;
    }

    /**
     * GAME HTTP请求日志记录
     * @param int $level 日志等级 //日志级别(0不写日志，1只写错误日志，2写错误及请求日志)
     * @param string $url 请求URL
     * @param string $return 返回值记录
     */
    private function _logsave($level, $url, $return='') {
        $rout = $level==2 ? 'gameapp' : 'gameapp_error';
         return $this->writeLog($rout, $url, $return);
    }

    /**
     * 解析app xml(此方法专门解析app返回的xml)
     * @param string $xml 默认GBK编码
     * @param string $encoding
     * @return array|bool
     */
    private function _parseXml($xml, $encoding='UTF-8') {
		//XML为空直接返回错误
		if(empty($xml)) {
			return false;
		}
        $xml = $this->_gbk2utf($xml);
		$encoding = strtoupper($encoding);
		$xml = trim($xml);
		if(strpos($xml,'<?xml')===false) {
			$xml = '<?xml version="1.0" encoding="'.$encoding.'"?>'.$xml;
		}

		$values = array();
		$index  = array();
		$array  = array();
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parse_into_struct($parser, $xml, $values, $index);
		xml_parser_free($parser);
		//无法取值直接返回错误
		if(empty($values)) {
			return false;
		}

		if(isset($values[0]['attributes'])) {
			$array = $values[0]['attributes'];
		}
		if(!isset($array['status'])) {
            return false;
		}
		for($i=1;$i<count($values);$i++) {
			if($values[$i]['type']=='complete'){
				$array['data'][] = $values[$i]['attributes'];
			}
		}
		return $array;
	}

	/**
	 * 获取gameapp请求url连接
	 * @return array
	 */
	private function _getGameAppUrl() {
		$urllist = array();
/* 		$back = json_decode(http::do_post(self::REQURL),true);
		if(isset($back['data'])) {
			$urllist = json_decode(urldecode($back['data']), true);
		}
		return $urllist; */
		return inter::init()->readRedisConfig('songplay:appurl');
	}
	
	/**
	 * GBK转UTF-8
	 * @param string $str
	 * @return string
	 */
	private function _gbk2utf($str) {
		if(!empty($str) && !$this->_isUtf8($str)){
			//$str = iconv('GBK', 'UTF-8', $str);
		}
		return $str;
	}
	
	/**
	 * 判断字符串是否utf8
	 * @param string $str
	 * @return boolean
	 */
	private function _isUtf8($str) {
		if (function_exists('mb_detect_encoding')) {
			return mb_detect_encoding($str, 'UTF-8', true);
		} else {
			return preg_match('/^.*$/u', $str) > 0;
		}
	}
	
	/**
	 * 写日志方法
	 * @param string $method
	 * @param string $para
	 * @param string $returnStr
	 * @return boolean
	 */
	private function  writeLog($method, $para, $returnStr) {
		$path = LOG . $method;		
		if (!file_exists(LOG)) {
			mkdir(LOG, 0777);
		}	
		if (!file_exists($path)) {
			mkdir($path, 0777);
		}
		$fileName = $path . '/' . date('Y-m-d') . '.log';		
		$fp = fopen($fileName, 'a+');
		// 判断创建或打开文件是否 创建或打开文件失败，请检查权限或者服务器忙;
		if ($fp === false) {
			return false;
		} else {
			if (fwrite($fp, '[TIME:' . date("Y-m-d H:i:s") . '] ---- [PARA:' . $para . '] ---- [RETURN: {' . $returnStr . "}]\r\n")) {
				fclose($fp);
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * 活动获取用户排行信息，这个接口是通用，不同活动$rank_id意义不同
	 * @param int $rank_id 排行id，不同活动意义不同
	 * @return array
	 */
	public function getRankInfo($rank_id = 0) {
		return $this->_run('GetRankInfo.app', array('RankID'=>$rank_id), 'get');
	}
	
	/**
	 * (世界杯活动)获取玩家状态---活动结束后可以删除
	 * @param array $param 格式：array('uid'=>用户ID,'type' => 类型id(传1))
	 * @return mixed
	 */
	public function getUserState($param = array()) {
		return $this->_run('GetUserState.app', $param, 'get');
	}
	
	/**
	 * (世界杯活动)修改玩家状态---活动结束后可以删除
	 * @param array $param 格式：array('uid'=>用户ID,'type' => 类型id(传1) ,'value'=>球队id)
	 * @return mixed
	 */
/* 	public function modifyUserState($param = array()) {
		return $this->_run('ModifyUserState.app', $param, 'get');
	} */
	
	/**
	 * (世界杯活动)查询各个球队支持者持有活动道具总数
	 * @return array
	 */
	public function QuerySJBPropCount() {
		return $this->_run('QuerySJBPropCount.app', array(), 'get');
	}
}
