<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-7-28
 */

class PlayerLock extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		echo $this->views->setRow(
				array(
					array(
							'name'=>'uname',
							'desc'=>'关键字',
					),
					array(
							'type'=>'button',
							'col'=>1,
							'onclick'=>array(
									'type'=>'reload',
							),
					)
				)
		)->setRow(
				array(
						array(
								'type'=>'table',
								'thList'=>array('guid', '昵称', '渠道号', '最后更新时间'),
								'colList'=>array('guid', 'nickname', 'client_id','last_update_time'),
						),
				)
				
		)->done();
	}
	
	public function ajaxTable(){
		$uname = get_extra('uname');
		
		if ($uname == '') {
			echo json_encode(array('data'=>array()), true);
			exit;
		}

		// 从数据库查询玩家信息
        $data = $this->dbapp->manage_sp_report_users($uname);

        $this->load->library('app/client', array($_SESSION['authId']));

        if ($this->client->getLevel() === 0) {
            echo json_encode(array('data'=>$data, true));
            exit;
        }

        if($this->client->checkUser($data[0]['client_id'])){
            echo json_encode(array('data'=>$data, true));
        }else{
            echo json_encode(array('data'=>array(), true));
        }


	}
}