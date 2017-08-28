<?php
/**
 * GAS信息请求类
 * @package application
 * @since 1.0.0 (2012-12-24)
 * @version 1.0.1 (2013-11-20)
 * @author jun <huanghaijun@mykj.com>
 */ 
defined('SROOT') || define('SROOT', dirname(dirname(__DIR__)));
//日志path
defined('LOG') || define('LOG', SROOT.'/data/log/system/');

class gasapp {
	/**
	 * 请求gasapp通知服务器url的接口url
	 * @var tring
	 */
	const REQURL = 'http://interface.mykj.com/data.php?method=appurl&format=json';
	/**
	 * 单例对象
	 * @var object
	 */
	static $gasapp = null;
	/**
	 * 当前请求app的url
	 * @var string
	 */
	private $_appurl;
    /**
     * 是否返回需要数据
     * @var boolean
     */
	private $_isreturn = true;
	/**
	 * 日志级别(0不写日志，1只写错误日志，2写错误及请求日志)
	 * @var int
	 */
    private $_loglevel = 2;
    /**
     * 用户uid
     * @var int
     */
    private $_uid;
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
     * 实列化对象
     * @param string $uid 用户UID
     * @param string $token 用户Token
     * @param int $isreturn 是否异步，默认同步
     * @return gasapp|object
     */
    static public function init($uid = '', $token = '', $isreturn = 1) {
		if(!isset(self::$gasapp)){
			self::$gasapp = new gasapp();
		}
        if($uid != '') {
            self::$gasapp->setUid($uid);
        }
        if($token != '') {
            self::$gasapp->setToken($token);
        }
        self::$gasapp->setIsreturn($isreturn);
		return self::$gasapp;
	}
    
	/**
     * POST请求是否需要返回数据
	 * @param boolean $isreturn
	 */
	public function setIsreturn($isreturn) {
		$this->_isreturn = $isreturn;
        return $this;
	}

    /**
     * 设置用户UID
     * @param type $uid
     */
    public function setUid($uid) {
        $this->_uid = $uid;
        return $this;
    }

    /**
     * 设置用户TOKEN
     * @param type $token
     */
    public function setToken($token) {
        $this->_token = $token;
        return $this;
    }
    
	/**
	 * GAS查询订单
	 * @param string $orderno 订单号
	 * @return array
	 */
	public function queryOrder($orderno, $so_number = 0) {
        $param = array(
            'order_id'=>$orderno
	       ,'so_number'=>$so_number
           ,'user_id' =>$this->_uid
           ,'token'   =>$this->_token
		);
        return $this->_run('QueryOrder', $param, 'get');
	}

	/**
     * 生成订单
     * @param int $pid 道具ID（如果是充值，这个字段填0）
     * @param int $count 数量（如果是充值，这个字段填0）
     * @param int $amount 金额，单位：分
     * @param int $channelid 渠道编号
     * @param int $subchannelid 子渠道编号
     * @param int $gameid 游戏ID
     * @param int $signtype 支付方式(这个字段由网关填写，wap方式填-1)
     * @param int $playtype 游戏玩法
     * @param string $ext 扩展字段
     * @return int
     */
	public function genOrder($pid, $count, $amount, $channelid, $subchannelid, $gameid, $signtype, $playtype = 0, $orderkey = 0, $gamever = 0, $os_id = 0, $ext = '') {
		$param = array(
				 'user_id'=>$this->_uid
				,'prop_id'=>$pid
				,'count'=>$count
				,'amount'=>$amount
				,'channel_id'=>$channelid
				,'sub_channel_id'=>$subchannelid
				,'game_id'=>$gameid
				,'sign_type'=>$signtype
				,'play_type'=>$playtype
				,'order_key'=>$orderkey
				,'order_key_len'=>strlen($orderkey)
				,'game_ver'=>$gamever
				,'os_id'=>$os_id
				,'ext'=>$ext
				,'token'=>$this->_token
		);
        return $this->_run('GenOrder', $param, 'get');
	}

