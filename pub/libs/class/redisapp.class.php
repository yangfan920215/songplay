<?php
/**
 * redis操作类
 * @author yangf@songplay.cn
 * @date 2016-8-15
 */

class redisapp{
	private static $redisapp = null;
	
	private $redis = null;
	
	private $separator = '_';
	
	private $ident = null;
	
	/**
	 * 单例模式，防止对象被克隆
	 */
	private function __clone() {}
	
	/**
	 * 构造函数
	 * @param unknown $conf
	 */
	private function __construct($conf) {
		$host = isset($conf['host']) ? $conf['host'] : '127.0.0.1';
		$port = isset($conf['port']) ? $conf['port'] : 6379;
		$maxConnTime = isset($conf['maxConnTime']) ? $conf['maxConnTime'] : 5;
		$this->ident = isset($conf['ident']) ? $conf['ident'] : 'pub';
		
		// 生成redis访问对象
		$this->redis = new Redis();
		$this->redis->connect($host, $port, $maxConnTime) or die('redis初始化失败！');
	}
	
	/**
	 * 单例出示化对象
	 * @return object
	 */
	public static function init($conf) {
		return isset(self::$redisapp) ? self::$redisapp : new redisapp($conf);
	}
	
	/**
	 * redis签名处理(不关心value值的情况,只是使用一个key占据位置)
	 * @param unknown $key
	 * @param unknown $timeout	默认生效期为一年
	 * @return boolean
	 */
	public function sign($key, $timeout = 31536000){
		// 严格模式
		$val = false;
		
		// 生成key
		$key = $this->ident . $this->separator . $key;
		
		if($this->_redis) {
			// 签入,若不存在该key或key值不是一个整数,会返回false
			$val = $this->_redis->incr($key);
			// 若key没有设置生存时间,进行设置
			if($this->_redis->ttl($key) < 1) {
				$this->_redis->expire($key, $timeout);
			}
		}
		// 返回处理结果		
		return $val;
	}
	
	
	
}