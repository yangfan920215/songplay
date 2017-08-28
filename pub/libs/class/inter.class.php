<?php
/**
 * @author yangf@songplay.cn
 * @date 2016-7-12
 */
class inter {
	
	// 数据库配置
	private $dbconf = array(
		'manage'=>array('host'=>'10.66.115.103', 'port'=>3306, 'uid'=>'root', 'pwd'=>'9lMtjzOvGJ', 'db'=>'manage', 'type'=>'mysqli'),
		'mge'=>array('host'=>'10.66.115.103', 'port'=>3306, 'uid'=>'root', 'pwd'=>'9lMtjzOvGJ', 'db'=>'mge', 'type'=>'mysqli'),
		'config'=>array('host'=>'10.66.115.103', 'port'=>3306, 'uid'=>'root', 'pwd'=>'9lMtjzOvGJ', 'db'=>'config', 'type'=>'mysqli'),
	);
	
	// 默认读取的redis配置
	private $redisdef = 0;
	
	// 内存数据库REDIS
	private $redisconfig = array(
		'0'=>array('host'=>'127.0.0.1', 'port'=>6379),
	);
	
	private function getClassPath($className){
		return dirname($_SERVER['DOCUMENT_ROOT']) . '/pub/libs/class/' . $className . '.class.php';
	}
	
	private function __construct(){}
	
	/**
	 * 单例模式
	 * @return inter
	 */
	public static function init(){
		return isset(self::$obj) ? self::$obj : new inter();
	}
	
	/**
	 * 执行存储过程
	 * inter::init()->execSp('mge', 'sp_table_s', array(array('mge', 1), array('config_conn', 1)));
	 * @param unknown $dbName
	 * @param unknown $spName
	 * @param array $spParams
	 * @return Ambigous <Ambigous, mixed, boolean, multitype:, multitype:NULL >
	 */
	public function execSp($dbName, $spName, array $spParams){
		// 得到数据库配置
		$dbconfname = isset($this->dbconf[$dbName]) ? $this->dbconf[$dbName] : _exit('dbConfig can\'t be find');

		require_once $this->getClassPath('dbproxy');
		require_once $this->getClassPath('mysql');
		require_once $this->getClassPath('xml');
		
		$result = dbproxy::init()->run($dbconfname ,$spName ,$spParams);
		
		if ($result === false) {
			_exit('sp execution failed, please check log');
		}
		
		return $result;
	}
	
	/**
	 * 读取redis配置
	 * @param unknown $key
	 * @return multitype:
	 */
	public function readRedisConfig($key){
		$data = redisconfig::init($this->redisconfig[$this->redisdef])->getConfig($key);
		return $data;
	}
}