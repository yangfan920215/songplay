<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 用户类
 * @author yangf@songplay.cn
 * @date 2016-1-12
 */

class User{
	private $CI;
	
	public function __construct(){
		$this->CI =& get_instance();
	}
	
	/**
	 * 用户登录
	 * @param unknown $uname
	 * @param unknown $passwd
	 * @return string|boolean
	 */
	public function login($uname, $passwd){
		// 参数效验通过,查询账户信息
		$result = $this->CI->dbmge->sp_check_login($uname, $passwd, $this->CI->input->ip_address());
		
		if ($this->CI->dbmge->getErrorMsg() == '') {
			// 登录成功
			// session保存用户信息
			$this->setUser($result);
				
			// 如果设置cookie
			if ($this->CI->input->post('hascookie') == 'true' && $this->CI->config->item('is_cache_uname_pwd')) {
				set_cookie('uname', $uname, 2592000);
				set_cookie('pwd', $passwd, 2592000);
			}
			return true;
		}else{
			// 返回错误信息
			return $this->CI->dbmge->getErrorMsg();
		}
	}
	
	/**
	 * 检验用户是否登录
	 */
	public function checkUser(){
		// 控制器名
		$cName = $this->CI->urlext->getContro();
		// 功能名
		$htName = $this->CI->urlext->getMethod();
		
		// 公共目录不需要检查权限
		if ($cName === 'Pub') {
			return true;
		}
		
		// 获取用户标识id
		if (!isset($_SESSION[$this->CI->config->item('USER_AUTH_KEY')]) || $_SESSION[$this->CI->config->item('USER_AUTH_KEY')] === false) {
			// 检测到用户未登录登录
			jump_url('请登录后再进行操作');
		}elseif($cName == 'Main'){
			// 如果用户只想进入主界面,存在账户就满足权限
			return true;
		}
	
		// 检查用户权限
		$nid = $this->checkAuth($cName, $htName);
		
		if($nid === false){
			// 用户并不拥有该节点的访问权限
			execExit('权限不足');
		}
		
		return $nid;
	}
	
	/**
	 * 用户session认证添加
	 */
	private function setUser(array $userInfo){
		// 修改session有效期
		$this->CI->config->set_item('sess_expiration', 43200);
		
		// 修改用户数据库链接
		$this->CI->session->set_userdata('conn_db_id',  $userInfo['conn_db_id']);

		
		$conn_config = isset($this->CI->config->item('conn_db')[$userInfo['conn_db_id']]) ? $this->CI->config->item('conn_db')[$userInfo['conn_db_id']] : exit('用户未配置正确的数据链接');
		
		$this->CI->session->set_userdata('dbconfig', $conn_config);
		
		// 将用户id,邮箱,最后登录时间,登录次数保存至session
		$this->CI->session->set_userdata($this->CI->config->item('USER_AUTH_KEY'), $userInfo['id']);
		$this->CI->session->set_userdata('email', $userInfo['acount']);
		$this->CI->session->set_userdata('last_login_time', $userInfo['last_login_time']);
		$this->CI->session->set_userdata('login_count', $userInfo['login_count']);
	
		$access = $this->CI->dbmge->sp_get_node($userInfo['id']);
		$this->CI->session->set_userdata('node_auth', $access);
	
		// 超管验证
		if(in_array($userInfo['acount'], $this->CI->config->item('ADMIN'))) {
			$this->CI->session->set_userdata($this->CI->config->item('ADMIN_AUTH_KEY'), true);
		}
	}
	
	/**
	 * 根据控制器三个变量获取节点id
	 * @param unknown $sName
	 * @param unknown $cName
	 * @param unknown $mName
	 * @return boolean|unknown
	 */
	private function checkAuth($cName, $htName){
		foreach ($_SESSION['node_auth'] as $value) {
			// 用户是否拥有该控制器的访问权限
			if (isset($value['id']) && isset($value['controller']) && isset($value['method']) && isset($value['perms']) && isset($value['permissions']) && strtolower($value['controller']) == strtolower($cName)) {
				// 用户是否拥有该功能点的访问权限
				// 默认权限不需要额外赋予
				// 如果真需要开放权限功能,这里应该是'index' == $htName
				if ('index' == $value['method']) {
					// 设计失败
					return $value['id'];
				}else{
					$perms = explode(',', $value['perms']);
					$permissions = explode(',', $value['permissions']);
					
					if (in_array($htName, $perms) && in_array($htName, $permissions)) {
						return $value['id'];
					}
					return false;
				}
			}
			continue;
		}
		return false;
	}
}