<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 链接主数据库类
 * @author yangf@songplay.cn
 * @date 2016-5-13
 */

require_once './Application/libraries/db/DbBase.php';

class DbMge extends DbBase{
	
	private $dbName = 'mge';

	public function __construct(){
		parent::__construct($this->dbName);
	}
	
	/**
	 * 登录验证
	 * @param unknown $uname
	 * @param unknown $passwd
	 * @param unknown $uip
	 * @return unknown
	 */
	public function sp_check_login($uname, $passwd, $uip){
		$spName = 'sp_check_login';
		$param = array(
				$uname, $passwd, $uip,
		);
	
		$ret = $this->checkRet($this->execSp($spName, $param));
		// 成功获取到登录数据
		if (isset($ret[0]) && isset($ret[0]['id'])) {
			return $ret[0];
		}
		return false;
	}
	

	/**
	 * 获取用户节点信息
	 * @param unknown $authId
	 * @return unknown
	 */
	public function sp_get_node($authId){
		$spName = 'sp_get_node';
		$param = array(
				$authId,
		);
	
		return $this->execSp($spName, $param);
	}
	
	
	public function sp_get_system_user($id){
		$spName = 'sp_get_system_user';
		$param = array(
			$id
		);
	
		return $this->execSp($spName, $param);
	}
	
	public function sp_get_system_role(){
		$spName = 'sp_get_system_role';
		$param = array(
		);
	
		return $this->execSp($spName, $param);
	}
	
	/**
	 * 用户重设密码
	 * @param unknown $uid
	 * @param unknown $n_pwd
	 */
	public function mge_sp_sys_reset_password($uid, $pwd, $npwd){
		$spName = 'sp_sys_reset_password';
		$param = array(
			$uid, $pwd, $npwd
		);
	
		return $this->execSp($spName, $param);
	}
	
	public function mge_sp_table_s($dbName, $tableName){
		$spName = 'sp_table_s';
		$param = array(
			$dbName, $tableName
		);
		return $this->execSp($spName, $param);
	}
	
	
	public function mge_sp_sys_register_user($acount, $password, $remark, $role){
		$spName = 'sp_sys_register_user';
		$param = array(
				$acount,
				$password,
				$remark,
				$role
		);
		return $this->execSp($spName, $param);
	}
	
	/**
	 * 根据uid获取cid,若cid不存在返回1
	 * @param unknown $uid
	 * @return unknown
	 */
	public function mge_sp_get_clientid_by_uid($uid){
		$spName = 'sp_get_clientid_by_uid';
		$param = array(
				$uid
		);
		return $this->execSp($spName, $param);
	}
	
	/**
	 * 检查某个渠道id是否存在且属于某级渠道商
	 * @param unknown $cid
	 * @param unknown $level
	 * @return unknown
	 */
	public function mge_sp_sys_delete_user($uid){
		$spName = 'sp_sys_delete_user';
		$param = array(
				$uid
		);
		return $this->execSp($spName, $param);
	}
	
	/**
	 * 检测一个玩家是否是机器人
	 * @param unknown $uid
	 * @return unknown
	 */
	public function mge_sp_report_robot($uid){
		$spName = 'sp_report_robot';
		$param = array($uid);
		return $this->execSp($spName, $param);
	}
	
	/**
	 * 重设用户角色
	 * @param unknown $uid
	 * @return unknown
	 */
	public function sp_sys_reset_role($userid, $role_id){
		$spName = 'sp_sys_reset_role';
		$param = array($userid, $role_id);
		return $this->execSp($spName, $param);
	}
	
	
	/**
	 * 获取全部节点信息
	 * @return unknown
	 */
	public function mge_sp_get_system_node(){
		$spName = 'sp_get_system_node';
		return $this->execSp($spName);
	}
	
	public function sp_get_userRole($uid){
		$spName = 'sp_get_userRole';
		$param = array(
			$uid
		);
		return $this->execSp($spName, $param);
	}
}