<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 公共模块
 * @author yangf@songplay.cn
 * @date 2016-5-17
 */

class Pub extends CI_Controller{

	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 进入登录界面
	 */
	public function index(){
		
		// 获取站点命名
		$this->data['title'] = $this->config->item('title');
		
		// 从cookie中查询是否拥有账户名和密码,若存在,前端页面会直接被渲染
		$this->data['uname'] = $this->config->item('is_cache_uname_pwd') ? get_cookie('uname') : '';
		$this->data['pwd'] = $this->config->item('is_cache_uname_pwd') ? get_cookie('pwd') : '';
		
		// 获取站点uri
		$this->data['mainroot'] = base_url() . 'index.php/system/Main/index';
		$this->data['checkroot'] = base_url() . 'index.php/Pub/checkLogin';
		
		$this->data['jroot'] = base_url() . 'Application/views/public/js/';	// js公共文件URL
		$this->data['croot'] = base_url() . 'Application/views/public/css/';	// css公共文件URL
		
		// 跳转登录页面
		$this->parser->parse('login.html', $this->data);
	}
	
	/**
	 * 登录验证操作
	 */
	public function checkLogin(){
		// 参数效验
		$this->form_validation->set_rules('uname', 'Username', 'trim|required|min_length[3]|max_length[30]',
				array(
						'required'  => '账户必须被填写!',
						'min_length'=>'账户过短(>3字符串)!',
						'max_length'=>'账户过长(<20字符串)!',
						//'valid_email'=>'账户不是一个合法的邮箱账户!',
				)
		);
		$this->form_validation->set_rules('passwd', 'Password', 'trim|required|min_length[5]|max_length[20]',
				array(
						'required'  => '密码必须被填写!',
						'min_length'=>'密码过短(>5字符串)!',
						'max_length'=>'密码过长(<20字符串)!',
				)
		);
		// 查看验证结果
		if ($this->form_validation->run() == FALSE){
			// 验证失败,输出错误信息
			echo validation_errors();
		}else {
			// 验证成功,初始化用户类,开始验证
			echo $this->user->login($this->input->post('uname'), $this->input->post('passwd'));
		}
	}
	
	/**
	 * 用户session认证添加
	 */
	private function setUser(array $userInfo){
		// 将用户id,邮箱,最后登录时间,登录次数保存至session
		$this->session->set_userdata($this->config->item('USER_AUTH_KEY'), $userInfo['id']);
		$this->session->set_userdata('email', $userInfo['acount']);
		$this->session->set_userdata('last_login_time', $userInfo['last_login_time']);
		$this->session->set_userdata('login_count', $userInfo['login_count']);
	
		// 超管验证
		if(in_array($userInfo['acount'], $this->config->item('ADMIN'))) {
			$this->session->set_userdata('administrator', true);
		}
	}
}