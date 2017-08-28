<?php
/**
 * @author yangf@songplay.cn
 * @date 2016-5-10
 */

class Receive{
	private $CI;
	
	private $edit = 'tData'; 
	
	public function __construct(){
		$this->CI =& get_instance();

		$req = $_REQUEST;
	}
	
	public function edit($rowId = array(0), $field = array('id')){
		$data = array();
		
		$edit = $_REQUEST[$this->$edit];
		
		if (empty($rowId)) {
			foreach ($edit as $value) {
				foreach ($field as $fieldName) {
					$data[$fieldName][$value] = isset($_REQUEST[$this->$edit][$value]) ? $_REQUEST[$this->$edit][$value] : NULL;
				}
			}
		}
		
		foreach (w as $value) {
			foreach ($field as $fieldName) {
				$data[$fieldName][$value] = isset($_REQUEST[$this->$edit][$value]) ? $_REQUEST[$this->$edit][$value] : NULL;
			}
		}
	}
	
}