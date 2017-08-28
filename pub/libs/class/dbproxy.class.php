<?php
/**
 * 数据库存储过程代理执行类
 * @author Shiny.Jiang
 *
 */
class dbproxy {
	static $obj = null;
	/**
	 * 关闭构造函数
	 */
	private function __construct() {}
	
	/**
	 * 关闭对象克隆
	 */
	private function __clone(){}
	
	/**
	 * 单例出示化对象
	 * @return object
	 */
	public static function init() {
		if (empty(self::$obj)) {
			self::$obj = new dbproxy();
		}
		return self::$obj;
	}
	
	/**
	 * 执行存储过程
	 * @param string $dbconfname 数据库配置名称
	 * @param unknown $proc	存储过程名称
	 * @param unknown $param 参数
	 * @return Ambigous <multitype:, multitype:string >
	 */
	public function run($dbconf, $proc, $param) {
		$db = new mysql($dbconf);
		$data = $db->execute($proc, $param);
		return $data;
	}
}

?>