	/**
	 * 通知GAS支付结果
	 * @param string $orderid	订单号
	 * @param string $charge_result	支付状态(0成功/-1失败)
	 * @param number $point_num	充值金额	单位：分
	 * @param string $sonumber	受理流水号
	 * @param number $payment	支付方式
	 * @param number $currency	币种
	 * @param string $pseudo	手机伪码
	 * @param string $cpserviceid	cpserviceid
	 * @param string $consumecode	消费代码	扩展字段
	 * @return array
	 */
	public function thirdPartyBuyNotify($orderid, $charge_result, $point_num = 0, $sonumber = 0, $payment = 0, $currency = 0, $pseudo = 0, $cpserviceid = 0, $consumecode = 0, $ext = '') {
		$param = array(
				'order_id'=>$orderid
				,'point_num'=>$point_num
				,'charge_result'=>$charge_result
				,'so_number'=>$sonumber
				,'payment'=>$payment
				,'currency'=>$currency
				,'pseudo'=>$pseudo
				,'cpserviceid'=>$cpserviceid
				,'consumecode'=>$consumecode
				,'ext'=>$ext				
				,'amount'=>0 //@TODO delete it

		);
        return $this->_run('ThirdPartyBuyNotify', $param, 'get');
	}

    /**
	 * 帐号密码登录
	 * @param  $account 帐号
	 * @param  $pwd 密码
	 * @param  $gid 游戏ID
	 * @param  $cid 客户端标识
	 * @param  $scid 子渠道
	 * @param  $cs 手机端类型标识 0-客户移动系列（默认）1-mtk系列2-拆包版本（配合移动大厅）其它保留使用，以PC字节序位准。4-定义为android系列
	 * @param  $mc 手机型号字符串
	 * @param  $pt 手机属性数据
	 * @param  $bcp 密码是否为密文，默认为密文 0-明文 1-密文
	 * @param  $pid 平台ID
	 * @param  $logonip 登陆ip
	 * @return array
	 */
	public function account( $account, $pwd, $gid = 100, $cid = 0, $scid = 0, $cs = '0', $mc = 'x', $pt = 'x',$bcp = 0 , $pid = 0,$logonip = '' ) {
		$param = array(
             'account'=>$account
            ,'pwd'=>$pwd
            ,'cid'=>$cid
            ,'scid'=>$scid
            ,'gid'=>$gid
            ,'mc'=>$mc
            ,'cs'=>$cs
            ,'pt'=>$pt
            ,'bcp'=>$bcp
            ,'pid'=>$pid
            ,'logonip'=>$logonip
		);
        return $this->_run('account.app', $param, 'get');
	}
	
	/**
	 * 新帐号密码登录
	 * @param  $account 帐号
	 * @param  $pwd 密码
	 * @param  $gid 游戏ID
	 * @param  $cid 客户端标识
	 * @param  $scid 子渠道
	 * @param  $cs 手机端类型标识 0-客户移动系列（默认）1-mtk系列2-拆包版本（配合移动大厅）其它保留使用，以PC字节序位准。4-定义为android系列
	 * @param  $mc 手机型号字符串
	 * @param  $pt 手机属性数据
	 * @param  $bcp 密码是否为密文，默认为密文 0-明文 1-密文
	 * @param  $pid 平台ID
	 * @param  $logonip 登陆ip	 
	 * @return array
	 */	  
	public function newaccount( $account, $pwd, $gid=100, $cid=0, $scid=0, $cs='0', $mc='x', $pt='x',$bcp=0 , $pid=0 ,$logonip='' ) {
		$param = array(
             'account'=>$account
            ,'pwd'=>$pwd
            ,'cid'=>$cid
            ,'scid'=>$scid
            ,'gid'=>$gid
            ,'mc'=>$mc
            ,'cs'=>$cs
            ,'pt'=>$pt
            ,'bcp'=>$bcp
            ,'pid'=>$pid
            ,'logonip'=>$logonip
		);
        return $this->_run('newaccount.app', $param, 'get');
	}
	
