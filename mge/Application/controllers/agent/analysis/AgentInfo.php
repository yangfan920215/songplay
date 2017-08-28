<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 建立渠道
 * @author yangf@songplay.cn
 * @date 2016-4-20
 */

class AgentInfo extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
/* 		echo $this->views->setRow(
				array(
						array(
								'type'=>'select',
								'name'=>'gid',
								'list'=>$this->config->item('gameList'),
						),
						array(
								'type'=>'select',
								'name'=>'level',
								'list'=>$this->config->item('cList'),
						),
						array(
								'type'=>'sedate',
						),
						array(
								'type'=>'button',
								'col'=>1,
								'onclick'=>array(
									'type'=>'reload',
								),
						),
				)
		)->setRow(
			array(
				array(
					'type'=>'_table',
					'thList'=>array('服务费(总)', '私人房服务费(总)', '转盘消耗(总)', '比赛场报名费(总)', '荷官打赏(总)', '互动道具(总)', '金币购买道具(总)', '红黑游戏(总)', '比赛重购(总)', '比赛增购(总)'),
				),
			)
		)->setRow(
			array(
				array(
					'type'=>'table',
					'thList'=>array('渠道号', '渠道等级', '公司名称', '新注册用户数', '服务费', '分成比例', '分成后服务费'),
					'colList'=>array('client_id', 'level', 'name', 'registerusers', 'server_money'=>array('class'=>'details-control', 'orderable'=>false, 'details'=>array(
						'revenue'=>'私人房服务费',
						'luckdraw'=>'转盘消耗',
						'applycost'=>'比赛场报名费',
						'playing'=>'荷官打赏',
						'interact'=>'互动道具',
						'buy'=>'金币购买道具',
						'rbgame'=>'红黑游戏',
						'rebuy'=>'比赛重购',
						'addbuy'=>'比赛增购',
					),), 'intoratio', 'divide'),
				),
			)
		)->done(); */
		
		$this->data['bi'] = $this->config->item('conn_db')[$_SESSION['conn_db_id']]['bl'];
		
		$this->parser->parse('agent/AgentInfo.html', $this->data);
	}
	
	public function ajaxTable(){
		$gid = get_extra('gameId');
		$sDate = get_extra('sDate');
		$eDate = get_extra('eDate');
		$level = get_extra('level');
		
		// 2016-06-29新增,渠道商用户查询开始日志不得大于2016-06-27
		$info = $this->dbapp->manage_sp_get_client_info($_SESSION['authId']);

		if (isset($info['id']) && $sDate < '2016-06-27') {
			$sDate = '2016-06-27';
		}
		
		
		$data = $this->dbapp->manage_sp_web_report_client($gid, $_SESSION['authId'], $sDate, $eDate, $level);

		foreach ($data as $key=>&$value) {
			$value['delayprop'] = isset($value['delayprop']) ? $value['delayprop'] : 0;
			$value['sr_revenue'] = isset($value['sr_revenue']) ? $value['sr_revenue'] : 0;
			$value['yl_revenue'] = isset($value['yl_revenue']) ? $value['yl_revenue'] : 0;
			$value['luckdraw'] = isset($value['luckdraw']) ? $value['luckdraw'] : 0;
			$value['applycost'] = isset($value['applycost']) ? $value['applycost'] : 0;
			$value['playing'] = isset($value['playing']) ? $value['playing'] : 0;
			$value['interact'] = isset($value['interact']) ? $value['interact'] : 0;
			$value['buy'] = isset($value['buy']) ? $value['buy'] : 0;
			$value['rbgame'] = isset($value['rbgame']) ? $value['rbgame'] : 0;
			$value['rebuy'] = isset($value['rebuy']) ? $value['rebuy'] : 0;
			$value['addbuy'] = isset($value['addbuy']) ? $value['addbuy'] : 0;
	
			$value['server_money'] = $value['delayprop'] + $value['sr_revenue'] + $value['yl_revenue'] + $value['luckdraw'] + $value['applycost'] + $value['playing'] + $value['interact'] + $value['buy'] + $value['rbgame'] + $value['rebuy'] + $value['addbuy'];
		
			$value['intoratio'] = isset($value['intoratio']) ? ($value['intoratio'] * 100) . '%' : null;
		}
		
/* 		$upcid = $this->dbapp->manage_sp_get_ac_winlist($this->config->item('upcid'), $sDate, $eDate);
		
		$count_cid = array();
		foreach ($upcid as $key => $value1) {
			if (isset($value1['priceInfo'])) {
				$arr = explode(':', $value1['priceInfo']);
				if (isset($arr[1])) {
					if (isset($count_cid[$arr[1]])) {
						$count_cid[$arr[1]]++;
					}else{
						$count_cid[$arr[1]] = 1;
					}
				}
				continue;
			}
			continue;
		}
		
		
		foreach ($count_cid as $key1 => $value1) {
			foreach ($data as &$value2) {
				if (isset($value2['client_id']) && isset($value2['registerusers']) && $value2['client_id'] == $key1) {
					$value2['registerusers'] += $value1;
				}
			}
		} */
		
		$ret = $this->dbapp->manage_sp_get_client_info($_SESSION['authId']);
		
		$bili = -1;
		if (isset($ret['id'])) {
			$bili = $ret['intoratio'];
		}
		
		// 统计服务费
		
		echo json_encode(array('data'=>$data, 'bili'=>$bili));
	}
}
