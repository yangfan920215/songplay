<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-6-29
 */

class Vip extends CI_Controller{
	
	private $ths = array(
		
	);
	
	private $cols = array(
			
	);
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		// 非超管不需要选择渠道
		$info = $this->dbapp->manage_sp_get_client_info($_SESSION['authId']);
		
		if (isset($info['id'])) {
			$searchRow = array(
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
				);
		}else{
			$searchRow = array(
					array(
							'name'=>'agent_id',
							'desc'=>'渠道id',
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
			);
		}
		
		echo $this->views->setRow(
				$searchRow
		)->setRow(
				array(
						array(
								'type'=>'table',
								'thList'=>array('渠道号', 'guid', '昵称', '绑定渠道时间', '最后登录时间', '服务费'),
								'colList'=>array('client_id', 'guid', 'nickname', 'post_date_client', 'post_date', 'revenue'),
						),
				)
		)->done();
	}
	
	public function ajaxTable(){
		$sDate = get_extra('sDate');
		$eDate = get_extra('eDate');
		
		$info = $this->dbapp->manage_sp_get_client_info($_SESSION['authId']);
		if (isset($info['id'])) {
			$agent_id = $info['clientid'];
		}else{
			$agent_id = isset($_REQUEST['extra_search'][0]['req']['agent_id']) ? $_REQUEST['extra_search'][0]['req']['agent_id'] : execExit('渠道id不存在');
			if ($agent_id == '') {
				echo json_encode(array('data'=>array()));
				exit;
			}
		}
	
		$data = $this->dbapp->manage_sp_report_clientUser($agent_id, $sDate, $eDate);
		
		echo json_encode(array('data'=>$data));
		exit;
	}
	
	
}