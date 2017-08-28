<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-7-22
 */

class Tg extends CI_Controller{
	
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
								'thList'=>array( '日期', '推广人数', '推广过5人数', '推广礼包总产出', '玩牌返利总产出', '推广总产出', '人均玩牌局数', '人均玩牌时间', '充值总额'),
								'colList'=>array('post_date', 'num', 'num5', 'giftnum', 'amounts', 'allamounts', 'playnum', 'playtime', 'chargenum'),
						),
					)
			)->done();
	}
	
	public function ajaxTable(){
		$gid = get_extra('gid');
		$sDate = get_extra('sDate');
		$eDate = get_extra('eDate');
		
		$reslet = $this->dbapp->manage_sp_report_promoter($gid, $sDate, $eDate);
		echo json_encode(array('data'=>$reslet));
	}
	
}