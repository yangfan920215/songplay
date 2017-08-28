<?php
/**
 * redis操作类
 * @author yangf@songplay.cn
 * @date 2016-8-15
 */

class Redisapp{
	private $redis = null;
	
	private $separator = '_';
	
	private $ident = null;

    protected $CI;

	/**
	 * 构造函数
	 * @param unknown $conf
	 */
	public function __construct($conf = null) {
        $this->CI =& get_instance();

		$host = isset($conf['host']) ? $conf['host'] : '127.0.0.1';
		$port = isset($conf['port']) ? $conf['port'] : 6379;
		$maxConnTime = isset($conf['maxConnTime']) ? $conf['maxConnTime'] : 5;
		$this->ident = isset($conf['ident']) ? $conf['ident'] : 'pub';
		
		// 生成redis访问对象
		$this->redis = new Redis();
		$this->redis->connect($host, $port, $maxConnTime) or die('redis初始化失败！');
	}

	public function del(){
	    $this->redis->del($this->key);
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
		$this->key = $this->ident . $this->separator . $key;

		if($this->redis) {
			// 签入,若不存在该key或key值不是一个整数,会返回false
			$val = $this->redis->incr($key);
			// 若key没有设置生存时间,进行设置
			if($this->redis->ttl($key) < 1) {
				$this->redis->expire($key, $timeout);
			}
		}
		// 返回处理结果
		return $val;
	}
	
	
	
}