	/**
	 * 免注册登录
	 * @param  $account 帐号
	 * @param  $pwd 密码
	 * @param  $gid 游戏ID
	 * @param  $cid 客户端标识
	 * @param  $scid 子渠道
	 * @param  $cs 手机端类型标识 0-客户移动系列（默认）1-mtk系列2-拆包版本（配合移动大厅）其它保留使用，以PC字节序位准。4-定义为android系列
	 * @param  $mc 手机型号字符串
	 * @param  $pt 手机属性数据
	 * @param  $logonip 登陆ip	 
	 * @return array
	 */
	public function quick( $gid ,$mc, $cid, $cs, $scid , $pt, $logonip ) {
		$param = array(
             'gid'=>$gid
            ,'mc'=>$mc
            ,'cid'=>$cid
            ,'cs'=>$cs
            ,'scid'=>$scid
            ,'pt'=>$pt
            ,'logonip'=>$logonip
		);
        return $this->_run('quick.app', $param, 'get');
	}
		
	/**
	 * AT 登录
	 * @param  $token TOKEN
	 * @param  $gid 游戏ID
	 * @param  $cid 客户端标识
	 * @param  $scid 子渠道
	 * @param  $cs 手机端类型标识 0-客户移动系列（默认）1-mtk系列2-拆包版本（配合移动大厅）其它保留使用，以PC字节序位准。4-定义为android系列
	 * @param  $mc 手机型号字符串
	 * @param  $pt 手机属性数据
	 * @param  $logonip 登陆ip	 
	 * @return array
	 */
	public function at($gid = 100, $cid = 0, $scid = 0, $cs = '0', $mc = 'x', $pt = 'x', $logonip = '') {
		$param = array(
             'token'=>$this->_token
            ,'cid'=>$cid
            ,'scid'=>$scid
            ,'gid'=>$gid
            ,'mc'=>$mc
            ,'cs'=>$cs
            ,'pt'=>$pt
			,'logonip'=>$logonip
		);
        return $this->_run('at.app', $param, 'get');
	}
	/**
	 * Token 登录
	 * @param  $token TOKEN
	 * @param  $gid 游戏ID
	 * @param  $cid 客户端标识
	 * @param  $scid 子渠道
	 * @param  $cs 手机端类型标识 0-客户移动系列（默认）1-mtk系列2-拆包版本（配合移动大厅）其它保留使用，以PC字节序位准。4-定义为android系列
	 * @param  $mc 手机型号字符串
	 * @param  $pt 手机属性数据
	 * @param  $logonip 登陆ip	 
	 * @return array
	 */
	public function token($gid =100, $cid = 0, $scid = 0, $cs = '0', $mc = 'x', $pt = 'x', $logonip = '') {
		$param = array(
             'token'=>$this->_token
            ,'cid'=>$cid
            ,'scid'=>$scid
            ,'gid'=>$gid
            ,'mc'=>$mc
            ,'cs'=>$cs
            ,'pt'=>$pt
		);
        return $this->_run('token.app', $param, 'get');
	}

	/**
	 * 帐号注册
	 * @param  $uname 用户名
	 * @param  $pwd 密码
	 * @param  $email 邮箱
	 * @param  $nick 昵称
	 * @param  $gid 游戏ID
	 * @param  $cid 渠道ID
	 * @return array
	 */
	public function pcregister($uname = '', $pwd = '', $email = '', $nick = '', $gid = 100, $cid = 0) {
		$param = array(
				 'uname'=>$uname
				,'pwd'=>$pwd
				,'email'=>$email
				,'nick'=>$nick
				,'cid'=>$cid
				//,'gid'=>$gid
		);
		if($uname==''){
			unset($param['uname']);
		}
        return $this->_run('pcregister.app', $param, 'get');
	}

