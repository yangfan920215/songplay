<?php
/**
 * @author yangf@songplay.cn
 * @date 2016-5-16
 */

class DataRece {
	
	protected $CI;
	
	private $edit = 'tData';
	
	public function __construct(){
		$this->CI =& get_instance();
	}
	
	/**
	 * 获取数据
	 * @param unknown $key
	 * @param unknown $isStrict	是否开启严格模式
	 * @return unknown|NULL
	 */
	public function post($key, $isStrict = true, $errorMsg = ''){
		// 参数存在,则返回
		if (isset($_POST[$key])) {
			return trim($_POST[$key]);
		}
		
		// 执行严格模式
		if ($isStrict) {
			execExit($errorMsg);
		}else{
			return null;
		}
	}
	
	public function edit(){
		$ids = array();
		
		foreach ($_REQUEST[$this->edit] as $value) {
			$ids[] = $value['DT_RowId'];
		}
		return $ids;
	}

	public function ids($val = 'id'){
        $ids = array();

        foreach ($_REQUEST[$this->edit] as $value) {
            $ids[] = $value[$val];
        }
        return $ids;
    }
}