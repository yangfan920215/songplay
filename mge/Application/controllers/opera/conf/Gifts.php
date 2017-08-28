<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-8-3
 */

class Gifts extends CI_Controller{
	
	private $config_auto_use = array(
		array('id'=>0, 'name'=>'手动解包'),
		array('id'=>1, 'name'=>'自动解包'),
	);
	
	private $is_broadcast = array(
		array('id'=>0, 'name'=>'不广播'),
		array('id'=>1, 'name'=>'广播'),
	);
	
	private $show_id = array(
		array('id'=>3, 'name'=>'在背包显示'),
		array('id'=>4, 'name'=>'不在背包显示'),
	);
	
	private $config_limit = array(
			array('id'=>0, 'name'=>'不限量'),
			array('id'=>1, 'name'=>'限量'),
	);
	
	public function __construct(){
		parent::__construct();
		
		//monitor($this->session->email);
	}
	
	public function index(){
 		$this->data['addImgModId'] = 'addImg';
		$this->data['upPropUrlBuff'] = 'upProp';

		$data = $this->dbapp->config_sp_prop_s_all();
		
		$props = array();
		foreach ($data as $key=>$value) {
			$props[$key]['id'] = $value['id'];
			$props[$key]['name'] = $value['name'];
		}
		
		$this->data['props'] = $props;
		array_unshift($this->data['props'], array(
			'id'=>0,
			'name'=>'金币',
		));
		$this->data['upUrl'] = $this->views->baseUrl . 'upload';
		$this->data['upCheck'] = $this->views->baseUrl . 'upCheck';
		
		$this->data['config_auto_use'] = $this->config_auto_use;
		$this->data['is_broadcast'] = $this->is_broadcast;
		$this->data['show_id'] = $this->show_id;
		
		$this->parser->parse('opera/conf/Gifts.html', $this->data);
	}
	
	public function getProps(){
		$id = isset($_REQUEST['tData'][0]['id']) ? $_REQUEST['tData'][0]['id'] : _exit('请选中一行数据');
		
		$data = $this->dbapp->config_sp_gifts_ext_s($id);
		
		echo json_encode($data);
	}
	