	/**
	 * 更新地址信息
	 * @param  $uid 本地用户标识
	 * @param  $cuid 移动用户标识（MUID）
	 * @param  $apikey 移动返回的api_key
	 * @param  $ip 新加必填，客户端的登录IP地址  
	 * @return array
	 */
	public function getareacode( $uid = 0, $cuid = 0, $apikey = '', $ip = '' ) {
		$param = array(
				 'uid'=>$uid
				,'cuid'=>$cuid
				,'apikey'=>$apikey
				,'ip'=>$ip 
		); 
        return $this->_run('getareacode.app', $param, 'get');
	}

    /**
	 * 获取登陆token
     * @param int $pid 平台ID
     * @param int $cid 客户端ID  编号由运维部分配
     * @param int $scid 子渠道标识 子渠道格式和长度：由纯数字组成的不超过19位长度的字符串。客户端采用字符串格式，默认值为0000。服务器数据库记录采用longlong(8个字节)的数据。
     * @param int $oid 用户的唯一标识
     * @param int $sid 会话id
     * @param int $gid 游戏应用ID，由第三方提供
     * @param string $ext 附加信息
     * @param int $type 第三方类型 1 -- 乐逗 2 --  91 3 --  极游 4 --  小米 5 --  新浪 6 --  梦龙 7 --  天玩 8 --  360 9 -- 德州点金 126 -- 飞信 127 -- 移动
     * @param int $gameid 游戏编号游戏编号，默认100(斗地主) 0-大厅
	 * @return array
	 */
    public function gettoken( $pid = 0 , $cid = 0 , $scid = 0 , $oid = 0 , $sid = 0 , $gid = 0 , $ext = '' , $type = 0 , $gameid = 0 ) {
		$param = array(
				 'pid'=>$pid
				,'cid'=>$cid
				,'scid'=>$scid
				,'oid'=>$oid 
				,'sid'=>$sid 
				,'gid'=>$gid 
				,'ext'=>$ext 
				,'type'=>$type 
				,'gameid'=>$gameid 
		);   
        return $this->_run('gettoken.app', $param, 'get');
	}	
	
	/**
	 * 第三方绑定(GET)
	 * @param int $tid	第三方平台ID
	 * @param int $type	第三方平台类型
	 * @param int $pid	平台标识（暂时不用）
	 * @param int $gid	游戏ID
	 * @param string $at	Token串
	 */
	public function bindthird($tid=0 , $type=0 , $pid=0 , $gid=0 , $at=''){
		$param = array(
				'tid'=>$tid
				,'type'=>$type
				,'pid'=>$pid
				,'gid'=>$gid
				,'at'=>urlencode($at)
		);
		return $this->_run('bindthird.app', $param, 'get');
	}

	/**
	 * GAS取用户信息(BY UID)
	 * @param string|array $cols 查询的字段
	 * @return array
	 */
	public function getUidInfo($cols = '') {
		return $this->getUserInfo($this->_uid, $cols, 'uid');
	}

	/**
	 * 查询帐号信息
	 * @param $byval 查询字段的值, 例如：$by = 'uid', 那么$byval就是uid的值
	 * @param $cols 列出的字段
	 * @param $by 查询字段
	 * @return array
	 */
	public function getUserInfo($byval, $cols = '', $by = 'uid') {
		$types = $this->_getColtype();
		$keyid = isset($types[$by])?$types[$by]:$types['uid'];

		//取要查询的字段
		if(!is_array($cols) && $cols!=''){
			$cols = explode(',', $cols);
		}
		$postdata = '<xml><query token="'.$this->_token.'" keyid="'.$keyid.'" key="'.$byval.'"/>';
		$ids = array();
		if($cols == '') {
			foreach($types as $col=>$id){
				$postdata .= '<field id="'.$id.'"/>';
				$ids[$id] = $col;
			}
		} else {
			foreach($cols as $col){
				$col = trim($col);
				if(!isset($types[$col])) continue;
				$postdata .= '<field id="'.$types[$col].'"/>';
				$ids[$types[$col]] = $col;
			}
		}

		$postdata .= '</xml>';
        $res = $this->_run('getuserinfo.app', $postdata, 'post');
		if(isset($res['result']) && $res['result'] == 0) {
            $tmpdata = array();
            foreach ($res['data'] as $k=>$v) {
                if(!isset($ids[$v['id']]))continue;
                $tmpdata[$ids[$v['id']]] = trim($v['value']);
            }
            $res['data'] = $tmpdata;
        }
		return $res;
	}

