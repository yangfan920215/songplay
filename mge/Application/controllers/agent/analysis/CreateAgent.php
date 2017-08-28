<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 建立渠道
 * @author yangf@songplay.cn
 * @date 2016-4-20
 */

class createAgent extends CI_Controller{
	
	/**
	 * 渠道商级别
	 * @var unknown
	 */
	private $clientTypeList = array(
		2=>'二级渠道商',
		3=>'三级渠道商',
	);
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		// 检查该用户属于几级渠道商,若是一级以下渠道商,不能建立二级渠道账户
		$level = $this->dbapp->manage_sp_web_check_uid($_SESSION['authId']);
		$level = $level['msg'];
		
		$views = $this->views;
		
		if ($level <= 1) {
			$views->setRow(array(
			array(
				'type'=>'select',
				'list'=>$this->clientTypeList,
				'name'=>'agent_type',
				'onchange'=>'cInput(\'fa_client_id\', \'父渠道id\', 3, this)',
				),
			));
		}
		
 		echo $views->setRow(
			array(
				array(
					'name'=>'agent_id',
					'desc'=>'渠道id',
				),
			)
		)->setRow(
			array(
				array(
					'name'=>'company_name',
					'desc'=>'公司名',
				),
			)
		)->setRow(
			array(
				array(
					'name'=>'intoration',
					'desc'=>'分成比例',
					'maxlength'=>2,
				),
			)
		)->setRow(
			array(
				array(
					'type'=>'password',
					'name'=>'password',
					'desc'=>'密码',
				),
			)
		)->setRow(
			array(
				array(
					'type'=>'password',
					'name'=>'rpassword',
					'desc'=>'确认密码',
				),
			)
		)->setRow(
			array(
				array(
					'name'=>'uname',
					'desc'=>'姓名',
				),
			)
		)->setRow(
			array(
				array(
					'name'=>'email',
					'desc'=>'邮箱',
				),
			)
		)->setRow(
			array(
				array(
					'name'=>'phone',
					'desc'=>'手机号码',
				),
			)
		)->setRow(
			array(
				array(
					'type'=>'button',
					'desc'=>'点击注册',
					'onclick'=>array(
					'type'=>'ajax',
				),
			),
		)
		)->done();
	}
	
	public function ajaxData(){
		// 接收数据
		$agent_id = $this->datarece->post('agent_id', true, '不合法的渠道id');
		$company_name = $this->datarece->post('company_name', true, '不合法的公司名');
		$uname = $this->datarece->post('uname', true, '不合法用户名');
		$email = $this->datarece->post('email', true, '不合法的邮箱');
		$password = $this->datarece->post('password', true, '不合法的密码1');
		$rpassword  = $this->datarece->post('rpassword', true, '不合法的密码2');
		$intoration = $this->datarece->post('intoration', true, '不合法的分成比例')/100;
		$phone = $this->datarece->post('phone', true, '不合法的手机号码');
		
				
		$agent_type = $this->datarece->post('agent_type', false, '不合法的渠道类型');
		
		$father_agent_id = null;
		if ($agent_type >= 3) {
			$father_agent_id = $this->datarece->post('fa_client_id', true, '不合法的父渠道id');
			// 判断用户提交的父渠道id是否正常
			$ret = $this->dbapp->manage_sp_web_check_cid($father_agent_id, $agent_type - 1);
			
			if ($ret['status'] != 0) {
				execExit('父渠道id不存在');
			}
		}
		
		// 密码验证
		if ($password != $rpassword) {
			execExit('两次输入的密码不一致,请重新输入');
		}

		// 如果是渠道商注册,只允许二级渠道新建三级渠道
        $this->load->library('app/client', array($_SESSION['authId']));
        if ($this->client->getLevel() != 0 && $this->client->getLevel() != 2) {
            execExit('权限不足');
        }

        if ($this->client->getLevel() == 2){
            $father_agent_id = $this->client->getClientId();
            $agent_type = 3;
        }

		// 注册用户
		$ret = $this->dbapp->manage_sp_web_addclientuser($agent_id, $password, $uname, $_SESSION['conn_db_id']);
		
		// 若新增成功赋值
		$uid = isset($ret['status']) && isset($ret['msg'])  && $ret['status'] == 0 ? $ret['msg'] : execExit('用户新增失败,可能已存在', 0);
		
		$this->dbapp->manage_sp_web_company_i($father_agent_id, $agent_type, $uid, $company_name, $phone, $agent_id, $email, $intoration);
		
		execExit('渠道新增成功', 0);
	}
}