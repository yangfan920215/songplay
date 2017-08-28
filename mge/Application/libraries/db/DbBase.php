<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 数据库存储过程调用基类
 * @author yangf@songplay.cn
 * @date 2016-5-13
 */

class DbBase{
	/**
	 * 全局CI对象
	 * @var unknown
	 */
	protected $CI;
	
	/**
	 * 数据库异常信息
	 * @var unknown
	 */
	protected $errorMsg = null;
	
	/**
	 * 数据库链接对象
	 * @var unknown
	 */
	protected $db;
	
	/**
	 * 单一对象模式
	 */
	public function __construct($dbName){
		$this->CI =& get_instance();
		$this->CI->load->database($dbName);

		// 链接数据库
		$this->db = $this->CI->db;
	}
	
	/**
	 * 判断逻辑执行到此时与DB的交互是否存在异常
	 * @param string $isStrict
	 * @return boolean
	 */
	public function isError($isStrict = true){
		// 存储过程执行并未发现异常
		if ($this->errorMsg == '') {
			return false;
		}
		
		// 在严格模式下,一旦监测到存储过程错误,直接中断执行
		if ($isStrict) {
			execExit($this->errorMsg);
		}else{
			return $this->errorMsg;
		}
	}
	
	/**
	 * 获取错误信息
	 * @return string
	 */
	public function getErrorMsg(){
		return $this->errorMsg;
	}
	
	/**
	 * 修改存储过程所在类
	 * @param unknown $key
	 * @return Sp
	 */
	private function changDb($dbName){
		$this->db = $this->CI->load->database($dbName, TRUE);
	}
	
	/**
	 * 执行存储过程
	 * @param unknown $spName
	 * @param unknown $param
	 * @param string $dbName
	 * @return unknown
	 */
	protected function execSp($spName, $param = array(), $dbName = 'mge'){
		// 链接数据库
		$dbName == 'mge' ? $this->db = $this->CI->load->database('mge', true) : $this->changDb($_SESSION['dbconfig'][$dbName]);
	
		// 拼凑存储过程sql
		if ($param == array()) {
			$sql = 'CALL '.$spName.'();';
		}else{
			$sql_p = is_array($param) ? implode('","', $param) : $param;
			$sql = 'CALL ' . $spName . '("' . $sql_p . '")';
		}
		// 执行存储过程,并获取返回数据
		$result = $this->db->query($sql)->result_array();
		// 重新链接,不然再次查询会报错
		$this->db->reconnect();
		
		// 若属于insert或update类型的存储过程,按照约定解析
		if (isset($result[0]['status']) && isset($result[0]['msg']) && count($result) == 1) {
			return $result[0];
		}
	
		return $result;
	}
	
	/**
	 * 返回需要的信息
	 * @param unknown $ret
	 * @return unknown|boolean
	 */
	protected function getMsg($ret){
		if (isset($ret['status']) && isset($ret['msg']) && $ret['status'] == 0) {
			return $ret['msg'];
		}
		return false;
	}
	
	/**
	 * 检查select结果集是否出现错误
	 * @param unknown $ret
	 * @return boolean|unknown
	 */
	protected function checkRet($ret){
		if (isset($ret['status']) && $ret['status'] != 0 && isset($ret['msg'])) {
			$this->errorMsg = $ret['msg'];
			return false;
		}
		return $ret;
	}
	
}