    /**
     * 批量查询用户信息
     * @param $uids 用户UID数组
     * @param string $cols 查询字段
     * @return mixed
     */
    public function getbatchuserinfo($uids, $cols='') {
        $types = $this->_getColtype();
        $keyid = $types['uid'];

        //取要查询的字段
        if(!is_array($cols) && $cols!=''){
            $cols = explode(',', $cols);
        }

        $postdata = '<xml>';
        $postdata .= '<keys>';
        foreach($uids as $_uid){
            $postdata .= '<key id="1" value="'.$_uid.'"/>';
        }
        $postdata .= '</keys>';
        $postdata .= '<fields>';
        $ids = array();
        if($cols == ''){
            foreach($types as $col=>$id){
                $postdata .= '<field id="'.$id.'"/>';
                $ids[$id] = $col;
            }
        }else{
            foreach($cols as $col){
                $col = trim($col);
                if(!isset($types[$col])) continue;
                $postdata .= '<field id="'.$types[$col].'"/>';
                $ids[$types[$col]] = $col;
            }
        }

        $postdata .= '</fields>';
        $postdata .= '</xml>';
        $res = $this->_run('getbatchuserinfo.app', $postdata, 'post');
        return $res;
    }

    /**
     * 通过第三方ID查询自营用户UID
     * @param string bindid 第三方绑定ID
     * @param integer platid 平台ID
     * @param string uid
     * @return array
     * @author liuweilong
     * 2014-4-8
     */
    public function addbindid($bindid,$platid,$uid) {
    	$param = array(
				'bindid'=>$bindid
				,'platid'=>$platid
				,'uid'=>$uid
		);
		return $this->_run('addbindid.app', $param, 'get');
    }

    /**
     * 通过第三方ID查询自营用户UID
     * @param string bindid 第三方绑定ID
     * @param integer type 类型
     * @return array
     * @author liuweilong
     * 2014-4-8
     */
    public function getuidforbindid($bindid,$type) {
    	$param = array(
				'token'=>$this->_token
				,'bindid'=>$bindid
				,'type'=>$type
		);
		//var_dump($param);
		return $this->_run('getuidforbindid.app', $param, 'get', 30, TRUE);
    }

	/**
	 * 修改帐号信息
	 * @param  $uid
	 * @param  $fields 修改的字段信息 字段名(非索引)=>值
	 * @return array
	 */
	public function moduserinfo($fields=array()) {
		$postdata = '<xml><mod token="'.$this->_token.'" uid="'.$this->_uid.'"/>';
		$types = $this->_getColtype();
		$ids = array();
		foreach($fields as $col=>$vall) {
			$col = trim($col);
			if(!isset($types[$col]))continue;
			$postdata .= '<field id="'.$types[$col].'" value="'.$vall.'"/>';
			$ids[$types[$col]] = $col;
		}
		$postdata .= '</xml>';
        return $this->_run('moduserinfo.app', $postdata, 'post');
	}

    /**
     * 更新token更新时间
     * @return mixed
     */
    public function updatetokentime() {
        $param = array(
            'uid'=>$this->_uid
           ,'token'=>$this->_token
        );
        return $this->_run('updatetokentime.app', $param, 'get');
    }
    
