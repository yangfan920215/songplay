<?php
/**
 * 解析url
 * @author yangf@songplay.cn
 * @date 2016-5-18
 */
class UrlExt{
	private $CI;
	
	private $file;
	
	private $controller;
	
	private $method;
	
	public function __construct(){
		$this->CI =& get_instance();
		switch (count($this->CI->uri->segments)) {
			case 0:
				$this->file = '';
				$this->controller = 'Pub';
				$this->method = 'index';
				break;
			case 2:
				$this->file = '';
				$this->controller = $this->CI->uri->segments[1];
				$this->method = $this->CI->uri->segments[2];
				break;
			case 3:
				$this->file = $this->CI->uri->segments[1];
				$this->controller = $this->CI->uri->segments[2];
				$this->method = $this->CI->uri->segments[3];
				break;
			case 4:
				$this->file = $this->CI->uri->segments[1] . '/' . $this->CI->uri->segments[2];
				$this->controller = $this->CI->uri->segments[3];
				$this->method = $this->CI->uri->segments[4];
				break;
			default:
				execExit('urlext:不合法的url');
				break;
		}
	}
	
	public function getFile(){
		return $this->file;
	}
	
	public function getContro(){
		return $this->controller;
	}
	
	public function getMethod(){
		return $this->method;
	}
}