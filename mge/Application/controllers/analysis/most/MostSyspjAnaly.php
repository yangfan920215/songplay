<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-7-22
 */

class MostSyspjAnaly extends CI_Controller{
	
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
								'thList'=>array( '日期', '【1-胜】', '【1-负】', '【2-胜】', '【2-负】', '【3-胜】', '【3-负】', '【4-胜】', '【4-负】'),
								'colList'=>array('post_date', 'win1', 'lose1', 'win2', 'lose2', 'win3', 'lose3', 'win4', 'lose4'),
						),
				)
		)->done();
	}
	
	public function ajaxTable(){
		$gid = get_extra('gid');
		$sDate = get_extra('sDate');
		$eDate = get_extra('eDate');
	
		$reslet = $this->dbapp->manage_sp_report_wr_versuslist($gid, $sDate, $eDate);
		echo json_encode(array('data'=>$reslet));
	}
}