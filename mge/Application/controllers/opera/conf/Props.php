<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-8-3
 */

class Props extends CI_Controller{
	
	private $jump_role = array(
		'int'=>array(1, 2, 5, 8, 9, 10, 14, 22),
		'roomid'=>array(6, 7),
		'nocheck'=>array(0, 3, 4, 11, 12, 13, 15, 16, 17, 18, 19, 20, 21, 23, 24),
	);
	
	private $jump = array(
		0=>'00-不跳转',			
		1=>'01-打开内嵌页面',
		2=>'02-打开系统页面',
		3=>'03-打开个人中心',
		4=>'04-返回大厅',	
		5=>'05-进入分区',
		6=>'06-进入房间',
		7=>'07-报名比赛',
		8=>'08-打开排行榜',
		9=>'09-兑换中心',
		10=>'10-打开商城',
		11=>'11-打开背包',
		12=>'12-快速开始',
		13=>'13-开发任务',
		14=>'14-快捷支付',
		15=>'15-打开转盘',
		16=>'16-打开喇叭',
		17=>'17-打开推广中心',
		18=>'18-打开激活码兑换',
		19=>'19-打开赢取爽卡界面',
		20=>'20-手动加入私人房',
		21=>'21-手动加入私人房',
		22=>'22-打开活动中心',
		23=>'23-打开邮箱',
		24=>'24-分享',
	);
	
	private $attribute = array(
		'50790407'=>'可合成',
		'34013191'=>'不可合成',	
	);
	
	private $show_id = array(
		'3'=>'在背包显示',
		'4'=>'不在背包显示',	
	);
	
	public function __construct(){
		parent::__construct();
		
		//monitor($this->session->email);
	}
	
