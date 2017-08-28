<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-7-22
 */

class MostUserAnaly extends CI_Controller{
	
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
								'thList'=>array( '日期', '进场人数', '下注人数', '参与局数', '人均时间'),
								'colList'=>array('post_date', 'in_num', 'bet_num', 'play_num', 'play_time'),
						),
				)
		)->done();
	}
	
	public function ajaxTable(){
		$gid = get_extra('gid');
		$sDate = get_extra('sDate');
		$eDate = get_extra('eDate');
	
		$reslet = $this->dbapp->manage_sp_report_wrplayers($gid, $sDate, $eDate);
		echo json_encode(array('data'=>$reslet));
	}
}