<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 用户转化率
 * @author yangf@songplay.cn
 * @date 2015-12-15
 */

class PerPro extends CI_Controller{
	/**
	 * 初始化
	 */
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 入口
	 */
	public function index(){
				echo $this->views->setRow(
				array(
						array(
								'type'=>'select',
								'name'=>'gid',
								'list'=>$this->config->item('gameList'),
						),
						array(
								'name'=>'agent_id',
								'desc'=>'渠道号,-1代指全部渠道',
								'val'=>-1,
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
					'type'=>'table',
					'thList'=>array('日期', '注册人数', '登录人数', '一局转换', '三局转换', '五局转换', '十局转换'),
					'colList'=>array('post_date', 'quantity', 'logoner', 'players1', 'players3', 'players5', 'players10'),
				),
			)
		)->done();
	}
	
	
	public function ajaxTable(){
		$gid = get_extra('gid');
		$cid = get_extra('agent_id');
		$sDate = get_extra('sDate');;
		$eDate = get_extra('eDate');
		
		echo json_encode(array('data'=>$this->data($gid, $cid, $sDate, $eDate)));
	}
	
	
	private function data($gid, $clientId, $sDate, $eDate){
		// 获取留存数据
		$perback = $this->dbapp->manage_sp_event_cal_players($gid, $clientId, $sDate, $eDate);
		
		$register =  $this->dbapp->manage_sp_report_register_s($gid, $clientId, -1, $sDate, $eDate); // -1是版本号
		$logon = $this->dbapp->manage_sp_report_logon_s(-1, $gid, $clientId, -1, $sDate, $eDate);	// 第一个-1是osid,第二个是版本号
		
		$logoner = array();
		foreach ($logon as $key=>$value) {
			$logoner[$key]['logoner'] = $value['quantity'];
			$logoner[$key]['post_date'] = $value['post_date'];
		}
		$result = $this->dataext->tableDataMerge('post_date', array('sort'=>'post_date desc','sortType'=>'date'), $perback, $register, $logoner);
		foreach ($result as $key1=>&$value1){
			if(isset($value1['quantity']) && $value1['quantity'] > 0){
				
				// 兼容数据库未返回该字段的情况
				$value1['players1'] = isset($value1['players1']) ? $value1['players1'] : 0;
				$value1['players3'] = isset($value1['players3']) ? $value1['players3'] : 0;
				$value1['players5'] = isset($value1['players5']) ? $value1['players5'] : 0;
				$value1['players10'] = isset($value1['players10']) ? $value1['players10'] : 0;
				
				$round1 = round( $value1['players1']/$value1['quantity'] * 100 , 2) . "%";
				$round3 = round( $value1['players3']/$value1['quantity'] * 100 , 2) . "%";
				$round5 = round( $value1['players5']/$value1['quantity'] * 100 , 2) . "%";
				$round10 = round( $value1['players10']/$value1['quantity'] * 100 , 2) . "%";
		
				$value1['players1'] =  $value1['players1'] . '(' . $round1 . ')';
				$value1['players3'] =  $value1['players3'] . '(' . $round3 . ')';
				$value1['players5'] =  $value1['players5'] . '(' . $round5 . ')';
				$value1['players10'] =  $value1['players10'] . '(' . $round10 . ')';
			}
		}
		
		return $result;
	}
	
	
}