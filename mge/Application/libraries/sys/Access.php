<?php
/**
 * 数据访问解析类
 * @author yangf@songplay.cn
 * @date 2016-8-17
 */

class Access{
		
	private $data;
	
	public function set($type){
		
		switch ($type) {
			case 1:
				$this->data = isset($_REQUEST['reqData']) ? $_REQUEST['reqData'] : _exit('非法数据');
			break;
			case 2:
				$this->data = isset($_REQUEST) ? $_REQUEST : _exit('非法数据');
			break;
			case 3:
				$this->data = isset($_REQUEST['tData']) ? $_REQUEST['tData'] : _exit('非法数据');
			break;
			case 4:
				$this->data = isset($_REQUEST['extra_search'][0]['req']) ? $_REQUEST['extra_search'][0]['req'] : _exit('非法数据');
			break;
			default:
				_exit('Access class initialize faild!');
			break;
		}
	}
	
	public function tData(){
		return $this->data;
	}
	
	public function settData($rowId){
		$this->data =  $this->data[$rowId];
	}
	
	/**
	 * 获取一个整数类型的数据
	 * @param unknown $str
	 * @param unknown $paramDesc
	 * @param array $scope
	 * @return number
	 */
	public function int($str, $paramDesc, array $scope = array()){
		if (!isset($this->data[$str])) {
			_exit('没有接收到' . $paramDesc . '数据');
		}

		if (preg_match('/^-?[1-9]\d*$/', $this->data[$str]) !== 1) {
			_exit($paramDesc . '不合法!');
		}
		
		$int = intval(trim($this->data[$str]));
		
		if (isset($scope['min']) && intval($scope['min']) > $int) {
			$min = intval($scope['min']);
			_exit($paramDesc . '必须大于' . $min);
		}
		
		if (isset($scope['max'])) {
			switch ($scope['max']) {
				case 'int':
					$max = 2147483647;
				break;
				default:
					$max = intval($scope['max']);
				break;
			}
			if ($max < $int) {
				_exit($paramDesc . '必须小于' . $max);
			}
		}
		
		return $int; 
	}
	
	/**
	 * 获取一个普通字符类型的数据
	 * @param unknown $str
	 * @param unknown $paramDesc
	 * @param array $scope
	 * @return string
	 */
	public function str($str, $paramDesc, array $scope = array()){
		if (!isset($this->data[$str])) {
			_exit('没有接收到' . $paramDesc . '数据');
		}
		
		$str = trim($this->data[$str]);
		
		if (isset($scope['min']) && intval($scope['min']) > iconv_strlen($str, 'utf-8')) {
			$min = intval($scope['min']);
			_exit($paramDesc . '必须大于' . $min . '字符');
		}
		
		if (isset($scope['max']) && intval($scope['max']) < iconv_strlen($str, 'utf-8')) {
			$max = intval($scope['max']);
			_exit($paramDesc . '必须小于' . $max . '字符');
		}
		
		return $str;
	}
	
	/**
	 * 获取一个必须隶属于特定集合的数据
	 * @param unknown $str
	 * @param unknown $paramDesc
	 * @param array $scope
	 * @return string
	 */
	public function select($str, $paramDesc, array $scope){
		if (!isset($this->data[$str])) {
			_exit('没有接收到' . $paramDesc . '数据');
		}
		
		$str = trim($this->data[$str]);
		
		if (!isset($scope[$str])) {
			_exit($paramDesc . '不合法!');
		}
		
		return $str;
	}
	
	public function selectv1($str, $paramDesc, array $scope){
		if (!isset($this->data[$str])) {
			_exit('没有接收到' . $paramDesc . '数据');
		}
	
		$str = trim($this->data[$str]);
	
		foreach ($scope as $value) {
			if (isset($value['id']) && $value['id'] == $str) {
				return $value['id'];
			};
		}
	
		_exit($paramDesc . '不合法!');
	}
	
	
	public function special($str, $paramDesc, $type){
		$str = $this->exist($str, $paramDesc);
		
		switch ($type) {
			case 'roomId':
				$arr = explode('.', $str);
				
				if (count($arr) != 4) {
					_exit($paramDesc . '不是合法的格式');
				}
			break;
			break;
			default:
				_exit($paramDesc . '未知的特殊数据类型,配置错误');
			break;
		}
		
		return $str;
	}
	
	
	private function exist($str, $paramDesc){
		if (!isset($this->data[$str])) {
			_exit('没有接收到' . $paramDesc . '数据');
		}
		
		return trim($this->data[$str]);
	}
}