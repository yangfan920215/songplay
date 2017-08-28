<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-7-22
 */

class MostXzAnaly extends CI_Controller{
	
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
								'thList'=>array( '日期', '1区投注', '1区最高', '1区人均', '2区投注', '2区最高', '2区人均', '3区投注', '3区最高', '3区人均', '4区投注', '4区最高', '4区人均'),
								'colList'=>array('post_date', 'bet1_bet', 'bet1_max', 'bet1_avg', 'bet2_bet', 'bet2_max', 'bet2_avg', 'bet3_bet', 'bet3_max', 'bet3_avg', 'bet4_bet', 'bet4_max', 'bet4_avg'),
						),
				)
		)->done();
	}
	
	public function ajaxTable(){
		$gid = get_extra('gid');
		$sDate = get_extra('sDate');
		$eDate = get_extra('eDate');
	
		$reslet = $this->dbapp->manage_sp_report_wr_versusbet($gid, $sDate, $eDate);
		echo json_encode(array('data'=>$reslet));
	}
}