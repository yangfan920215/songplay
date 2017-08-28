<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-6-6
 */

class GiveFolw extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		$this->data['userType'] = $this->config->item('userType');
		
		$this->data['goldType'] = $this->dbapp->manage_sp_gold_typpe_config_s(-1);
		
		array_unshift($this->data['goldType'], array('id'=>-1, 'gname'=>'全部类型'));
		
		$this->parser->parse('customer/GiveFolw.html', $this->data);
	}
	
	public function ajaxTable(){
		$gid = get_extra_v2('gid');
		$chkid = get_extra_v2('usertype');
		$id = get_extra_v2('userkey');
		$sDate = get_extra_v2('sDate');
		$eDate = get_extra_v2('eDate');

		if (empty($id)) {
			exit(json_encode(array('data'=>array())));
		}
		
		// 加载公共gas,game,http
		require $this->config->item('sys_libs_dir') . 'http.class.php';
		require $this->config->item('sys_libs_dir') . 'gasapp.class.php';
		$app = Gasapp::init();
		
		// 查询用户UID
		$uid = getUid($chkid, $id, $app, $this->config->item('userType'));
		
		$data = $this->dbapp->sp_report_emailGive($uid, $gid, $sDate, $eDate);
		
		
		echo json_encode(array('data'=>$data));
	}
}