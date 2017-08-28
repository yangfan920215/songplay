<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-6-6
 */

class GameFolw extends CI_Controller{
	// 根据房间号查询
	const room_type = 100;
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		$this->data['userType'] = $this->config->item('userType');
		
		
		$this->data['userType'] = array_merge($this->data['userType'], array(array('id'=>self::room_type, 'name'=>'房间号')));
		
		$this->parser->parse('customer/GameFolw.html', $this->data);
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
		
		
		$data = $goldType == self::room_type ? $this->dbapp->manage_sp_versus_s_by_roomid($uid, $gid, $sDate, $eDate, '') : $this->dbapp->manage_sp_versus_s_extra($uid, $gid, $sDate, $eDate, '', $_SESSION['authId']);
		
		foreach ($data as $key=>$value) {
			$data[$key]['gold3'] = $value['gold2'] - $value['gold1'];
		}
		
		$roomId = $this->dbapp->config_sp_node_room_s_content(-1);//获取所有的房间
		$roomId = $this->dataext->toSelectData($roomId,'room_id','name');
		
		$newData = array();
		$page = 0;
		if (!empty($data) && isset($data[0]) && isset($data[1][0]['rows'])) {
			$newData = $data[0];
			foreach ($newData as $key => $value) {
				$newData [$key] ['gold'] = $value['gold2'] - $value['gold1'];
				if($value['score1'] != $value['score2']) {
					if( $value['score1'] > $value['score2'] ) {
						$newData [$key] ['result'] = '输';
					}else{
						$newData [$key] ['result'] = '赢';
					}
				} else {
					if( $value['gold1'] > $value['gold2'] ){
						$newData [$key] ['result'] = '输';
					}else{
						$newData [$key] ['result'] = '赢';
					}
				}
		
				$newData [$key] ['room_id'] = $roomId[$value['room_id']].'('.$value['room_id'].')';
				$newData [$key] ['now_gold'] = $value['gold2'];
				$newData [$key] ['date'] = $value['post_date'].'&nbsp;'.$value['post_time'];
		
			}
			$page = $data[1][0]['rows'];
		}
		
		echo json_encode(array('data'=>$data));
	}
}