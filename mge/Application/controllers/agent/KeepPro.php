<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 用户留存率
 * @author yangf@songplay.cn
 * @date 2015-12-15
 */

class KeepPro extends CI_Controller{
	
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
					'thList'=>array('日期', '注册人数', '登录人数', '次日留存', '3日留存', '7日留存', '14日留存', '30日留存'),
					'colList'=>array('post_date', 'quantity', 'logoner', 'comeback1', 'comeback3', 'comeback7', 'comeback14', 'comeback30'),
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
	
	/**
	 * 获取数据
	 * @param unknown $gid
	 * @param unknown $clientId
	 * @param unknown $sDate
	 * @param unknown $eDate
	 * @return unknown
	 */
	private function data($gid, $clientId, $sDate, $eDate){
		// 获取留存数据
		$comeback = $this->dbapp->manage_sp_event_cal_comeback($gid, $clientId, $sDate, $eDate);
		
		$register =  $this->dbapp->manage_sp_report_register_s($gid, $clientId, -1, $sDate, $eDate);	// -1是版本号
		$logon = $this->dbapp->manage_sp_report_logon_s(-1, $gid, $clientId, -1, $sDate, $eDate);	// 第一个-1是osid,第二个是版本号
		
		$logoner = array();
		foreach ($logon as $key=>$value) {
			$logoner[$key]['logoner'] = $value['quantity'];
			$logoner[$key]['post_date'] = $value['post_date'];
		}
		
		$result = $this->dataext->tableDataMerge('post_date', array('sort'=>'post_date desc','sortType'=>'date'), $comeback, $register, $logoner);
		
		foreach ($result as $key1=>&$value1){
			if(isset($value1['quantity']) && $value1['quantity'] > 0){
				if(isset($value1['comeback1'])){
					$round1 = round( $value1['comeback1']/$value1['quantity'] * 100 , 2) . "%";
					$value1['comeback1'] =  $value1['comeback1'] . '(' . $round1 . ')';
				}
					
				if(isset($value1['comeback3'])){
					$round3 = round( $value1['comeback3']/$value1['quantity'] * 100 , 2) . "%";
					$value1['comeback3'] =  $value1['comeback3'] . '(' . $round3 . ')';
				}
					
				if(isset($value1['comeback7'])){
					$round7 = round( $value1['comeback7']/$value1['quantity'] * 100 , 2) . "%";
					$value1['comeback7'] =  $value1['comeback7'] . '(' . $round7 . ')';
				}
					
				if(isset($value1['comeback14'])){
					$round14 = round( $value1['comeback14']/$value1['quantity'] * 100 , 2) . "%";
					$value1['comeback14'] =  $value1['comeback14'] . '(' . $round14 . ')';
				}
					
				if(isset($value1['comeback30'])){
					$round30 = round( $value1['comeback30']/$value1['quantity'] * 100 , 2) . "%";
					$value1['comeback30'] =  $value1['comeback30'] . '(' . $round30 . ')';
				}
			}
		}
		
		return $result;
	}
	
}