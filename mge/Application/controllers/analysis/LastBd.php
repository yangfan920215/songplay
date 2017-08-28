<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-7-22
 */

class LastBd extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		echo $this->views->setRow(
				array(
						array(
								'type'=>'select',
								'name'=>'gid',
								'list'=>$this->config->item('gameList'),
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
								'thList'=>array( '排名', '玩家ID', '战绩数值', '是否是机器人'),
								'colList'=>array('rank_id', 'uid', 'value', 'robot'),
						),
				)
		)->done();
	}
	
	public function ajaxTable(){
		$gid = get_extra('gid');
		$sDate = get_extra('sDate');
		$eDate = get_extra('eDate');
	
		$reslet = $this->dbapp->manage_sp_report_rank_data($gid, $sDate, $eDate);
		echo json_encode(array('data'=>$reslet));
	}
}