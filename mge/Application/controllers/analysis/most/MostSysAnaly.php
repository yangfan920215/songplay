<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-7-22
 */

class MostSysAnaly extends CI_Controller{
	
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
								'thList'=>array( '日期', '单局最高赢钱', '个人最高赢钱', '投注总额', '人均投注额', '服务费回收', '系统实际输赢', '人均输赢'),
								'colList'=>array('post_date', 'verwin', 'perwin', 'totalbet', 'perbet', 'revenue', 'syswin', 'perresult'),
						),
				)
		)->done();
	}
	
	public function ajaxTable(){
		$gid = get_extra('gid');
		$sDate = get_extra('sDate');
		$eDate = get_extra('eDate');
	
		$reslet = $this->dbapp->manage_sp_report_wranalysis($gid, $sDate, $eDate);
		echo json_encode(array('data'=>$reslet));
	}
}