    /**
	 * 刷新商城列表
	 * @param unknown $tbus_list
	 * @return multitype:Ambigous <unknown, multitype:number string , multitype:>
	 */
    public function refreshShopList($tbus, $type) { 		
		$param=array(
				'BusId'=>$tbus,
				'Type'=>$type
		);
		return $this->_run('RefreshShopList',$param,'get');
    }


    
    /**
     * 推广员系统， 创建上下级关系
     * @param 游戏id $gamid
     * @param 被绑定人的uid $euid
     * @return array
     */
    public function createUpDownRelation($gameid, $euid) {
    	$param = array(
    			'uid'=>$euid,
    			'token'=>'xxx',   //随便给个参数
    			'gameId'=>$gameid,
    			'upuid'=>$this->_uid
    	);
    	return $this->_run('CreateUpDownRelation', $param, 'get');
    }
    
    /**
     * 获取游戏下线列表
     * @param int $gameid 游戏id
     * @return array
     */
    public function getUpDownRelation($gameid) {
    	$param = array(
    			'uid'=>$this->_uid,
    			'token'=>'xxx',  //随便给个参数
    			'gameid'=>$gameid
    	);
    	return $this->_run('GetUpDownRelation.app', $param, 'get');  	
    }
    
    /**
     * 批量删除游戏推广关系列表
     * @param int $gameid 游戏id
     * @param array $euid 被绑定者的uid
     * @return array
     */
    public function delUpDownRelation($gameid, $euidarr = array()) {
    	$postdata = '<delrel><upuser  uid = "{$this->_uid}”  token="xxx" />';
    	if(is_array($euidarr) && count($euidarr)) {
    		foreach($euidarr as $val) {
    			$postdata .= '<downuser uid="{$val}" gameid = "{$gameid}"/>';
    		}  		
    	}
    	$postdata .= '</delrel >';
    	return $this->_run('DelUpDownRelation.app', $postdata, 'post');
    }
	
    /**
     * 获取列的类型
     * @return multitype:string
     */
	private function _getColtype() {
		$types = array(
				 'uid' => '1' //用户标识
				,'uname' => '2' //用户名
				,'nick_name' => '3' //昵称
				,'real_name' => '4' //真实名称
				,'pwd' => '5' //密码
				//,'token' => '6' //鉴权ID -- @TODO 不能查，会失败
				,'avatar_id' => '7' //头像ID
				,'sex' => '8' //性别
				,'sign' => '9' //个人签名
				,'money' => '10' //本位币
				,'area_code' => '11' //地区编码
				,'guid' => '12' //用户号码
				,'idcard' => '13' //身份证
				,'mobile' => '14' //手机号码
				,'email' => '15' //电子邮箱
				,'register_time' => '16' //注册时间
				,'logon_times' => '17' //登录次数
				,'keep_logon_days' => '18' //连续登录天数
				,'logon_ip' => '19' //登录IP
				,'last_logon_time' => '20' //最后登录时间
				,'last_charge_time' => '21' //最后付费时间
				,'safe_level' => '22' //安全等级
				,'client_id' => '23' //渠道ID
				,'client_id_sub' => '24' //子渠道ID
				,'hardware_code' => '27' //硬件编码
				,'hardware_type' => '28' //硬件类型
				,'plat_id' => '29' //平台ID
				,'pay_times' => '32' //付费次数
				,'imsi' => '33' //用户imsi
				,'imei' => '34' //用户imei
				,'is_temp_account' => '101' //是否临时用户
				,'muid' => '102' //移动UID
				,'disable_account' => '103' //封号
				,'vip_level' => '104' //VIP等级
				,'vip_expiration' => '105' //VIP失效时间
				//,'status_bit' => '106' //状态位
			);
		return $types;
	}
	
