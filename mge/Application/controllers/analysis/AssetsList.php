<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-2-25
 */

class AssetsList extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		
		$this->load->database('manage');
	}
	
	public function index(){
		$this->parser->parse('assetsList.html', $this->data);
	}
	
	public function ajaxTable(){
		$gid  = get_extra_v1('gameId');
		$reslet = $this->dbapp->mge_sp_get_gold_rank($gid);
		echo json_encode(array('data'=>$reslet));
	}
	
}