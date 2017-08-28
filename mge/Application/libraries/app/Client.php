<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 渠道商管理类
 * @author yangf@songplay.cn
 * @date 2016-5-17
 */


class Client{
	
	private $CI;

	/**
	 * 渠道id
	 * @var unknown
	 */
	private $clientid;

    /**
     * @var 渠道额度
     */
	private $limit;

	/**
	 * 渠道等级
	 * @var unknown
	 */
	private $level = -1;
	
	/**
	 * 不允许不经过验证随意创建渠道对象
	 */
	public function __construct($param){
		$this->CI =& get_instance();
		
		// 没有指定用户id,则使用登录账户的id
		$id = isset($param[0]) ? $param[0] : $_SESSION['authId'];
		
		// 用户id
		$ret = $this->CI->dbapp->manage_sp_get_client_info($id);
		
		if (isset($ret['status']) && $ret['status'] == -1) {
			$this->level = 0;
		}elseif(isset($ret['id'])){
			// 真实渠道
			$this->clientid = $ret['clientid'];
			$this->limit = $ret['_limit'];
			$this->level = $ret['level'];
			$this->status = $ret['_status'];
		}
	}
	
	public function getLevel(){
		return $this->level;
	}

    public function getLimit(){
        return $this->limit;
    }

    /**
     * @param $client
     * @param int $type 0:解封，1,封禁
     */
    public function clientChangeStatus($id, $client, $type = 0){
        if($this->checkUser($client)){
            // 取出该渠道下全部子渠道
            $ret = $this->CI->dbapp->manage_sp_web_get_clientsub($id);
            foreach ($ret as $key=>$value) {
                if (isset($value['clientid'])) {
                    $this->CI->dbapp->manage_sp_news1_web_company_u_status($value['clientid'], $type);
                }
                continue;
            }
            return true;
        }
    }

    public function getStatus(){
        return $this->status == 0 ? true : false;
    }
	
	public function getClientId(){
		return $this->clientid;
	}

	public function getUserClient($uid){
        $ret = $this->CI->dbapp->manage_sp_get_client_info($uid);

        if (isset($ret['clientid'])) {
            return $ret['clientid'];
        }else{
            return false;
        }
    }

	public function checkUser($clientId){
        // 非渠道用户可以查询任何用户
        if ($this->getLevel() === 0) {
            return true;
        }

        $ret = $this->CI->dbapp->manage_sp_web_get_clientsub($_SESSION['authId']);

        if (is_array($ret) && isset($clientId)){
            foreach ($ret as $key=>$value) {
                if (isset($value['clientid']) && $value['clientid'] == $clientId) {
                    return true;
                }
                continue;
            }
        }else{
            return false;
        }
    }

}