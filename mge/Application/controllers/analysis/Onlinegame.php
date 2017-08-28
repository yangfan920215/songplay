<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Onlinegame extends CI_Controller{
	public function __construct(){
		parent::__construct();
	}
	
	
	public function index(){
		#var_dump($this->getGRoomList(100));
		$this->parser->parse('onlinegame/index.html', $this->data);
	}
	
	public function getRoomNameList($gid = 44){
		$gid = isset($_REQUEST['gid']) ? $_REQUEST['gid'] : 44;
		$rooidList = $this->getGRoomList($gid);
		
		$RoomNameList = json_encode(array_keys($rooidList), TRUE);
		
		// 是否需要输入数据给ajax获取
		handleData($RoomNameList, 'gid');
		
		return $RoomNameList;
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
	
	public function getData($gid = 44, $sDate = '2015-11-01', $eDate = '2015-11-11'){
		$gid = isset($_REQUEST['extra_search'][0]['gameId']) ? $_REQUEST['extra_search'][0]['gameId'] : 44;
		$sDate = isset($_REQUEST['extra_search'][1]['sDate']) ? $_REQUEST['extra_search'][1]['sDate'] : $sDate;
		$eDate = isset($_REQUEST['extra_search'][2]['eDate']) ? $_REQUEST['extra_search'][2]['eDate'] : $eDate;
		
		$RoomList = $this->getGRoomList($gid);
		
		// 总局数,总人数
		$gidRoomId = $this->config->item('gidRoomId');
		$gidRoomId = array_flip($gidRoomId);
		// 获取数据库里标识的该游戏总人数房间id
		$RoomList[] = array($gidRoomId[$gid]);
		$data = array();
		
		$i = 0;
		foreach ($RoomList as $value) {
			$str = implode('\',\'', $value);
			$str = '\''.$str.'\'';
			$result = $this->dbapp->mge_sp_room_date_s($sDate, $eDate, $str, $i);
			$data = $i == 0 ? $result : $this->dataext->tableDataMerge('post_date',array('sort'=>'post_date asc','sortType'=>'date'), $data, $result);
			$i++;
			$this->db->reconnect();
		}
		
		// 计算平均值
		$this->getAvg($data, $i - 1);
		
		$i = 0;
		// 新增平均局数值在局数栏
		foreach ($data as &$value1) {
			for($i =0; $i < 10; $i++){
				if(isset($value1[$i.'_num']) && isset($value1[$i.'_pl'])){
					$avg = round($value1[$i.'_num']/$value1[$i.'_pl'], 2);
					$value1[$i.'_num'] = $value1[$i.'_num'] . '(' . $avg . ')';
				}
			}
		}

		// affebcdc97241650940881a4dee9c8de
		echo  json_encode(array('data'=>$data));
	}
	
	private function getAvg(&$data, $i){
		foreach ($data as &$value) {
			@$value['avg'] = $value[$i.'_num']/$value[$i.'_pl'];
		}
	}
	
	
/* 	private function calData(&$data){
		foreach ($data as &$value) {
			$value['total_pl'] = 0;
			$value['total_num'] = 0;
			
			foreach ($value as $k=>$v) {
				if (strpos($k, 'pl') && $k != 'total_pl') {
					$value['total_pl'] += $v;
				}elseif(strpos($k, 'num') && $k != 'total_num'){
					$value['total_num'] += $v;
				}
			}
			
			@$value['avf_num'] = $value['total_num'] != 0 ? $value['total_num']/$value['total_pl'] : 0;
		}
	} */
	
	
}