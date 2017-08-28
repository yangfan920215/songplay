<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 运营报表
 * @author yangf@songplay.cn
 * @date 2015-12-8
 */

class Analysis extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index() {
		$this->parser->parse('analysis/analysis.html', $this->data);
	} 
	
	public function ajaxTable(){
		$gameId = get_extra('gid');
		$sDate = get_extra('sDate');
		$eDate = get_extra('eDate');
		
		$this->getData($gameId, $sDate, $eDate);
	}
	
	public function getData($gameId = 44, $sDate = '', $eDate = '', $osId = -1, $clientId = -1, $verId = -1){
		
		// D($_REQUEST['extra_search']);
		
		// 默认查询当天五天内数据
		$gameId = isset($_REQUEST['extra_search'][0]['gameId']) ? $_REQUEST['extra_search'][0]['gameId'] : 44;
		$sDate = isset($_REQUEST['extra_search'][1]['sDate']) ? $_REQUEST['extra_search'][1]['sDate'] : date('Y-m-d', strtotime('-8 day'));
		$eDate = isset($_REQUEST['extra_search'][2]['eDate']) ? $_REQUEST['extra_search'][2]['eDate'] : date('Y-m-d', strtotime('-1 day'));
		
		// 参数列表
		$param = array($osId, $gameId, $clientId, $verId, $sDate, $eDate);
		$param1 = array($gameId, $clientId, $verId, $sDate, $eDate);
		$param2 = array($gameId, $sDate, $eDate);
		
/* 		D($param, false);
		D($param1, false);
		D($param2); */
		
		// 登录数据
		$logon = $this->dbapp->manage_sp_report_logon_s($osId, $gameId, $clientId, $verId, $sDate, $eDate);
		
		// 注册数据
		$register = $this->dbapp->manage_sp_report_register_s($gameId, $clientId, $verId, $sDate, $eDate);
		
		// 统计总注册人数
		$register_total = 0;
		foreach ($register as $value_reg) {
			$register_total += $value_reg['quantity'];
		}
		
		
		// 日收入，日付费人数，付费渗透率，arppu值
		$charge = $this->dbapp->manage_sp_report_charge_s($osId, $gameId, $clientId, $verId, $sDate, $eDate);
		
		// 1局转化,5局转化
		$conv = $this->dbapp->manage_sp_report_conv_s($gameId, $clientId, $verId, $sDate, $eDate);
		
		// 人均在线时长
		$avgonl = $this->dbapp->manage_sp_report_avgonl_s($gameId, $clientId, $verId, $sDate, $eDate);
		
		// 最高在线人数，最高在线时间点
		$maxonl = $this->dbapp->manage_sp_report_maxonl_s($gameId, $sDate, $eDate);
		
		// 新增1局转化,新增5局转化
		$conv1 = $this->dbapp->manage_sp_report_conv_n($gameId, $clientId, $verId, $sDate, $eDate);
		
		// 新增人均在线时长
		$avgonl1 = $this->dbapp->manage_sp_report_avgonl_n($gameId, $clientId, $verId, $sDate, $eDate);
		
		// 日收入,日付费人数,付费渗透率,arppu值
		$charge1 = $this->dbapp->manage_sp_report_charge_n($osId, $gameId, $clientId, $verId, $sDate, $eDate);
		
		//日收入，日付费人数，付费渗透率，arppu值
		
		foreach ($charge as $key => $val) {
			$charge[$key]['amounts'] = sprintf('%.2f', ($val['amounts']/100));
			$charge[$key]['infilter'] = sprintf('%.2f', ($val['infilter']*100)). '%';
			$charge[$key]['infilter_im'] = sprintf('%.2f', ($val['infilter_im']*100)). '%';
			$charge[$key]['arppu'] = sprintf('%.2f', ($val['arppu']/100));
			$charge[$key]['arppu_im'] = sprintf('%.2f', ($val['arppu_im']/100));
			$charge[$key]['arppu_im'] = sprintf('%.2f', ($val['arppu_im']/100));
		}
		
		//1局转化,5局转化
		foreach ($conv as $key => $val) {
			$conv[$key]['conv1'] = sprintf('%.2f', ($val['conv1']*100)). '%';
			$conv[$key]['conv5'] = sprintf('%.2f', ($val['conv5']*100)). '%';
			$conv[$key]['conv1_im'] = sprintf('%.2f', ($val['conv1_im']*100)). '%';
			$conv[$key]['conv5_im'] = sprintf('%.2f', ($val['conv5_im']*100)). '%';
		}
		
		//人均在线时长
		foreach ($avgonl as $key => $val) {
			$avgonl[$key]['avg_time'] = sprintf('%.2f', ($val['avg_time']/60));
			$avgonl[$key]['avg_time_im'] = sprintf('%.2f', ($val['avg_time_im']/60));
			$avgonl[$key]['yl_time'] = sprintf('%.2f', ($val['yl_time']/60));
		}
		
		//新增1局转化,新增5局转化
		foreach ($conv1 as $key => $val) {
			$conv1[$key]['conv1'] = sprintf('%.2f', ($val['conv1']*100)). '%';
			$conv1[$key]['conv5'] = sprintf('%.2f', ($val['conv5']*100)). '%';
			$conv1[$key]['conv1_im'] = sprintf('%.2f', ($val['conv1_im']*100)). '%';
			$conv1[$key]['conv5_im'] = sprintf('%.2f', ($val['conv5_im']*100)). '%';
		}
		
		//日收入,日付费人数,付费渗透率,arppu值
		foreach ($charge1 as $key => $val) {
			$charge1[$key]['amounts'] = sprintf('%.2f', ($val['amounts']/100));
			$charge1[$key]['infilter'] = sprintf('%.2f', ($val['infilter']*100)). '%';
			$charge1[$key]['infilter_im'] = sprintf('%.2f', ($val['infilter_im']*100)). '%';
			$charge1[$key]['arppu'] = sprintf('%.2f', ($val['arppu']/100));
			$charge1[$key]['arppu_im'] = sprintf('%.2f', ($val['arppu_im']/100));
			$charge1[$key]['arppu_im'] = sprintf('%.2f', ($val['arppu_im']/100));
		}
		
		//人均在线时长
		foreach ($avgonl1 as $key => $val) {
			$avgonl1[$key]['avg_time'] = sprintf('%.2f', ($val['avg_time']/60));
			$avgonl1[$key]['avg_time_im'] = sprintf('%.2f', ($val['avg_time_im']/60));
			$avgonl1[$key]['yl_newtime'] = sprintf('%.2f', ($val['yl_newtime']/60));
		}
		
		// 数据处理
		$logon = $this->dataext->calData($logon, $changes = array('quantity_im'=>'quantity_im_logon'));
		$register = $this->dataext->calData($register, $changes = array('quantity'=>'quantity_register', 'quantity_im' => 'quantity_im_register'));
		$charge = $this->dataext->calData($charge, $changes = array('quantity'=>'quantity_charge', 'quantity_im' => 'quantity_im_charge'));
		$charge = $this->dataext->calData($charge, $changes = array('infilter_im'=>'infilter_im_charge'));
		$conv1 = $this->dataext->calData($conv1, $changes = array('conv1'=>'conv1_conv1', 'conv5' => 'conv5_conv1'));
		$conv1 = $this->dataext->calData($conv1, $changes = array('conv1_im'=>'conv1_im_conv1', 'conv5_im' => 'conv5_im_conv1'));
		$avgonl1 = $this->dataext->calData($avgonl1, $changes = array('avg_time'=>'avg_time_avgonl1', 'avg_time_im' => 'avg_time_im_avgonl1'));
		$charge1 = $this->dataext->calData($charge1, $changes = array('quantity'=>'quantity_charge1', 'amounts' => 'amounts_charge1'));
		$charge1 = $this->dataext->calData($charge1, $changes = array('infilter'=>'infilter_charge1', 'arppu' => 'arppu_charge1'));
		$charge1 = $this->dataext->calData($charge1, $changes = array('quantity_im'=>'quantity_im_charge1', 'infilter_im' => 'infilter_im_charge1'));
		$charge1 = $this->dataext->calData($charge1, $changes = array('arppu_im'=>'arppu_im_charge1'));
		
		// 数据汇总
		$result = $this->dataext->tableDataMerge('post_date',array('sort'=>'post_date asc','sortType'=>'date'),$logon,$register,$charge,$conv,$avgonl,$maxonl,$conv1,$avgonl1,$charge1);		
		
		
		echo json_encode(array('data'=>$result));
	}
}