	public function index(){
		$this->data['addImgModId'] = 'addImg';
		$this->data['upPropUrlBuff'] = 'upProp';
		
		echo $this->views->setRow(
			array(
				array(
					'type'=>'select',
					'name'=>'gid',
					'list'=>$this->config->item('gameList'),
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
					'type'=>'button',
					'desc'=>'新增道具',
					'col'=>1,
					'noInsert'=>array('id'),
					'onclick'=>array(
						'type'=>'add',
						'field'=>array(
							array(
								'name'=>'id',
								'desc'=>'请输入道具ID',
								'validator'=>array(
									'type'=>'notEmpty,int,remote',
									'maxStr'=>5,
									'min'=>'0',
									'max'=>'99999',
								),
							),	
							array(
								'name'=>'name',
								'desc'=>'请输入道具名称',
								'validator'=>array(
									'type'=>'notEmpty',
									'maxStr'=>10,
								),
							),
							array(
								'type'=>'select',
								'name'=>'game_id',
								'list'=>$this->config->item('gameList'),
							),
							array(
									'type'=>'select',
									'name'=>'show_id',
									'list'=>$this->show_id,
							),
							array(
									'type'=>'select',
									'name'=>'attribute',
									'list'=>$this->attribute,
							),
							array(
									'name'=>'summary',
									'desc'=>'请输入道具使用提示',
									'validator'=>array(
										'type'=>'notEmpty',
										'maxStr'=>64,
									),
							),
							array(
								'type'=>'textarea',
								'name'=>'description',
								'desc'=>'请输入道具描述 ',
								'validator'=>array(
										'type'=>'notEmpty',
										'maxStr'=>255,
								),
							),
							array(
									'type'=>'select',
									'name'=>'jump',
									'list'=>$this->jump,
							),
							array(
									'name'=>'jumpvalue',
									'desc'=>'请输入跳转ID/房间ID',
									'data-showType'=>1,
							),
							array(
									'name'=>'photo',
									'desc'=>'请输入道具图片名',
									'validator'=>array(
											'type'=>'notEmpty,remote',
									),
							),
							array(
									'name'=>'indate',
									'desc'=>'请输入道具有效期',
									'value'=>65535,
									'validator'=>array(
											'type'=>'notEmpty,int',
											'min'=>'0',
											'max'=>'65536',
									),
							),
						),
					)
				),
					array(
							'type'=>'button',
							'desc'=>'编辑道具',
							'col'=>1,
							'onclick'=>array(
									'type'=>'edit',
									'field'=>array(
							array(
								'name'=>'name',
								'desc'=>'请输入道具名称',
								'validator'=>array(
									'type'=>'notEmpty',
									'maxStr'=>10,
								),
							),
							array(
								'type'=>'select',
								'name'=>'game_id',
								'list'=>$this->config->item('gameList'),
							),
							array(
									'type'=>'select',
									'name'=>'show_id',
									'list'=>$this->show_id,
							),
							array(
									'type'=>'select',
									'name'=>'attribute',
									'list'=>$this->attribute,
							),
							array(
									'name'=>'summary',
									'desc'=>'请输入道具使用提示',
									'validator'=>array(
										'type'=>'notEmpty',
										'maxStr'=>64,
									),
							),
							array(
								'type'=>'textarea',
								'name'=>'description',
								'desc'=>'请输入道具描述 ',
								'validator'=>array(
										'type'=>'notEmpty',
										'maxStr'=>255,
								),
							),
							array(
									'type'=>'select',
									'name'=>'jump',
									'list'=>$this->jump,
							),
							array(
									'name'=>'jumpvalue',
									'desc'=>'请输入跳转ID/房间ID',
									'data-showType'=>1,
							),
							array(
									'name'=>'photo',
									'desc'=>'请输入道具图片名',
									'validator'=>array(
											'type'=>'notEmpty,remote',
									),
							),
							array(
									'name'=>'indate',
									'desc'=>'请输入道具有效期',
									'value'=>65535,
									'validator'=>array(
											'type'=>'notEmpty,int',
											'min'=>'0',
											'max'=>'65536',
									),
							),
						),
							)
					),array(
					'type'=>'button',
					'desc'=>'删除道具',
					'col'=>1,
					'onclick'=>array(
						'type'=>'delete',
					),
			),	array(
					'type'=>'button',
					'desc'=>'新增图片',
					'col'=>1,
					'onclick'=>array(
						'type'=>'div',
						'modId'=>$this->data['addImgModId'],
						'url'=>$this->data['upPropUrlBuff'],
					),
			),		array(
					'type'=>'button',
					'desc'=>'启用配置',
					'col'=>2,
					'onclick'=>array(
						'type'=>'ajax',
					),
			)
							
			)
		)->setRow(
			array(
				array(
					'type'=>'table',
					'thList'=>array('选择','游戏名称','道具ID','道具名称','道具描述','道具图片名','道具有效期','跳转类型','跳转ID',),
					'colList'=>array('gname', 'id', 'name', 'description', 'photo', '_indate', '_jump', 'jumpvalue'),
				),
			)
		)->done();
		
		$this->data['jump_role'] = json_encode($this->jump_role, true);
		$this->data['add_model_id'] = $this->views->add_model_id;
		$this->data['add_model_id'] = $this->views->add_model_id;
		$this->data['upUrl'] = $this->views->baseUrl . 'upload';
		$this->data['upCheck'] = $this->views->baseUrl . 'upCheck';
		
		$this->parser->parse('opera/conf/Props.html', $this->data);
	}
	
	public function ajaxData(){
		$this->access->set(2);
		
		$game_id = $this->access->select('gid', '游戏名称', $this->config->item('gameList'));
		
		require $this->config->item('sys_libs_dir') . 'http.class.php';
		require $this->config->item('sys_libs_dir') . 'gameapp.class.php';
		
		$gameapp = gameapp::init();
		
		$gameapp->setGid($game_id);
		
		// 更新道具配置的id=3
		$ret = $gameapp->updateLobbyConfig('', 3, 0, 0);
		
		if (isset($ret['status']) && $ret['status'] == 0) {
			_exit('刷新成功', 0);
		}
		
		$errmsg = isset($ret['msg']) ? $ret['msg'] : '未知错误';
		_exit('刷新失败,原因:' . $errmsg);
	}
	
