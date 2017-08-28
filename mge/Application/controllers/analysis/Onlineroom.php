<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Onlineroom extends CI_Controller{
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		// 获取初始化页面所需的数据
		$defGames = getDefGames($this->data['gameList']);
		$this->data['morrisData'] = json_encode($this->ajaxData($defGames, $this->data['sDate'], $this->data['eDate']), TRUE);
		$this->data['labels'] = $this->getLables($defGames);
		$this->data['yKeys'] = $this->getYkeys($defGames);
		$this->data['colObj'] = $this->getColObj($defGames);
		$this->data['colObj'] = str_replace('"data"', 'data', $this->data['colObj']);
		$this->data['colObj'] = str_replace('"defaultContent"', 'defaultContent', $this->data['colObj']);
		$this->data['colObj'] = str_replace('"title"', 'title', $this->data['colObj']);
		
		$this->parser->parse('onlineroom.html', $this->data);
	}
	
	public function ajaxTable(){
		if (isset($_POST['gidList'])) {
			$gidList = $this->getGidList($_POST['gidList']);
		}
		
		$sDate = isset($_POST['sDate']) ? $_POST['sDate'] : date('Y-m-d', strtotime('-8 day'));;
		$eDate = isset($_POST['eDate']) ? $_POST['eDate'] : date('Y-m-d', strtotime('-1 day'));;
		
		$colObj = $this->getColObj($gidList);
		echo json_encode(array('data'=>$this->ajaxData($gidList, $sDate, $eDate), 'colObj'=>$colObj), JSON_UNESCAPED_UNICODE);
	}
	
	public function ajaxMorris(){
		if (isset($_POST['gidList'])) {
			$gidList = $this->getGidList($_POST['gidList']);
		}
		
		$sDate = isset($_POST['sDate']) ? $_POST['sDate'] : date('Y-m-d', strtotime('-8 day'));;
		$eDate = isset($_POST['eDate']) ? $_POST['eDate'] : date('Y-m-d', strtotime('-1 day'));;
		
		echo json_encode($this->ajaxData($gidList, $sDate, $eDate), TRUE);
	}
	
	
	/**
	 * 获取报表ykeys
	 * @param unknown $gidList
	 * @return string
	 */
	private function getYkeys($gidList){
		$ykeys = array();
		foreach ($gidList as $value) {
			$ykeys[] = 'game_'.$value;
		}
		return json_encode($ykeys, TRUE);
	}
	
	
	/**
	 * 获取表格的col对象
	 * @param unknown $gidList
	 * @return string
	 */
	private function getColObj($gidList){
		// 获取日期列配置
		$colList = getColPostDate();
		
		$gameList = $this->config->item('gameList');
		$i = 1;
		
		foreach ($gidList as $k=>$value) {
			$colList[$i]['data'] = 'game_'.$value;
			$colList[$i]['defaultContent'] = 'NULL';
			$colList[$i]['title'] = getGname($gameList, $value).'峰值';
			
			$i++;
			
			$colList[$i]['data'] = 'game_'.$value.'_avg';
			$colList[$i]['defaultContent'] = 'NULL';
			$colList[$i]['title'] = getGname($gameList, $value).'平均值';
			
			$i++;
		}
		return json_encode($colList, JSON_UNESCAPED_UNICODE);
	}
	
	/**
	 * 获取labels
	 * @param unknown $gidList
	 * @return string
	 */
	private function getLables($gidList){
		$gameList = $this->config->item('gameList');
		$labels = array();
		$name = '';
		
		foreach ($gidList as $value) {
			foreach ($gameList as $v) {
				if (isset($v['id'], $v['name']) && $v['id'] == $value) {
					$name = $v['name'];
					break;
				}
				continue;
			}
			
			$labels[] = $name;
		}
		// 汉字不进行转码
		return json_encode($labels, JSON_UNESCAPED_UNICODE);
	}

	/**
	 * 获取数据
	 * @param unknown $gidList
	 * @param string $sDate
	 * @param string $eDate
	 * @return string
	 */
	private function ajaxData($gidList, $sDate, $eDate){
		// 初始化数据变量
		$gameDataMax = array();
        $gameDataAvg = array();
        $gameData = array();
        
		foreach ($gidList as $value) {
			$gameDataMax[$value] = $this->dbapp->manage_sp_online_hall_s_total('h', $value, $sDate, $eDate);
			$gameDataMax[$value] = array_reverse($gameDataMax[$value]);
			$gameDataAvg[$value] = $this->dbapp->manage_sp_online_hall_s_total('a', $value, $sDate, $eDate);
			$gameDataAvg[$value] = array_reverse($gameDataAvg[$value]);
		}
		
		$actionStr = array();
		$actionStrAvg = array();
		$actionKey = '';
		$dataKey = array();
		// 修改field
		if(!empty($gameDataMax)) {
			foreach ($gameDataMax as $key => $val) {
				if(!empty($val)) {
					$gameDataMax[$key] = $this->dataext->calData($val, array('quantity'=>'game_'.$key), array());
					$gameDataAvg[$key] = $this->dataext->calData($gameDataAvg[$key], array('quantity'=>'game_'.$key.'_avg'), array());
					$actionStr[] = "\$val['game_".$key."']";
					$actionStrAvg[] = "\$val['game_".$key."_avg']";
					$actionKey = 'game_'.$key;
					$dataKey[] = 'game_'.$key;
				}
				//合并数组
				$gameData[$key] = $this->dataext->tableDataMerge('post_date', array('sort'=>'post_date desc'), $gameDataMax[$key], $gameDataAvg[$key]);
			}
		}
		
		$data = $this->dataext->tableDataMerge2('post_date',array('sort'=>'post_date desc'),$gameData);
		$data = $this->dataext->dataKey($data, $dataKey);
		
		return $data;
	}
	
	/**
	 * 获取gidList
	 * @param unknown $gidList
	 * @return multitype:
	 */
	private function getGidList($gidList){
		return explode('&', str_replace('gidList=', '', $gidList));
	}
}