	public function ajaxData(){
		$this->access->set(2);
		
		$game_id = $this->access->select('gid', '游戏名称', $this->config->item('gameList'));
		
		require $this->config->item('sys_libs_dir') . 'http.class.php';
		require $this->config->item('sys_libs_dir') . 'gameapp.class.php';
		
		$gameapp = gameapp::init();
		
		$gameapp->setGid($game_id);
		
		// 更新礼包配置的id=3
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
				$ret = $this->dbapp->config_sp_gifts_dn($value['id']);
		
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
				$ret = $this->dbapp->manage_sp_gifts_s_exists(intval($_REQUEST['id']));
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
	
	private function checkCountRule(){
		
	}
	
	public function add(){
		$this->access->set(1);
		
		$id = $this->access->int('id', '礼包ID', array('min'=>0, 'max'=>65535));
		$name = $this->access->str('name', '礼包名称', array('min'=>0, 'max'=>10));
		$gid = $this->access->select('gameId', '游戏名称', $this->config->item('gameList'));
		$description = $this->access->str('description', '礼包描述', array('min'=>0, 'max'=>255));
		$jump = $this->access->select('auto_use', '自动解包', $this->config_auto_use);
		$summary = $this->access->str('summary', '礼包使用提示', array('min'=>0, 'max'=>64));
		$photo = $this->access->str('photo', '图片名称', array('min'=>3));
		
		$auto_use = $this->access->select('auto_use', '是否自动解包', $this->config_auto_use);
		$show_id = $this->access->selectv1('show_id', '是否在背包显示', $this->show_id);
		$count = $this->access->int('count', '获取道具数量', array('min'=>0, 'max'=>10));
		
		// 验证礼包和道具关系表的数据,
		if (isset($_REQUEST['reqData']['add_props']) && is_array($_REQUEST['reqData']['add_props']) && isset($_REQUEST['reqData']['min']) && isset($_REQUEST['reqData']['max']) && isset($_REQUEST['reqData']['probability']) && isset($_REQUEST['reqData']['limit'])) {
			$add_props = $_REQUEST['reqData']['add_props'];
			$min = $_REQUEST['reqData']['min'];
			$max = $_REQUEST['reqData']['max'];
			$probability = $_REQUEST['reqData']['probability'];
			$limit = $_REQUEST['reqData']['limit'];
			$broadcast = $_REQUEST['reqData']['broadcast'];
			
			$gift_props = array();
			$probability_sum = 0;
			for ($i = 0; $i < count($add_props); $i++) {
				if (!isset($add_props[$i]['value'], $min[$i]['value'], $max[$i]['value'], $probability[$i]['value'], $limit[$i]['value'], $broadcast[$i]['value'])) {
					_exit('礼包-道具关系配置有空值,请重新输入');
				}
				
				// 检查道具是否存在,不存在过滤
				if ($add_props[$i]['value'] != 0) {
					$ret_check_prop = $this->dbapp->config_sp_prop_s_exists($add_props[$i]['value']);
					if (!isset($ret_check_prop['status']) || $ret_check_prop['status'] != 0) {
						_exit('ID:' . $add_props[$i]['value'] . '不存在');
					}
				}
				
				$min[$i]['value'] = intval($min[$i]['value']);
				$max[$i]['value'] = intval($max[$i]['value']);
				
				if ($min[$i]['value'] < 0 || $min[$i]['value'] > 10000 || $max[$i]['value'] < 0 || $max[$i]['value'] > 10000 || $min[$i]['value'] > $max[$i]['value']) {
					_exit('Min和Max的值不能小于0或大于10000,且Max不能小于Min');;
				}
				
				// 关系概率和必须为1
				$probability_sum += floatval($probability[$i]['value']);
				
				$value = isset($add_props[$i]['value']) ? $add_props[$i]['value'] : '';
			}
			
			if ($probability_sum != 1) {
				_exit('道具概率和不为一');
			}
			
		}
		
		// 插入礼包
		$ret = $this->dbapp->config_sp_gifts_i($id, $name, $photo, 0, $show_id, $gid, 34013189, $auto_use, $summary, $description, $this->session->email, $count);
		if (!isset($ret['status']) || $ret['status'] != 0) {
			if (isset($ret['data'])) {
				_exit($ret['data']);
			}
			_exit('礼包新增失败,请检查礼包参数或通知管理员!');
		}
		
		// 插入礼包和道具的关系信息
		for ($i = 0; $i < count($add_props); $i++) {
			$ret = $this->dbapp->config_sp_gifts_ext_i($id, $add_props[$i]['value'], $min[$i]['value'], $max[$i]['value'], $probability[$i]['value'], $limit[$i]['value'], $broadcast[$i]['value'], $this->session->email);
			// 一旦失败即可回滚
			if (!isset($ret['status']) || $ret['status'] != 0) {
				D($ret);
				$ret = $this->dbapp->config_sp_gifts_dn($id);
				_exit('礼包新增失败,请检查礼包参数或通知管理员!');
			}
		}
		
		_exit($name . '添加成功', 0);
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
		
		
		$name = $this->access->str('name', '礼包名称', array('min'=>0, 'max'=>10));
		$gid = $this->access->select('gameId', '游戏名称', $this->config->item('gameList'));
		$description = $this->access->str('description', '礼包描述', array('min'=>0, 'max'=>255));
		$jump = $this->access->select('auto_use', '自动解包', $this->config_auto_use);
		$summary = $this->access->str('summary', '礼包使用提示', array('min'=>0, 'max'=>64));
		$photo = $this->access->str('photo', '图片名称', array('min'=>3));
		
		$auto_use = $this->access->select('auto_use', '是否自动解包', $this->config_auto_use);
		$show_id = $this->access->selectv1('show_id', '是否在背包显示', $this->show_id);
		$count = $this->access->int('count', '获取道具数量', array('min'=>0, 'max'=>99999));
		
		$this->access->set(3);
		$tData = $this->access->settData(0);
		
		$id = $this->access->int('id', '礼包ID', array('min'=>0, 'max'=>65535));
		
		// 验证礼包和道具关系表的数据,
		if (isset($_REQUEST['add_props']) && is_array($_REQUEST['add_props']) && isset($_REQUEST['min']) && isset($_REQUEST['max']) && isset($_REQUEST['probability']) && isset($_REQUEST['limit'])) {
			$add_props = $_REQUEST['add_props'];
			$min = $_REQUEST['min'];
			$max = $_REQUEST['max'];
			$probability = $_REQUEST['probability'];
			$limit = $_REQUEST['limit'];
			$broadcast = $_REQUEST['broadcast'];
			
			$gift_props = array();
			$probability_sum = 0;
			for ($i = 0; $i < count($add_props); $i++) {
				if (!isset($add_props[$i]['value'], $min[$i]['value'], $max[$i]['value'], $probability[$i]['value'], $limit[$i]['value'], $broadcast[$i]['value'])) {
					_exit('礼包-道具关系配置有空值,请重新输入');
				}
				
				// 检查道具是否存在,不存在过滤
				if ($add_props[$i]['value'] != 0) {
					$ret_check_prop = $this->dbapp->config_sp_prop_s_exists($add_props[$i]['value']);
					if (!isset($ret_check_prop['status']) || $ret_check_prop['status'] != 0) {
						_exit('ID:' . $add_props[$i]['value'] . '不存在');
					}
				}

				
				$min[$i]['value'] = intval($min[$i]['value']);
				$max[$i]['value'] = intval($max[$i]['value']);
				
				if ($min[$i]['value'] < 0 || $min[$i]['value'] > 10000 || $max[$i]['value'] < 0 || $max[$i]['value'] > 10000 || $min[$i]['value'] > $max[$i]['value']) {
					_exit('Min和Max的值不能小于0或大于10000,且Max不能小于Min');;
				}
				
				// 关系概率和必须为1
				$probability_sum += floatval($probability[$i]['value']);
				
				$value = isset($add_props[$i]['value']) ? $add_props[$i]['value'] : '';
			}
			
			if ($probability_sum != 1) {
				_exit('道具概率和不为一');
			}
			
		}
		
		// 插入礼包
		$ret = $this->dbapp->config_sp_gifts_u($id, $name, $photo, 0, $show_id, $gid, 34013189, $auto_use, $summary, $description, $this->session->email, $count);
		if (!isset($ret['status']) || $ret['status'] != 0) {
			_exit('礼包修改失败,请检查礼包参数或通知管理员!');
		}
		
		// 清空礼包关系
		$this->dbapp->config_sp_gifts_ext_d_all($id);
		
		// 插入礼包和道具的关系信息
		for ($i = 0; $i < count($add_props); $i++) {
			$ret = $this->dbapp->config_sp_gifts_ext_i($id, $add_props[$i]['value'], $min[$i]['value'], $max[$i]['value'], $probability[$i]['value'], $limit[$i]['value'], $broadcast[$i]['value'], $this->session->email);
			// 一旦失败即可回滚
			if (!isset($ret['status']) || $ret['status'] != 0) {
				$ret = $this->dbapp->config_sp_gifts_dn($id);
				_exit('礼包新增失败,请检查礼包参数或通知管理员!');
			}
		}
		
		_exit($name . '修改成功', 0);
	}
	
	private function editMsg(&$msg, $ret, $index){
		if (!isset($ret['status']) || $ret['status'] != 0) {
			$msg .= isset($ret['msg']) ? $index . ':' . $ret['msg'] . ';' : $index . ':' . '修改失败;';
		}
	}
	
	public function ajaxTable(){
		$this->access->set(4);
		$gid = $this->access->select('gid', '游戏名称', $this->config->item('gameList'));
		
		$data = $this->dbapp->config_sp_gifts_s('', $gid, '');
		
		foreach ($data as &$value) {
			if (isset($value['auto_use'])) {
				foreach ($this->config_auto_use as $value1) {
					if (isset($value1['id']) && isset($value1['name']) && intval($value['auto_use']) == intval($value1['id'])) {
						$value['_auto_use'] = $value1['name'];
						break;
					}
				}
			};
			
			if (isset($value['count'])) {
				$value['_count'] = intval($value['count']);
			};
			
			
			if (isset($value['game_id'])) {
				$value['gname'] = isset($this->config->item('gameList')[$value['game_id']]) ? $this->config->item('gameList')[$value['game_id']] : '未知游戏';
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