<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-7-22
 */

class Cjj extends CI_Controller{
	
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
								'thList'=>array( '日期', '当天转盘使用人数', '当天送出的红利数', ' 当天红利价值产出', '实际回收'),
								'colList'=>array('post_date', 'num', 'sknum', 'skvalue', 'recovery'),
						),
					)
			)->done();
	}
	
	public function ajaxTable(){
		$gid = get_extra('gid');
		$sDate = get_extra('sDate');
		$eDate = get_extra('eDate');
		
		$reslet = $this->dbapp->manage_sp_report_luckyDraw($gid, $sDate, $eDate);
		echo json_encode(array('data'=>$reslet));
	}
	
}