	/**
	 * HTTP请求处理流程函数
	 * @param string $api 需要调用的接口
	 * @param xml/string/array $param 调用的参数
	 * @param string $method 传值方式
	 */
	private function _run($api, $param = array(), $method = 'post', $timeout = 30, $urlencode = false) {
		//获取到IP+port地址集
		$url_list = $this->_getGameAppUrl();
		$ips = isset($url_list[2]) ? $url_list[2] : array();	   //-web, 2-gas, 3-game
		if(!isset($ips[100][$api]) && !is_array($ips[100][$api])) {
			return array('status'=>1, 'msg'=>'gas缓存'.$api.'接口ip和port组成url没有找到！');
		}
		$_ips = $ips[100][$api];   //一个真正的IP集，注意gas是部分游戏， 所以同意用斗地主游戏id来区分100
		shuffle($_ips);
		//组装传递的参数， 注意http_build_query函数组装出来的参数是urlencode的，所以要转一下才能返回不乱码
		if($urlencode == false) {
			$query = is_array($param) ? urldecode(http_build_query($param)) : $param;
		} else {
			$query = is_array($param) ? http_build_query($param) : $param;
		}
		foreach($_ips as $_ip) {
			//注意get和post请求的区别
			$url = 'http://'.$_ip.'/'.$api;
			$rexml = (strtolower($method) == 'get') ? http::do_get($url.'?'.$query, $timeout) : (($this->_isreturn) ? http::do_post($url, $param, $timeout) : http::do_post_async($url, $param, $timeout));	
			$rearr = $this->_parseXml($rexml);
		    if($rearr !== false && isset($rearr['status']) && $rearr['status'] == 0) {
                break;
            }
		}
		//结果为error，这里需要商榷
		if(!$rearr) {
			return array('status'=>1, 'msg'=>'GAS服务器未找到！');
		}
		if($rearr['result'] == 0 ){ //success
			$loglevel = 2;
		} else {
			$loglevel = 1;
		}
		if(!isset($rearr['msg']) && isset($rearr['error'])) {
			$rearr['msg'] = $rearr['error'];
			unset($rearr['error']);
		}
		$this->_logsave($loglevel, $url.'?'.$query, $rexml);
		return $rearr;
	}
	
	/**
	 * GAS HTTP请求日志记录
	 * @param type $level 日志等级 //日志级别(0不写日志，1只写错误日志，2写错误及请求日志)
	 * @param type $url 请求URL
	 * @param type $return 返回值记录
	 */
	private function _logsave($level, $url, $return=''){
		$rout = $level==2 ? 'gasapp' : 'gasapp_error';
		return  $this->writeLog($rout, $url, $return);
	
	}

	/**
	 * 解析app xml(此方法专门解析app返回的xml)
	 * @param string $xml 默认GBK编码
	 */
	private function _parseXml($xml, $encoding='UTF-8') {
		//XML为空直接返回错误
		if(empty($xml)){
			return array('result'=>1,'msg'=>'XML格式错误');
		}
        $xml = $this->_gbk2utf($xml);
		$encoding = strtoupper($encoding);
		$xml = trim($xml);
		if(strpos($xml,'<?xml')===false){
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
			return array('result'=>1,'msg'=>'XML格式错误');
		}

		if(isset($values[0]['attributes'])) {
			$array = $values[0]['attributes'];
		}

		if(!isset($array['result'])) {
			return array('result'=>1,'msg'=>'XML没返回result');
		}
		for($i=1;$i<count($values);$i++) {
			if($values[$i]['type']=='complete') {
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
		// $back = json_decode(http::do_post(self::REQURL),true);
		return inter::init()->readRedisConfig('songplay:appurl');
	}
	
	/**
	 * GBK转UTF-8
	 * @param string $str
	 * @return string
	 */
	private function _gbk2utf($str) {
		if(!empty($str) && !$this->_isUtf8($str)) {
			$str = iconv('GBK', 'UTF-8', $str);
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
}
