<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Task extends CI_Controller{
	public function __construct(){
		parent::__construct();
	
		$this->load->helper(array('func'));
		// 链接数据库
		$this->load->database();
	}
	
	public function index(){
		
		$this->parser->parse('task/index.html', $this->data);
	}
}