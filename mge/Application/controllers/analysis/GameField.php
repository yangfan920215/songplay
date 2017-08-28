<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-3-7
 */

class GameField extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		
		// 链接数据库
		$this->load->database();
	}
	
	public function index(){
		$cascade = array();
		
		foreach ($this->data['gameList'] as $key=>&$value) {
			$cascade[$key]['id'] = $value['id'];
			$cascade[$key]['name'] = $value['name'];
			
			$cascade[$key]['child'] = $this->getCascade($this->dbapp->config_sp_node_gameids($value['id']));
			
			$cascade[$key]['child'][-1] = '全部比赛';
		}
		
		$this->data['cascade'] = json_encode($cascade, JSON_UNESCAPED_UNICODE);
		
		$this->parser->parse('GameField.html', $this->data);
	}
	
	
	public function ajaxData(){
		$cascade = isset($_REQUEST['extra_search'][0]['req']['cascade']) ? $_REQUEST['extra_search'][0]['req']['cascade'] :exit;
		$sDate = isset($_REQUEST['extra_search'][0]['req']['sDate']) ? $_REQUEST['extra_search'][0]['req']['sDate'] :exit;
		$eDate = isset($_REQUEST['extra_search'][0]['req']['eDate']) ? $_REQUEST['extra_search'][0]['req']['eDate'] :exit;
		
		echo $this->data($cascade, $sDate, $eDate);
	}
	
	private function data($cascade, $sDate, $eDate){
		
		if ($cascade == -1) {
			$roomids = -1;
		}else{
			$roomid = $this->dbapp->config_sp_node_id_s($cascade);
				
			$roomids = '';
			foreach ($roomid as $value) {
				if (isset($value['id'])) {
					$roomids .= $value['id'] . ',';
				}
			}
		}
		
		$data = $this->dbapp->mge_sp_event_cal_match($sDate, $eDate, $roomids);
		
		$pidList = $this->dbapp->config_sp_prop_s_all();
		
		$roomList = $this->dbapp->config_sp_room_s_all();
		
		foreach ($data as $key => &$value) {
			//$first = isset($value['first']) ? $value['first'] : '';
			//$second = isset($value['second']) ? $value['second'] : '';
			//$third = isset($value['third']) ? $value['third'] : '';
			$award = isset($value['award']) ? explode(';', $value['award']) : '';
			$awardrob = isset($value['awardrob']) ? explode(',', $value['awardrob']) : '';
			// 报名费 
			$apply_list = isset($value['apply_list']) ? explode(',', $value['apply_list']) : '';
			
			// 实际参赛玩家信息
			$player_list = isset($value['player_list']) ? explode(',', $value['player_list']) : array();
			
			$realParameter = array();
			foreach ($player_list as $player_listSon) {
				$info = explode(';', $player_listSon);
				
				$guid = isset($info[1]) ? $info[1] : '未知';
					
				$uname = isset($info[2]) ? $info[2] : '未知';
				
				$realParameter[] = array(
						'guid'=>$guid,
						'uname'=>$uname,
				);
			}
			
			
			
			foreach ($roomList as $room) {
				if (isset($room['id']) && isset($room['name']) && $room['id'] == $value['room_id']) {
					$value['room_name'] = $room['name'];
				};
			}
			
			unset($value['award']);
			unset($value['awardrob']);
			unset($value['apply_list']);
			
			// 获取用户是否是机器人人信息
			$userInfo = array();
			if(is_array($awardrob)){
				foreach ($awardrob as $key2=>$value2) {
					$userIsReb = explode(':', $value2);
					$uid = isset($userIsReb[0]) ? $userIsReb[0] : 'NULL';
					$isrebot = isset($userIsReb[1]) ? $userIsReb[1] : 'NULL';
					$userInfo[$uid] = $isrebot;
				}	
			}
			
			$userbm = array();
			if (is_array($apply_list)) {
				foreach ($apply_list as $value3) {
					
					$info = explode(';', $value3);
					
					$guid = isset($info[2]) ? $info[2] : '未知';
					
					$uname = isset($info[3]) ? $info[3] : '未知';
					
					$pname = '';
					
					if (isset($info[1])) {
						$bm = explode('-', $info[1]);
						
						if (isset($bm[0]) && isset($bm[1])) {
							if ($bm[0] == 0) {
								$pname = '金币x' . $bm[1];
							}else{
								foreach ($pidList as $value4) {
									if ($value4['id'] == $bm[0]) {
										$pname = isset($value4['name']) ? $value4['name'] : $bm[0] . '(未找到对应道具名)';
										break;
									}
								}
								
								$pname = $pname . 'x' . $bm[1];
							}
						}
						

					}

					
					$userbm[] = array(
						'guid'=>$guid,
						'pname'=>$pname,
						'uname'=>$uname,
					);
				};
			}
			
			//$aw = $first . ';' . $second . ';' . $third . ';' . $award;
			
			$arr = array(
				
			);
			
			foreach ($award as $key1=>&$value1) {
				// 转换为数组
				$arr[$key1] = array('uid' => $value1);
				
				$arr[$key1]['rebot'] = isset($userInfo[$value1]) ? $userInfo[$value1] : 'unKnow';
				
/* 				if ($value1 == '') {
					unset($arr[$key1]);
					continue;
				}; */

			}
			
			$value['award'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
			$value['userbm'] =  json_encode($userbm, JSON_UNESCAPED_UNICODE);
			$value['realParameter'] =  json_encode($realParameter, JSON_UNESCAPED_UNICODE);
			
			unset($value['first']);
			unset($value['second']);
			unset($value['third']);
		}
		
		return json_encode(array('data'=>$data));
	}
	
	/**
	 * 
	 * @param unknown $sDate
	 * @param unknown $eDate
	 * @param unknown $cascade
	 */
	public function divfun(){
		$cascade = get_comm_req('cascade');
		$sDate = get_comm_req('sDate');
		$eDate = get_comm_req('eDate');
		
		if($cascade != -1){
			$roomid = $this->dbapp->config_sp_node_id_s($cascade);
			
			$roomids = '';
			foreach ($roomid as $value) {
				if (isset($value['id'])) {
					$roomids .= $value['id'] . ',';
				}
			}
		}else{
			$roomids = -1;
		}
		
		echo json_encode($this->dbapp->mge_sp_event_cal_matchgold($sDate, $eDate, $roomids), JSON_UNESCAPED_UNICODE);
	}
	
	/**
	 * 检测一个uid是否是机器人,若是则返回true
	 * @param unknown $uid
	 * @return boolean
	 */
	private function checkRebot($uid){
		$data = $this->dbapp->manage_sp_report_robot($uid);
		
		if (!isset($data[0]['vip_id'])) {
			return false;
		}
		switch ($data[0]['vip_id']) {
			case 100:
				return true;
				break;
			default:
				return false;
				break;
		}
	}
	
	/**
	 * 生成key=>value结果的比赛场数据,用于配合前端生成级联下拉列表
	 * @param array $room
	 * @return multitype:unknown
	 */
	private function getCascade(array $room){
		$cascade = array();
		foreach ($room as $value) {
			// 过滤不合法的数据
			if (!isset($value['id']) || !isset($value['name'])) {
				continue;
			}
			$cascade[$value['id']] = $value['name'];
		}
		return $cascade;
	}
}