	public function delete(){
		$this->access->set(3);
		
		$msg = '';
		foreach ($this->access->tData() as $value) {
			if (isset($value['id'])) {
				$ret = $this->dbapp->config_sp_prop_d($value['id']);
		
				$index = isset($value['name']) ?  $value['name'] : $value['id'];
				$this->editMsg($msg, $ret, $index);
			}
		}
		
		if ($msg === '') {
			_exit('删除成功', 0);
		}
		
		_exit($msg);
	}
	
	public function remote(){
		$valid = false;
		
		if (isset($_REQUEST['id'])) {
			if ($_REQUEST['id'] != '' && intval($_REQUEST['id']) > 0) {
				// 数据库检查
				$ret = $this->dbapp->config_sp_prop_s_exists(intval($_REQUEST['id']));
				if (isset($ret['status']) && $ret['status'] == 1) {
					$valid = true;
				}
			};
		}
		
		if (isset($_REQUEST['photo'])) {
			if (file_exists($this->config->item('prop_img_down_url') . $_REQUEST['photo'])) {
				$valid = true;
			};
		}
		
		echo json_encode(array(
				'valid' => $valid,
		));
	}
	
	public function add(){
		$this->access->set(1);
		
		$id = $this->access->int('id', '道具ID', array('min'=>0, 'max'=>99999));
		$name = $this->access->str('name', '道具名称', array('min'=>0, 'max'=>10));
		$game_id = $this->access->select('game_id', '游戏名称', $this->config->item('gameList'));
		$description = $this->access->str('description', '道具描述', array('min'=>0, 'max'=>255));
		$jump = $this->access->select('jump', '跳转动作类型', $this->jump);
		
		// 如果是int类型
		if (in_array($jump, $this->jump_role['int'])) {
			$jumpvalue = $this->access->int('jumpvalue', '跳转ID', array('min'=>0, 'max'=>'int'));	// int最大值
		// 房间id
		}elseif(in_array($jump, $this->jump_role['roomid'])){
			$roomId = $this->access->special('jumpvalue', '房间ID', 'roomId');
			$roomlist = explode('.', $roomId);
			
			$jumpvalue = ($roomlist[3] << 24) + ($roomlist[2] << 16) + ($roomlist[1] << 8) + $roomlist[0];
		// 0
		}elseif(in_array($jump, $this->jump_role['nocheck'])){
			$jumpvalue = 0;
		}
		
		$photo = $this->access->str('photo', '图片名称', array('min'=>3));
		$indate = $this->access->int('indate', '道具有效期', array('min'=>0, 'max'=>65536));
		
		$attribute = $this->access->select('attribute', '合成类型', $this->attribute);
		
		$show_id = $this->access->select('show_id', '是否在背包显示', $this->show_id);
		
		$summary = $this->access->str('summary', '道具使用提示', array('min'=>0, 'max'=>64));
		
		$ret = $this->dbapp->config_sp_prop_i($id, $name, $photo, $indate, 0, $show_id, $game_id, $attribute, $summary, $description, $this->session->email);
		
		if (isset($ret['status']) && $ret['status'] == 0) {
			_exit($name . '添加成功', 0);
		}else{
			monitor($ret);
			_exit($name . '添加失败');
		}
	}
	
	
	public function upload(){
		require $this->config->item('sys_libs_dir') . 'fileUp.class.php';
		
		$file = fileUp::init(array(array('img', 'image/png')), $this->config->item('prop_img_down_url'));
		
		var_export($file->getUpStatus());
	}
	
	public function upCheck(){
		$this->access->set(2);
		
		$fileName = $this->access->str('fileName', '图片名称', array('min'=>3));
		
		if (file_exists($this->config->item('prop_img_down_url') . $fileName)) {
			_exit($fileName . '已存在,若上传会源文件会被直接覆盖!');
		}
		
		_exit('图片不存在', 0);
	}
	
