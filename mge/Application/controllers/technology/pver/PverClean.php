<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-7-29
 */

class PverClean extends CI_Controller{
	
	private $gameCon = array(
		44=>'dzpk',
		100=>'ddz',	
	);
	
	public function __construct(){
		parent::__construct();
		
		$this->load->helper('file');
	}
	
	public function index(){
		echo $this->views->setRow(
				array(
					array(
							'type'=>'select',
							'name'=>'gid',
							'list'=>$this->config->item('gameList'),
					),
				)
		)->setRow(
				array(
					array(
						'name'=>'agent_id',
						'desc'=>'渠道id',
					),
				)
		)->setRow(
				array(
					array(
						'name'=>'pver',
						'desc'=>'版本',
					),
				)
		)->setRow(
			array(
				array(
					'type'=>'button',
					'desc'=>'点击清除',
					'onclick'=>array(
					'type'=>'ajax',
				),
			),
		)
		)->done();
	}
	
	public function ajaxData(){
		$agent_id = getReq('agent_id', '请输入渠道号');
		$gid = getReq('gid', '请选择游戏名称');
		$pver = getReq('pver', '请输入版本号');
		
		if (!isset($this->gameCon[$gid])) {
			_exit('游戏不存在');
		}
		
		// 解压文件保存地址
		$filePver =  './Application/third_party/Ver/pver/' . $this->gameCon[$gid] . '/' . $agent_id . '/' . $pver;
		// 最终zip文件存放地址
		$verDown = $this->config->item('down_file') . $this->gameCon[$gid] . '/android/' . $agent_id . '/' . $pver .'.zip';
		
		$delDownMsg = '';
		$delPverMsg = '';
		
		if (file_exists($filePver)) {
			$ret = delete_files($filePver, true, false, 1);
			
			$delPverMsg = $ret === true ? '文件删除成功;' : '文件删除失败';
		}else{
			$delPverMsg = '文件不存在';;
		}
		
		if (file_exists($verDown)) {
			$ret = unlink($verDown);
				
			$delDownMsg = $ret === true ? 'zip包删除成功;' : 'zip包删除失败';
		}else{
			$delDownMsg = 'zip包不存在;';
		}
		
		_exit($delDownMsg . $delPverMsg, 0);
	}
}