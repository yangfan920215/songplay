<?php
/**
 * 公共函数库
 * @author yangf@songplay.cn
 * @date 2016-6-24
 */

/**
 * 监控访问信息并打印日志
 * @return boolean
 */
function monitor($ret = ''){
	defined(LOG_PATH) or define(LOG_PATH, dirname($_SERVER['DOCUMENT_ROOT']).'/pub/data/log/');
	
	// 汇总数据
	$data = array();

	// 如果定义了日志目录,或日志目录已存在,使用之
	if (is_writable(LOG_PATH)) {
		$log_path = LOG_PATH;
	}else{
		echo 'writeLog failed, can\'t find the logFile write in';
		return false;
	}
	// 日志目录
	$fileName = $log_path . '/' . date('Y-m-d') . '.log';

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
		echo 'function monitor error, can\'t open the file';
		return false;
	}

	// 记录访问url,参数和返回值
	if(fwrite($fp,'[TIME:'. date("Y-m-d H:i:s").'] --- [url:{'.$request_url.'}] --- [data:{' . str_replace(' ', '', var_export($data, true)) . "}] --- [ret:{" . var_export($ret, true) . "}]\r\n")){
		fclose($fp);
		return true;
	}else{
		echo 'function monitor error, can\'t write in file';
		return false;
	}
}

/**
 * 调试函数
 * @param mixd $data
 * @param string $is_ext
 */
function D($data, $is_ext = TRUE){
	echo '<pre>';
	print_r($data);
	if ($is_ext) {
		exit;
	}
}

/**
 * 退出并返回json格式数据
 * @param string $msg
 * @param unknown $status
 */
function _exit($msg = '', $status = -1){
	exit(json_encode(array('status'=>$status, 'data'=>$msg), JSON_UNESCAPED_UNICODE));
}


/**
 * 获取参数值
 * @param unknown $key
 * @param string $errMsg
 * @return Ambigous <NULL, unknown>
 */
function getReq($key, $errMsg = ''){
	$val = isset($_REQUEST[$key]) ? $_REQUEST[$key] : null;
	
	if (!isset($val) && $errMsg != '') {
		_exit($errMsg);
	}
	
	return $val;
}