	public function edit(){
		$this->access->set(2);
		
		$name = $this->access->str('name', '道具名称', array('min'=>0, 'max'=>10));
		$game_id = $this->access->select('game_id', '游戏名称', $this->config->item('gameList'));
		$description = $this->access->str('description', '道具描述', array('min'=>0, 'max'=>255));
		$jump = $this->access->select('jump', '跳转动作类型', $this->jump);
		
		// 如果是int类型
		if (in_array($jump, $this->jump_role['int'])) {
			$jumpvalue = $this->access->int('jumpvalue', '跳转ID', array('min'=>0, 'max'=>'int'));	// int最大值
			// 房间id
		}elseif(in_array($jump, $this->jump_role['roomid'])){
			$roomId = $this->access->special('jumpvalue', '房间ID', 'roomId');
			$roomlist = explode('.', $roomId);
				
			$jumpvalue = ($roomlist[3] << 24) + ($roomlist[2] << 16) + ($roomlist[1] << 8) + $roomlist[0];
			// 0
		}elseif(in_array($jump, $this->jump_role['nocheck'])){
			$jumpvalue = 0;
		}
		
		$photo = $this->access->str('photo', '图片名称', array('min'=>3));
		$indate = $this->access->int('indate', '道具有效期', array('min'=>0, 'max'=>65536));
		
		$attribute = $this->access->select('attribute', '合成类型', $this->attribute);
		
		$show_id = $this->access->select('show_id', '是否在背包显示', $this->show_id);
		
		$summary = $this->access->str('summary', '道具使用提示', array('min'=>0, 'max'=>64));
		
		$this->access->set(3);
		
		$msg = '';
		foreach ($this->access->tData() as $value) {
			if (isset($value['id'])) {
				$ret = $this->dbapp->config_sp_prop_u($value['id'], $name, $photo, $indate, 0, $show_id, $game_id, $attribute, $summary, $description, $this->session->email, $jump, $jumpvalue);
				
				$index = isset($value['name']) ?  $value['name'] : $value['id'];
				$this->editMsg($msg, $ret, $index);
			}
		}
		
		if ($msg === '') {
			_exit('修改成功', 0);
		}
		
		_exit($msg);
	}
	
	private function editMsg(&$msg, $ret, $index){
		if (!isset($ret['status']) || $ret['status'] != 0) {
			$msg .= isset($ret['msg']) ? $index . ':' . $ret['msg'] . ';' : $index . ':' . '修改失败;';
		}
	}
	
	public function ajaxTable(){
		$this->access->set(4);
		$gid = $this->access->select('gid', '游戏名称', $this->config->item('gameList'));
		
		$data = $this->dbapp->config_sp_prop_s_game(-1, $gid, -1);
		
		foreach ($data as &$value) {
			if (isset($value['indate'])) {
				$value['_indate'] = $value['indate'] . '天';
			};
			
			
			if (isset($value['game_id'])) {
				$value['gname'] = isset($this->config->item('gameList')[$value['game_id']]) ? $this->config->item('gameList')[$value['game_id']] : '未知游戏';
			}
			
			if (isset($value['jump'])) {
				$value['_jump'] = isset($this->jump[$value['jump']]) ? $this->jump[$value['jump']] : NULL;
			}
			
			if (isset($value['jump']) && isset($value['jumpvalue']) && in_array($value['jump'], $this->jump_role['roomid'])) {
				$roomId_0 = ($value['jumpvalue'] & 0xFF000000) >> 24;
				$roomId_1 = ($value['jumpvalue'] & 0xFF0000) >> 16;
				$roomId_2 = ($value['jumpvalue'] & 0xFF00) >> 8;
				$roomId_3 = ($value['jumpvalue'] & 0xFF);
				$value['jumpvalue'] = $roomId_3 . '.' . $roomId_2 . '.' . $roomId_1 . '.' . $roomId_0;
			}
		}
		
		echo json_encode(array('data'=>$data));
	}
	
/* 	private function formatdate($t){
		$t=intval($t);
	
		$d=floor($t/(60*60*24));
		$h=floor(($t-$d*60*60*24)/(60*60));
		$M=floor(($t-$d*(60*60*24)-$h*60*60)/60);
		$s=floor($t-$d*(60*60*24)-$h*60*60-$M*60);
		$str="";
		if($d>0){
			$str=$str.$d."天";
		}
		if($h>0){
			$str=$str.$h."小时";
		}
		if($M>0){
			$str=$str.$M."分";
		}
		if($s>0){
			$str=$str.$s."秒";
		}
		return $str;
	} */
}