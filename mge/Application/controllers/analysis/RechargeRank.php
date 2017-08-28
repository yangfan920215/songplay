<?php
/**
 * @author yangf@songplay.cn
 * @date 2016-1-6
 */
class RechargeRank extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		// 链接数据库
		$this->load->database('log_comm');
	}
	
	public function index(){
		$thList = array('排名', '充值金额(元)', '充值次数', '单次充值最大金额', '用户uid', '渠道号', '注册时间', '最后登录时间', '游戏时长/分');
		$colList = array('ranks', 'total_amount', 'total_count', 'max_amount', 'uid', 'client_id', 'register_time', 'last_logon_time', 'playtime');
		
		$this->html->setSelectGid()->setInputTime($this->config->item('dateInterval')['eDate'])->setInputTime($this->config->item('dateInterval')['sDate'])->setButton();
		$this->html->setTable($thList, $colList)->done();
	}
	
	public function ajaxDataTable(){
		$gid = get_extra_data('gList');
		$sDate = get_extra_data('dtp_0');
		$eDate = get_extra_data('dtp_1');
		$data = $this->data($gid, $sDate, $eDate);
		echo json_encode(array('data'=>$data));
	}
	
	
	private function data($gid, $sDate, $eDate){
		// 获取用户uid,充值金额，充值次数，红包，集分宝
		$data = $this->dbapp->manage_sp_charge_rank_sn($gid, $sDate, $eDate);
		
		$uids = array();
		foreach ($data as $key => $val) {
			// 取出用户uid
			$uids[$key] = $val['uid'];
		}
		// 将用户uids转换成字符串。
		$uids = implode(',', $uids);
		
		// 如果查找不到任何用户充值记录
		if (empty($uids)) {
			echo json_encode(array('data'=>array()));
			exit;
		}
		
		
		//获取用户注册时间
		$registerTime = $this->dbapp->manage_sp_web_get_userinfo_by_uids($uids);
		
		$registerT = array();
		if(isset($registerTime)) {
			foreach ($registerTime as $key => $val) {
				$registerT[$val['uid']] = $val;
			}
		}
		
		$playtime = $this->dbapp->manage_sp_web_get_playtime_by_uids($uids);
		
		$playT = array();
		if(isset($playtime)) {
			foreach ($playtime as $key => $val) {
				$playT[$val['uid']] = $val;
			}
		}
		foreach ($data as $key => $val) {
			$data[$key]['client_id'] =  isset($registerT[$val['uid']]) ? $registerT[$val['uid']]['client_id'] : 0;
			$data[$key]['register_time'] =  isset($registerT[$val['uid']]) ? $registerT[$val['uid']]['register_time'] : '';
			$data[$key]['last_logon_time'] =  isset($registerT[$val['uid']]) ? $registerT[$val['uid']]['last_logon_time'] : '';
			$data[$key]['playtime'] =  isset($playT[$val['uid']]) ? $playT[$val['uid']]['playtime'] / 60 : 0;
			$data[$key]['ranks'] =  $key+1;
		}
		
		// 过滤掉渠道号为0为数据
		foreach ($data as $k=>$v) {
			if ($v['client_id'] == 0) {
				unset($data[$k]);
			};
		}
		
		// 数组序列中间出现断裂,重置key,否则生成的json字符串前端无法识别
		return array_values($data);
	}
}