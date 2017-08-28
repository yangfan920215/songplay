<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/general/controllers.html
 */
class CI_Controller {
	
	/**
	 * 渲染数据
	 * @var unknown
	 */
	public $data  = array();

	/**
	 * Reference to the CI singleton
	 *
	 * @var	object
	 */
	private static $instance;

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		self::$instance =& $this;

		// Assign all the class objects that were instantiated by the
		// bootstrap file (CodeIgniter.php) to local class variables
		// so that CI can run as one big super object.
		foreach (is_loaded() as $var => $class)
		{
			$this->$var =& load_class($class);
		}

		$this->load =& load_class('Loader', 'core');
		$this->load->initialize();
		log_message('info', 'Controller Class Initialized');
		
		//* 拓展部分 *//
		$this->data['tab_id'] = $this->user->checkUser();
		
		// 权限检查,记载渲染类
		$this->load->library('views', array($this->urlext->getFile(), $this->urlext->getContro(), $this->urlext->getMethod(), $this->data['tab_id']));
		
		// 以前静态页面的渲染
		// 获取公共URL
		$this->data['sroot'] = base_url();	// 公共文件URL
		$this->data['ssroot'] = base_url().'index.php/';	// 公共文件URL(新)
		$this->data['jroot'] = base_url().'Application/views/public/js/';	// js公共文件URL
		$this->data['croot'] = base_url().'Application/views/public/css/';	// css公共文件URL
		
		
		// 默认日期设置
		$this->data['sDate'] = $this->config->item('searchDate')['sDate'];;
		$this->data['eDate'] = $this->config->item('searchDate')['eDate'];
		
		// 游戏列表
		$this->data['gameList'] = $this->config->item('gameListv1');
		
		// 除非公共目录,否则一律检查权限,权限检测失败跳转至首页
		
		// 获取控制器名称和页面名称
		$ciName = $this->uri->rsegment(1);
		
		$htName =  $this->uri->rsegment(2);
		
		
		$this->data['ciName'] = $ciName;
		$this->data['htName'] = $htName;
		$this->data['tab'] = 'tab_' . $this->data['tab_id'];
		$this->data['search_row_id'] = 'search_row' . $this->data['tab'];
		$this->data['divId'] = $this->data['tab'].'_div';
		$this->data['tableId'] = $this->data['tab'].'_table';
		$this->data['gameid'] = $ciName.'_gameid';
		$this->data['comData'] = $this->data['ssroot'].$ciName.'/comData';
		$this->data['clientId'] = $this->data['tab'].'_client';
		$this->data['ptId'] = $this->data['tab'].'_pt';
		$this->data['sDateId'] = $this->data['tab'].'_sDate';
		$this->data['eDateId'] = $this->data['tab'].'_eDate';
		$this->data['reloadButton'] = $this->data['tab'].'_reloadButton';
		
		// 渲染类
		$this->data['ajaxData'] = $this->data['ssroot'].$this->urlext->getFile().'/'.$ciName.'/ajaxData';
		$this->data['ajaxTable'] = $this->data['ssroot'].$this->urlext->getFile().'/'.$ciName.'/ajaxTable';
		$this->data['ajaxMorris'] = $this->data['ssroot'].$this->urlext->getFile().'/'.$ciName.'/ajaxMorris';
		$this->data['tab_fun'] = $this->data['ssroot'].$this->urlext->getFile().'/'.$ciName.'/divfun';
		// 更新数据函数名
		$this->data['ajaxReload'] = $this->data['tab'].'_'.'ajaxReload()';
		$this->data['addModel'] = $this->data['ssroot'].$this->urlext->getFile().'/'.$ciName.'/addModel';
		// 默认选中哪些游戏
		$this->data['checkGame'] = $this->config->item('checkGame');
		$this->data['add_model_id'] = 'add_model_id_' . $this->data['tab'];
		$this->data['edit_model_id'] = 'edit_model_id' . $this->data['tab'];
		
		// 节点id,用于在标识页面的唯一性
		//$this->data['tab_id'] = $this->user->checkUser($ciName, $htName);
		switch (count($this->uri->segments)) {
			case 0:
				$file = '';
				$controller = 'Main';
				$method = 'index';
				break;
			case 2:
				$file = '';
				$controller = $this->uri->segments[1];
				$method = $this->uri->segments[2];
				break;
			case 3:
				$file = $this->uri->segments[1];
				$controller = $this->uri->segments[2];
				$method = $this->uri->segments[3];
				break;
			case 4:
				$file = $this->uri->segments[1] . '/' . $this->uri->segments[2];
				$controller = $this->uri->segments[3];
				$method = $this->uri->segments[4];
				break;
			default:
				exit(json_encode(array('status'=>1, 'data'=>'不合法的url')));
				break;
		}
		
		// 兼容性
		$this->data['tab'] = 'tab_' . $this->data['tab_id'];
		$this->data['search_tab'] = 'search_tab_' . $this->data['tab_id'];
		
		$this->data['ajaxTable'] = $this->data['ssroot'] . $file . '/' . $controller . '/ajaxTable';
		$this->load->library('html', array('method'=>$this->uri->rsegment(1), 'action'=>$this->uri->rsegment(2), 'tab'=>$this->data['tab']));
		$this->load->library('nhtml', array('method'=>$this->uri->rsegment(1), 'action'=>$this->uri->rsegment(2), 'tab'=>$this->data['tab']));
		require $this->config->item('sys_libs_dir') . 'inter.class.php';
		require $this->config->item('sys_libs_dir') . 'redisconfig.class.php';
		
		// $this->load->library('views', array($this->data['tab_id'], $ciName, $htName));
		if ($ciName == 'userManage' || $ciName == 'roleManage') {
			$this->load->library('view', array($this->data['tab_id'], $ciName, $htName));
		}

		// 模版赋值
		$this->views->setTab($this->data, $this->data['tab_id']);

		// 语言
        $this->config->set_item('language', 'zh_tw');
        $this->load->language(array('public'));
	}

	// --------------------------------------------------------------------

	/**
	 * Get the CI singleton
	 *
	 * @static
	 * @return	object
	 */
	public static function &get_instance()
	{
		return self::$instance;
	}
	
}