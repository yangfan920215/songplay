<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-4-21
 */

class ReportAnaly extends CI_Controller{
	
	/**
	 * 反馈处理状态配置
	 * @var unknown
	 */
	private $reportStatus = array(
		array('id'=>-1, 'name'=>'全部'),
		array('id'=>0, 'name'=>'未处理'),
		array('id'=>1, 'name'=>'已处理'),
	);
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		$this->data['userType'] = $this->config->item('userType');
		
		$this->data['reportStatus'] = $this->reportStatus;
		
		$this->parser->parse('ReportAnaly.html', $this->data);
	}
	
	public function ajaxTable(){
		$usertype = get_extra_v1('usertype');
		$userkey = get_extra_v1('userkey');
		$gid = get_extra_v1('gid');
		$isOver = get_extra_v1('isOver');
		$sDate = get_extra_v1('sDate');
		$eDate = get_extra_v1('eDate');
		
		if (empty($userkey)) {
			$userkey = 0;
		}elseif ($usertype != 0){
			$type = $this->config->item('userType');
			
			// 加载公共gas,game,http
			require $this->config->item('sys_libs_dir') . 'http.class.php';
			require $this->config->item('sys_libs_dir') . 'gasapp.class.php';
			
			$res =  gasapp::init()->getUserInfo($userkey, $cols = 'uid', $type[$usertype]['pname']);
			
			if($res['result'] == 0) {
				$userkey = $res['data']['uid'];
			} else {
				exit(json_encode(array('status'=>-1, 'data'=>'通过key,找不到该用户')));
			}
		}
		
		$data = $this->dbapp->manage_sp_feedback_s($userkey, $isOver, $sDate, $eDate, $gid);
		
		foreach ($data as $key=>$value) {
			$data[$key]['datetime'] = $value['post_date'] . ' ' . $value['post_time'];
		}
		
		echo json_encode(array('data'=>$data));
	}
	
	private function _getUid($chkid, $id, $app) {
		$output = array('success' => true,'errors' => '');
	
		switch ($chkid) {
			case 0:
				$output['errors'] = $id;
				break;
	
			case 1:
			case 2:
			case 3:
				$type = $this->config->item('userType');
				$res = $app->getUserInfo($id, 'uid', $type[$chkid]['pname']);
				if($res['result'] == 0) {
					$output['errors'] = $res['data']['uid'];
	
				} else {
					$output['success'] = false;
					$output['errors'] = $res['msg'];
				}
				break;
			default:
				$output['success'] = false;
				$output['errors'] = '参数错误';
				break;
		}
	
		return $output;
	}
}