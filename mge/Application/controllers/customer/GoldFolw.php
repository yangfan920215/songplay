<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-6-6
 */

class GoldFolw extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		$this->data['userType'] = $this->config->item('userType');
		
		$this->data['goldType'] = $this->dbapp->manage_sp_gold_typpe_config_s(-1);
		
		array_unshift($this->data['goldType'], array('tid'=>-1, 'gname'=>'全部类型'));
		
		$this->parser->parse('customer/GoldFolw.html', $this->data);
	}
	
	public function ajaxTable(){
		$gid = get_extra_v2('gid');
		$chkid = get_extra_v2('usertype');
		$id = get_extra_v2('userkey');
		$sDate = get_extra_v2('sDate');
		$eDate = get_extra_v2('eDate');
		$goldType = get_extra_v2('goldType');

		if (empty($id)) {
			exit(json_encode(array('data'=>array())));
		}
		
		// 加载公共gas,game,http
		require $this->config->item('sys_libs_dir') . 'http.class.php';
		require $this->config->item('sys_libs_dir') . 'gasapp.class.php';
		$app = Gasapp::init();
		
		// 查询用户UID
		$uid = getUid($chkid, $id, $app, $this->config->item('userType'));
		
		$this->load->library('app/client', array($_SESSION['authId']));
		
		$client_id = $this->client->getClientId();
		
		if (isset($client_id)) {
			$data = $this->dbapp->manage_sp_gold_sn($uid, $gid, $goldType, $client_id, $sDate, $eDate);
		}else{
			$data = $this->dbapp->manage_sp_gold_s($uid, $gid, $goldType, $sDate, $eDate);
		}
		
		
		
		$config_gold = $this->dbapp->manage_sp_gold_typpe_config_s(-1);
		
		$config_room = $this->dbapp->config_sp_room_s_all();
		
		foreach ($data as $key=>$value) {
			$data[$key]['typeName'] = '未知流水类型';
			foreach ($config_gold as $key1=>$value1) {
				if ($value['type_id'] == $value1['tid']) {
					$data[$key]['typeName'] = $value1['gname'];
				}
				continue;
			}
			
			// 房间id
			foreach ($config_room as $key2=>$value2) {
				if ($value['room_id'] == $value2['id']) {
					$data[$key]['room_name'] = $value2['name'];
				}
				continue;
			}
			
			$data[$key]['post_date'] = $value['post_date'].' '.$value['post_time'];
			$data[$key]['gold3'] = $value['gold2'] - $value['gold1'];
		}
		
		
		echo json_encode(array('data'=>$data));
	}
}