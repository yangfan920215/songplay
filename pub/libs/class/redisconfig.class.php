<?php
/**
 * 获取appurl列表类
 * @author Shiny.Jiang
 *
 */
class redisconfig {
	static $obj = null;
	
	private $host = null;
	
	private $port = null;
	
	private $_redis = null;
	
	/**
	 * 关闭构造函数
	 */
	private function __construct($conf) {
		$this->host = isset($conf['host']) ? $conf['host'] : '127.0.0.1';
		$this->port = isset($conf['port']) ? $conf['port'] : 6379;
	}
	
	/**
	 * 关闭对象克隆
	 */
	private function __clone(){}
	
	/**
	 * 单例出示化对象
	 * @return object
	 */
	public static function init($conf) {
		return isset(self::$obj) ? self::$obj : new redisconfig($conf);
	}
	
	/**
	 * 获取redis对象
	 */
	private function _getRedisObject() {
		if(!isset($this->_redis)) {
			$this->_redis = new Redis();
			$this->_redis->connect($this->host, $this->port, 5) or die('redis初始化失败！');
		}
		return $this->_redis;
	}
	
	/**
	 * 获取game的app的url
	 * @return array
	 */
	public function getConfig($key) {
		if($this->_getRedisObject()) {
			if($this->_redis->exists($key)) {
				// 获取的数据转化为数组
				$data = json_decode($this->_redis->get($key), true);
			} else {
				// 从数据库直接获取最新数据
				$data = $this->_getDbConfig($key);
				if(is_array($data) && count($data)) {
					$this->_redis->setnx($key, json_encode($data));	
				}
			}
		} else {
			$data = $this->_getDbUrlList();
		}
		return $data;
	}
	
	/**
	 * 删除xcache中app地址
	 * @return bool
	 */
	public function delAppUrlFromXcache($key) {
		$back = false;
		if($this->_getRedisObject()) {
			if($this->_redis->exists($key)) {
				$this->_redis->del($key);
				$back = true;
			}
			
		}
		return $back;	
	}
	
	/**
	 * 数据库查出列表
	 * @return array
	 */
	private function _getDbConfig($key) {
		$data = array();

		switch ($key) {
			case 'songplay:appurl':
				$res = inter::init()->execSp('config', 'sp_gas_notify_s_simple', array('i_type'=>array(-1, 1)));
				if(is_array($res)) {
					foreach($res as $k=>$v) {
						$data[$v['type_id']][$v['game_id']][$v['uri']] = $this->_parseXml2url($v['xml']);
					}
				}
			break;
			default:
				_exit('can\'t find config');
			break;
		}

		return $data;
	}
	
	/**
	 * 解析appurl的xml
	 * @param string $xmlstr
	 * @return array
	 */
	private function _parseXml2url($xmlstr) {	
		$arr = xml::gamexml2array(trim($xmlstr));
		$return = array();
		foreach($arr['child'] as $_arr){
			if($_arr['tag'] == 'NotifyServer'){
				$return[] = $_arr['child'][0]['value'].':'.$_arr['child'][1]['value'];
			}
		}
		return $return;
	}
}

