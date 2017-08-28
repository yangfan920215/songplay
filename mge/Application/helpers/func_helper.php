<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 加载公共函数
$pub_func_path = dirname($_SERVER['DOCUMENT_ROOT']) . '/pub/libs/func.php';
if (file_exists($pub_func_path)) {
	require_once $pub_func_path;
	unset($pub_func_path);
}

/**
 * 日志函数,例:txtLog('test', '测试数据', array('log_path'=>'/Application/logs'));
 * @param unknown $class 在日志目录下的分类目录
 * @param unknown $msg	日志信息
 * @param array $ext	数组,log_path:指定日志目录
 * @return boolean
 */
function txtLog($class, $msg, array $ext = array()){
	// 汇总数据
	$data = array();

	// 如果定义了日志目录,或日志目录已存在,使用之
	if (isset($ext['log_path']) && is_writable($_SERVER['DOCUMENT_ROOT'] . $ext['log_path'])) {
		$log_path = $_SERVER['DOCUMENT_ROOT'] . $ext['log_path'];
	}elseif(defined('LOG') && is_writable(LOG)){
		$log_path = LOG;
	}elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . '/data/log') && is_writable($_SERVER['DOCUMENT_ROOT'] . '/data/log')){
		$log_path = $_SERVER['DOCUMENT_ROOT'] . '/data/log';
	}else{
		echo 'txt log error, can\'t find file write in';
		return false;
	}
	
	if (!file_exists($log_path . '/' . $class)) {
		mkdir($log_path . '/' . $class);
	}
	
	// 日志目录
	$fileName = $log_path . '/' . $class . '/' . date('Y-m-d') . '.log';

	//　访问的uri
	$request_url = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];

	// POST数据
	if (!empty($_POST)) {
		$data['post'] = $_POST;
	}

	$php_input = file_get_contents("php://input");

	if (!empty($php_input)) {
		$data['post_input'] = $php_input;
	}

	// 获取日志文件句柄
	$fp = fopen($fileName, 'a+');

	// 判断创建或打开文件是否  创建或打开文件失败，请检查权限或者服务器忙;
	if($fp === false){
		echo 'txt log error, can\'t open the file';
		return false;
	}
	
	if(fwrite($fp,'[TIME:'. date("Y-m-d H:i:s").'] --- [msg:{' . $msg . '}] --- [url:{'.$request_url.'}] --- [data: {' . str_replace(' ', '', var_export($data, true)) . "}]\r\n")){
		fclose($fp);
		return true;
	}else{
		echo 'txt log error, can\'t write in file';
		return false;
	}
}

/**
 * 获取一个数组中的默认配置选项的key值(比如或者默认配置中的游戏)
 * @param array $list
 * @param string $key
 * @return unknown|boolean
 */
function getDefSelect(array $list, $key = 'id'){
	// 关键字
	$defKey = 'check';
	
	foreach ($list as $value) {
		if (isset($value[$key])) {
			// 兼容老配置
			if (isset($value[$defKey]) && $value[$defKey] === true) {
				return $value[$key];
			};
			
			// 即在数组中设置,array(array($defKey));
			if (in_array($defKey, $list)) {
				return $value[$key];
			}
		}
		
		return false;
	}
	
}