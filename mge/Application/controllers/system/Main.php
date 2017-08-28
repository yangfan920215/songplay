<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 主页面
 * @author yangf@songplay.cn
 * @date 2016-5-13
 */

class  Main extends CI_Controller{
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		// 将用户拥有的节点拼凑成规则的链表结构
		$this->data['access_list'] = $this->getAccessList();
		
		// 用户信息
		$this->data['uname'] = $this->session->email;
		$this->data['last_login_time'] = $this->session->last_login_time;
		$this->data['login_count'] = $this->session->login_count;
		// 站点标题
		$this->data['title'] = $this->config->item('title');
		// 获取站点uri
		$this->data['jroot'] = base_url() . 'Application/views/public/js/';	// js公共文件URL
		$this->data['croot'] = base_url() . 'Application/views/public/css/';	// css公共文件URL
		$this->data['iroot'] = base_url() . 'Application/views/public/images/';	// css公共文件URL
		$this->data['setPwdUrl'] = base_url() . 'index.php/system/Main/setPwd';	// 设置密码
		$this->data['logoutUrl'] = base_url() . 'index.php/system/Main/logout';	// 登出
		$this->data['sroot'] = base_url() . 'index.php/';	// 前置uri

        $limitHtml = '';
        $this->load->library('app/client', array($_SESSION['authId']));
        if ($this->client->getLevel() != 0){
            $limit = $this->client->getLimit();
            $limitHtml = <<<str
				<li class="sidebar-search">
					<div class="input-group">
						<span>我的金幣額度：$limit</span>
					</div>
				</li>
str;

        }
        $this->data['limitHtml'] = $limitHtml;
		// 解析主页面
		$this->parser->parse('main.html', $this->data);
	}
	
	/**
	 * 登出
	 */
	public function logout(){
		// 销毁全部登录认证的session
		$this->session->sess_destroy();
		// 跳转首页
		header('Location: http://' . $_SERVER['HTTP_HOST']);
	}
	
	/**
	 * 重设密码
	 */
	public function setPwd(){
		// 参数验证
		$this->form_validation->set_rules('pwd', '原密码', 'trim|required',
				array(
						'required'  =>'原密码必须被填写!',
				)
		);
		$this->form_validation->set_rules('npwd', '新密码', 'trim|required|min_length[5]|max_length[30]',
				array(
						'required'  => '新密码必须被填写!',
						'min_length'=>'新密码过短(>5字符串)!',
						'max_length'=>'新密码过长(<20字符串)!',
				)
		);
		$this->form_validation->set_rules('rpwd', '新密码', 'trim|required|min_length[5]|max_length[30]',
				array(
						'required'  =>'新密码必须被填写!',
						'min_length'=>'新密码过短(>5字符串)!',
						'max_length'=>'新密码过长(<20字符串)!',
				)
		);
		
		// 参数验证失败,返回错误信息
		if ($this->form_validation->run() == FALSE) {
			execExit(strip_tags(validation_errors()));
		}
		
		// 若两次输入新密码不一致,返回错误信息
		if ($this->input->post('npwd') != $this->input->post('rpwd')) {
			execExit('两次输入的新密码不一致,请仔细输入!');
		}
		
		$ret = $this->dbmge->mge_sp_sys_reset_password($_SESSION['authId'], $this->input->post('pwd'), $this->input->post('npwd'));
		if (isset($ret['status']) && $ret['status'] == 0) {
			echo json_encode(array('status'=>0, 'data'=>'密码修改成功'));
			exit;
		}else {
			$error_msg = isset($ret['msg']) ? $ret['msg'].'密码修改失败' : '系统异常,密码修改失败';
			echo json_encode(array('status'=>-1, 'data'=>$error_msg));
			exit;
		}
	}
	
	/**
	 * 获取该用户角色下能访问的全部节点
	 * @return unknown
	 */
	private function getAccessList(){
		$leftList = $this->getLeftList($_SESSION['node_auth']);
		return json_encode($leftList, JSON_UNESCAPED_UNICODE);
	}
	
	/**
	 * 获取排序后的节点数据
	 * @param unknown $access
	 * @return string
	 */
	private function getLeftList($access){
		$leftList_0 = $this->getLevelList(0, $access);
		$leftList_1 = $this->getLevelList(1, $access);
		$leftList_2 = $this->getLevelList(2, $access);
		
		
		$leftList = $this->insTopNode($leftList_1, $leftList_2);
		$leftList = $this->insTopNode($leftList_0, $leftList);
		return $leftList;
	}
	
	/**
	 * 获取某个level的全部节点
	 * @param unknown $level
	 * @param unknown $access
	 * @return multitype:unknown
	 */
	private function getLevelList($level, $access){
		$arr = array();
		
		foreach ($access as $k=>$v){
			if ($v['level'] == $level) {
				$arr[]  = $v;
			}
		}
		return $arr;
	}
	
	/**
	 * 将子节点数据合并到上一层节点数据中
	 * @param unknown $topNode
	 * @param unknown $node
	 * @return unknown
	 */
	private function insTopNode($topNode, $node){
		foreach ($node as $v){
			$pid = $v['pid'];
			foreach ($topNode as &$v1) {
				if ($v1['id'] == $pid) {
					$v1['data'][] = $v;;
				}
			}
		}
		return $topNode;
	}
}