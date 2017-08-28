<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2015-12-25
 * @fun 机器人金币消耗
 */


class RebotGold extends CI_Controller{
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		$this->parser->parse('RebotGold.html', $this->data);
	}
	
	/**
	 * ajax获取列名和数据
	 */
	public function ajaxTable(){
		// 获取游戏id
		if (isset($_POST['gid'])) {
			$gid = $_POST['gid'];
		}else if(isset($_REQUEST['extra_search'][0]['gameId'])){
			$gid = $_REQUEST['extra_search'][0]['gameId'];
		}else{
			error_report('1', 'gid');
		}
		$RoomList = $this->getGRoomList($gid);
		
		if (isset($_REQUEST['extra_search'])) {
			$sDate = isset($_REQUEST['extra_search'][1]['sDate']) ? $_REQUEST['extra_search'][1]['sDate'] : error_report('1', 'sDate');
			$eDate = isset($_REQUEST['extra_search'][2]['eDate']) ? $_REQUEST['extra_search'][2]['eDate'] : error_report('1', 'eDate');
			$data = $this->getData($gid, $sDate, $eDate, $RoomList);
			echo $data;
		}else{
			
			$RoomNameList = json_encode(array_keys($RoomList), JSON_UNESCAPED_UNICODE);
			echo $RoomNameList;
		}
	}
	
	public function getData($gid, $sDate, $eDate, $RoomList){
		$data = array();
		$i = 0;
		foreach ($RoomList as $value) {
			$str = implode('\',\'', $value);
			$str = '\''.$str.'\'';
			$result = $this->dbapp->manage_sp_event_cal_goldtype($sDate, $eDate, $str, $i);
			$data = $i == 0 ? $result : $this->dataext->tableDataMerge('post_date',array('sort'=>'post_date asc','sortType'=>'date'), $data, $result);
			$i = $i + 1;
		}

		foreach ($data as $key=>$value) {
			// 初始值
			$data[$key]['total_robquantity'] = 0;
			foreach ($value as $k=>$v) {
				if (strpos($k, 'robquantity') === false) {
					continue;
				};
				$data[$key]['total_robquantity'] += $v;
			}
		}
		
		return  json_encode(array('data'=>$data));
	}
	
	/**
	 * 获取游戏各个场次的房间号
	 * @param number $gRoomid
	 * @return Ambigous <multitype:, unknown>
	 */
	private function getGRoomList($gRoomid = 44){
		// 获取房间id
		$rooidList = $this->dbapp->config_sp_room_id_s();
	
		$GRoomList = array();
		$gidRoomId = $this->config->item('gidRoomId');
	
		foreach ($rooidList as $value) {
			if (isset($value['id'])) {
				$arr = explode('.', $value['id']);
	
				if (!isset($arr[0])) {
					continue;
				}
	
				// 取出对应游戏id是否存在
				if(isset($gidRoomId[$arr[0]]) && $gidRoomId[$arr[0]] == $gRoomid && isset($value['name'])){
					$GRoomList[$value['name']][] = $value['id'];
				}
			}
		}
		return $GRoomList;
	}
	
}