<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function handleData($data, $code){
	if (isset($_REQUEST[$code])) {
		echo $data;
	}
}

/**
 * 获取初始化时选中的游戏
 * @param unknown $gameList
 * @return multitype:unknown
 */
function getDefGames($gameList){
	$defGame = array();
	foreach ($gameList as $k=>$v) {
		if (isset($v['ischeck'], $v['id']) && $v['ischeck'] == 'checked') {
			$defGame[] = $v['id'];
		}
	}
	return $defGame;
}

function getColPostDate(){
	return array(array('data'=>'post_date', 'title'=>'日期', 'defaultContent'=>'0000-00-00'));
}

function getGname($gameList, $gid){
	$gname = '未知游戏';
	foreach ($gameList as $value) {
		if (isset($value['id'], $value['name']) && $value['id'] == $gid) {
			$gname =  $value['name'];
		}
		continue;
	}
	return $gname;
}

/**
 * 输入异常信息退出脚本执行
 * @param unknown $status
 * @return string
 */
function error_report($status, $extMsg = ''){
	$error_conf = $GLOBALS['CFG']->config['error'];
	$report = isset($error_conf[$status]) ? json_encode(array('status'=>$status, 'msg'=>$error_conf[$status], 'ext'=>$extMsg), JSON_UNESCAPED_UNICODE) : json_encode(array('statue'=>10000, 'mge'=>'未知的错误类型'), JSON_UNESCAPED_UNICODE);
	exit($report);
}

/**
 * 获取表格更换数据源数据
 * @param unknown $name
 * @return Ambigous <string, mixed>
 */
function get_extra_data($name){
	if (isset($_REQUEST['extra_search'][0]['reqData'])) {
		$json = $_REQUEST['extra_search'][0]['reqData'];
	}elseif(isset($_REQUEST['extra_search'][0]['req'])){
		$json = $_REQUEST['extra_search'][0]['req'];
	}else{
		error_report(1, $name);
	}

	$arr = $json;//json_decode($json, true);
	return isset($arr[$name]) ? $arr[$name] : error_report(1, $name);
}

function get_extra_v2($name){
	if (isset($_REQUEST['extra_search'][0]['req'])) {
		foreach ($_REQUEST['extra_search'][0]['req'] as $k=>$v) {
			if ($k == $name) {
				return $v;
			};
			continue;
		};
	}
	return null;
}

function getUid($chkid, $id, $app, $type) {
		switch ($chkid) {
			case 0:
				$uid = $id;
				break;
			case 1:
			case 2:
			case 3:
				$res = $app->getUserInfo($id, 'uid', $type[$chkid]['pname']);
				if($res['result'] == 0) {
					$uid = $res['data']['uid'];
				} else {
					return false;
				}
				break;
			default:
				return false;
		}
	
		return $uid;
	}
/**
 * 退出执行并返回json格式数据
 * @param string $msg
 * @param unknown $status
 */
function execExit($msg = '', $status = -1){
	exit(json_encode(array('status'=>$status, 'data'=>$msg), JSON_UNESCAPED_UNICODE));
}

/**
 * 获取图表获其他普通ajax传递的数据
 * @param unknown $name
 * @return Ambigous <string, mixed>
 */
function get_comm_req($name){
	$arr = isset($_REQUEST['reqData']) ? $_REQUEST['reqData'] : execExit($name);
	return isset($arr[$name]) ? $arr[$name] : execExit($name);
}

/**
 * 获取参数值
 * @param unknown $key
 * @param string $errMsg
 * @return Ambigous <NULL, unknown>
 */
function reqData($key, $errMsg = ''){
	$val = isset($_REQUEST['reqData'][$key]) ? $_REQUEST['reqData'][$key] : null;

	if (!isset($val) && $errMsg != '') {
		_exit($errMsg);
	}

	return $val;
}

/**
 * 跳转到某个url并弹出提示语句
 * @param string $msg
 * @param string $url
 * @param string $tagert
 */
function jump_url($msg = '', $url = '', $tagert = 'document'){
	@header("content-type:text/html;charset=utf-8");
	// 默认跳转域名首页
	$url = $url === '' ? 'http://'.$_SERVER['HTTP_HOST'] : $url;

	if (is_array($url)) {
		$url = $url[0]['url'];
	}
	$html = "";
	if ($msg) {
		$html .= "alert('$msg');";
	}
	if ($url) {
		$html .= $tagert.".location.href='$url';";
	} else {
		$html .= "history.go(-1);";
	}
	$html = "<script>$html</script>";
	echo $html;
	exit();
}


function get_extra($name){
	if (isset($_REQUEST['extra_search'])) {
		foreach ($_REQUEST['extra_search'][0] as $k=>$v) {
			if (isset($v[$name])) {
				return $v[$name];
			};
			continue;
		};
	}
	return NULL;
}

function get_extra_v1($name){
	if (isset($_REQUEST['extra_search'])) {
		foreach ($_REQUEST['extra_search'] as $k=>$v) {
			if (isset($v[$name])) {
				return $v[$name];
			};
			continue;
		};
	}
	return NULL;
}

function convGname(&$data, $gameList, $colName = 'gid'){
	foreach ($data as &$v) {
		foreach ($gameList as $key=>$value) {
			if ($v[$colName] == $key) {
				$v['gname'] = $value;
				break;
			}
			continue;
		}
	}
}

function convPname(&$data, $pidList, $colName = 'pid'){
	foreach ($data as &$v) {
		foreach ($pidList as $value) {
			if ($value['id'] == $v[$colName]) {
				$v['pname'] = isset($value['name']) ? $value['name'] : $value['id'];
				break;
			}
		}
	}
}

/**
 * 配合前端渲染
 * @param unknown $data
 * @param string $index
 * @return boolean
 */
function convDT(&$data, $index = 'id'){
	foreach ($data as &$value) {
		if (!isset($value[$index])) {
			return false;
		}
		$value['DT_RowId'] = $value[$index];
		unset($value[$index]);
	}
}