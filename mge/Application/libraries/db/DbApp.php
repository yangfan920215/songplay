<?php
/**
 * @author yangf@songplay.cn
 * @date 2016-5-16
 */

require_once './Application/libraries/db/DbBase.php';

class DbApp extends DbBase{
	
	private $dbName = 'manage';

	public function __construct(){
		parent::__construct($this->dbName);
	}
	
	
	/**
	 * 根据比赛场id获取该比赛场下的房间id
	 * @param unknown $cascade
	 * @return unknown
	 */
	public function config_sp_node_id_s($cascade){
		$spName = 'sp_node_id_s';
		$dbName = 'config';
		$param = array(
				$cascade,
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function config_sp_type_game_s(){
		$spName = 'sp_type_game_s';
		$dbName = 'config';
		$param = array(
				-1, '',
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function config_sp_type_pay_s_simple(){
		$spName = 'sp_type_pay_s_simple';
		$dbName = 'config';
		$param = array(
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function config_sp_room_s_all(){
		$spName = 'sp_room_s_all';
		$dbName = 'config';
		$param = array(
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function config_sp_type_pay_s(){
		$spName = 'sp_type_pay_s';
		$dbName = 'config';
		$param = array(
			''
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function config_sp_prop_s_exists($pid){
		$spName = 'sp_prop_s_exists';
		$dbName = 'config';
		$param = array(
			$pid
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function config_sp_area_s_detail($area_code){
		$spName = 'sp_area_s_detail';
		$dbName = 'config';
		$param = array(
				$area_code
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_pay_track_s($uid, $gid,$orderId,$pseudo,$status,$sDate,$eDate,$i_ext,$_uid){
		$spName = 'sp_pay_track_s';
		$dbName = 'manage';
		$param = array(
			$uid, $gid,$orderId,$pseudo,$status,$sDate,$eDate,$i_ext,$_uid
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function config_sp_node_room_s_content($pam1){
		$spName = 'sp_node_room_s_content';
		$dbName = 'config';
		$param = array(
			$pam1
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_gifts_s_exists($id){
		$spName = 'sp_gifts_s_exists';
		$dbName = 'config';
		$param = array(
			$id
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_get_ac_winlist($acid, $sDate, $eDate){
		$spName = 'sp_get_ac_winlist';
		$param = array(
			$acid, $sDate, $eDate
		);
	
		return $this->execSp($spName, $param);
	}
	
	public function manage_sp_winlist_new_i($acid, $uid, $cid_befter, $cid){
		$spName = 'sp_winlist_new_i';
		$dbName = 'manage';
		$priceInfo = $cid_befter . ':' . $cid;
		$param = array(
				'i_activity_id '=>$acid,
				'i_uid '=>$uid,
				'priceInfo '=>$priceInfo,
				'i_prize_id '=>0,
				'i_amount'=>0,
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function config_sp_gifts_i($id, $name, $photo, $sort_id, $show_id, $gid, $attribute, $auto_use, $summary, $description, $op_name, $count){
		$spName = 'sp_gifts_i';
		$dbName = 'config';
		$param = array(
				$id, $name, $photo, $sort_id, $show_id, $gid, $attribute, $auto_use, $summary, $description, $op_name, $count
		);
		
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function config_sp_gifts_ext_d_all($id){
		$spName = 'sp_gifts_ext_d_all';
		$dbName = 'config';
		$param = array(
				$id
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function config_sp_gifts_u($id, $name, $photo, $sort_id, $show_id, $gid, $attribute, $auto_use, $summary, $description, $op_name, $count){
		$spName = 'sp_gifts_u';
		$dbName = 'config';
		$param = array(
				$id, $name, $photo, $sort_id, $show_id, $gid, $attribute, $auto_use, $summary, $description, $op_name, $count
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	

	public function config_sp_gifts_ext_s($id){
		$spName = 'sp_gifts_ext_s';
		$dbName = 'config';
		$param = array(
				$id
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function config_sp_gifts_ext_i($i_gift_id, $i_prop_id, $i_quantity1, $i_quantity2, $i_probability, $i_limit_count, $broadcast, $i_op_name){
		$spName = 'sp_gifts_ext_i';
		$dbName = 'config';
		$param = array(
			$i_gift_id, $i_prop_id, $i_quantity1, $i_quantity2, $i_probability, $i_limit_count, $broadcast, $i_op_name
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_promoter($gid, $sDate, $eDate){
		$spName = 'sp_report_promoter';
		$dbName = 'manage';
		$param = array(
			$gid, $sDate, $eDate
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function config_sp_gifts_dn($giftId){
		$spName = 'sp_gifts_dn';
		$dbName = 'config';
		$param = array(
			$giftId
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_versus_vcr($date, $stime, $etime, $uid, $gid){
		$spName = 'sp_report_versus_vcr';
		$dbName = 'manage';
		$param = array(
			$date, $stime, $etime, $uid, $gid
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	

	public function manage_sp_report_users($uname){
		$spName = 'sp_report_users';
		$dbName = 'manage';
		$param = array(
			$uname
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_hour_client($clientid, $date){
		$spName = 'sp_report_hour_client';
		$dbName = 'manage';
		$param = array(
				$clientid, $date
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_usersBag($gid, $pid, $ext){
		$spName = 'sp_report_usersBag';
		$dbName = 'manage';
		$param = array(
			$gid, $pid, $ext
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_wrplayers($gid, $pid, $ext){
		$spName = 'sp_report_wrplayers';
		$dbName = 'manage';
		$param = array(
				$gid, $pid, $ext
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	
	public function manage_sp_report_wr_versusbet($gid, $pid, $ext){
		$spName = 'sp_report_wr_versusbet';
		$dbName = 'manage';
		$param = array(
				$gid, $pid, $ext
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_wranalysis($gid, $pid, $ext){
		$spName = 'sp_report_wranalysis';
		$dbName = 'manage';
		$param = array(
				$gid, $pid, $ext
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_wr_versuslist($gid, $pid, $ext){
		$spName = 'sp_report_wr_versuslist';
		$dbName = 'manage';
		$param = array(
				$gid, $pid, $ext
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_email($uid, $gid){
		$spName = 'sp_report_email';
		$dbName = 'manage';
		$param = array(
			$uid, $gid
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_rank_data($gid, $sDate, $eDate){
		$spName = 'sp_report_rank_data';
		$dbName = 'manage';
		$param = array(
			$gid, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	
	public function manage_sp_report_promoterInfo($uid){
		$spName = 'sp_report_promoterInfo';
		$dbName = 'manage';
		$param = array(
			$uid
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	
	public function manage_sp_report_bind($uid){
		$spName = 'sp_report_bind';
		$dbName = 'manage';
		$param = array(
				$uid
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_luckyDraw($gid, $sDate, $eDate){
		$spName = 'sp_report_luckyDraw';
		$dbName = 'manage';
		$param = array(
			$gid, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_clientUser($agent_id, $sDate, $eDate){
		$spName = 'sp_report_clientUser';
		$dbName = 'manage';
		$param = array(
				$agent_id, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_clientUser_all($agent_id){
		$spName = 'sp_report_clientUser_all';
		$dbName = 'manage';
		$param = array(
				$agent_id
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_web_clientidExist($agent_id){
		$spName = 'sp_web_clientidExist';
		$dbName = 'manage';
		$param = array(
				$agent_id
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function sp_report_emailGive($iUid, $gid, $iStartDate, $iEndDate){
		$spName = 'sp_report_emailGive';
		$dbName = 'manage';
		$param = array(
			$iUid,  $gid, $iStartDate, $iEndDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_versus_s_extra($iUid,  $gid, $iStartDate, $iEndDate, $iExt, $_uid){
		$spName = 'sp_versus_s_extra';
		$dbName = 'manage';
		$param = array(
			$iUid,  $gid, $iStartDate, $iEndDate, $iExt, $_uid
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_sp_versus_s_by_roomid($roomid,  $gid, $iStartDate, $iEndDate, $iExt){
		$spName = 'sp_versus_s_by_roomid';
		$dbName = 'manage';
		$param = array(
				$roomid,  $gid, $iStartDate, $iEndDate, $iExt
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_gold_s($iUid, $gid, $iTypeId, $iStartDate, $iEndDate){
		$spName = 'sp_gold_s';
		$dbName = 'manage';
		$param = array(
			$iUid, $gid, $iTypeId, $iStartDate, $iEndDate, '1,10000'
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_gold_sn($iUid, $gid, $iTypeId, $i_cid, $iStartDate, $iEndDate){
		$spName = 'sp_gold_sn';
		$dbName = 'manage';
		$param = array(
				$iUid, $gid, $iTypeId, $i_cid, $iStartDate, $iEndDate, '1,10000'
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_user_lock_new_s($uid, $gid, $typeId, $iStartDate, $iEndDate){
		$spName = 'sp_user_lock_new_s';
		$dbName = 'manage';
		$param = array(
			$uid,$gid,$typeId,$iStartDate,$iEndDate,''
		);
		return $this->execSp($spName, $param, $dbName);
	}

	
	public function manage_sp_user_lock_iu($flag, $uid, $name, $ip, $gid, $summary, $unlock_time){
		$spName = 'sp_user_lock_new_iu';
		$dbName = 'manage';
		$param = array(
			$flag, $uid, $name, $ip, $gid, $summary, $unlock_time
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_type_config_s($dbName, $tableName, $colName, $pam = 1){
		$spName = 'sp_type_config_s';
		$dbName = 'manage';
		$param = array(
			$dbName, $tableName, $colName, $pam
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	/**
	 * 封号解号日志查询
	 * @param unknown $uid
	 * @param unknown $gid
	 * @param unknown $sDate
	 * @param unknown $eDate
	 * @return unknown
	 */
	public function manage_sp_user_lock_s($uid, $gid, $sDate, $eDate){
		$spName = 'sp_user_lock_s';
		$dbName = 'manage';
		$param = array(
			$uid, $gid, $sDate, $eDate, ''
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_logon_s($osId, $gameId, $clientId, $verId, $sDate, $eDate){
		$spName = 'sp_report_logon_s';
		$dbName = 'manage';
		$param = array(
			$osId, $gameId, $clientId, $verId, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_register_s($gameId, $clientId, $verId, $sDate, $eDate){
		$spName = 'sp_report_register_s';
		$dbName = 'manage';
		$param = array(
			$gameId, $clientId, $verId, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_conv_s($gameId, $clientId, $verId, $sDate, $eDate){
		$spName = 'sp_report_conv_s';
		$dbName = 'manage';
		$param = array(
				$gameId, $clientId, $verId, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}

	public function manage_sp_report_avgonl_s($gameId, $clientId, $verId, $sDate, $eDate){
		$spName = 'sp_report_avgonl_s';
		$dbName = 'manage';
		$param = array(
				$gameId, $clientId, $verId, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_maxonl_s($gameId, $sDate, $eDate){
		$spName = 'sp_report_maxonl_s';
		$dbName = 'manage';
		$param = array(
			$gameId, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_charge_s($osId, $gameId, $clientId, $verId, $sDate, $eDate){
		$spName = 'sp_report_charge_s';
		$dbName = 'manage';
		$param = array(
			$osId, $gameId, $clientId, $verId, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_charge_n($osId, $gameId, $clientId, $verId, $sDate, $eDate){
		$spName = 'sp_report_charge_n';
		$dbName = 'manage';
		$param = array(
				$osId, $gameId, $clientId, $verId, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_conv_n($gameId, $clientId, $verId, $sDate, $eDate){
		$spName = 'sp_report_conv_n';
		$dbName = 'manage';
		$param = array(
			$gameId, $clientId, $verId, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_avgonl_n($gameId, $clientId, $verId, $sDate, $eDate){
		$spName = 'sp_report_avgonl_n';
		$dbName = 'manage';
		$param = array(
				$gameId, $clientId, $verId, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_user_exchange_u($id){
		$spName = 'sp_user_exchange_u';
		$dbName = 'manage';
		$param = array(
				$id
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_user_exchange_s(){
		$spName = 'sp_user_exchange_s';
		$dbName = 'manage';
		$param = array(
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function config_sp_prop_s_all(){
		$spName = 'sp_prop_s_all';
		$dbName = 'config';
		$param = array(
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function config_sp_prop_s_game($i_platform, $i_game_id, $i_type){
		$spName = 'sp_prop_s_game';
		$dbName = 'config';
		$param = array(
			$i_platform, $i_game_id, $i_type
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function config_sp_prop_s($name, $gid, $ext){
		$spName = 'sp_prop_s';
		$dbName = 'config';
		$param = array(
				$name, $gid, $ext
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function config_sp_gifts_s($name, $gid, $ext){
		$spName = 'sp_gifts_s';
		$dbName = 'config';
		$param = array(
				$name, $gid, $ext
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function config_sp_goods_s_simple($gid){
		$spName = 'sp_goods_s_simple';
		$dbName = 'config';
		$param = array(
				$gid
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	/**
	 *
	 * @param unknown $gid
	 */
	public function manage_sp_web_check_uid($uid){
		$spName = 'sp_web_check_uid';
		$dbName = 'manage';
		$param = array(
				$uid
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	/**
	 *
	 * @param unknown $gid
	 */
	public function manage_sp_charge_rank_sn($gid, $sDate, $eDate){
		$spName = 'sp_charge_rank_sn';
		$dbName = 'manage';
		$param = array(
			$gid, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	
	public function manage_sp_web_get_userinfo_by_uids($uids){
		$spName = 'sp_web_get_userinfo_by_uids';
		$dbName = 'manage';
		$param = array(
			$uids
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_web_get_playtime_by_uids($uids){
		$spName = 'sp_web_get_playtime_by_uids';
		$dbName = 'manage';
		$param = array(
				$uids
		);
		return $this->execSp($spName, $param, $dbName);
	}
	/**
	 * 修改渠道公司信息
	 * @param unknown $client_id
	 * @param unknown $name
	 * @param unknown $remark
	 * @param unknown $email
	 * @param unknown $intoratio
	 */
	public function manage_sp_web_company_u($client_id, $name, $remark, $email, $intoratio){
		$spName = 'sp_web_company_u';
		$dbName = 'manage';
		$param = array(
				$client_id, $name, $remark, $email, $intoratio
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_charge_check_s($ptId, $gid, $sDate, $eDate){
		$spName = 'sp_charge_check_s';
		$dbName = 'manage';
		$param = array(
				$ptId, $gid, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_pay_track0($ptId, $gid, $sDate, $eDate){
		$spName = 'sp_report_pay_track0';
		$dbName = 'manage';
		$param = array(
				$ptId, $gid, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_goods_s_all(){
		$spName = 'sp_goods_s_all';
		$dbName = 'config';
		$param = array(
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	
	public function manage_sp_bag_s_use($uid,$sDate,$eDate,$props, $_uid){
		$spName = 'sp_bag_s_use';
		$dbName = 'manage';
		$param = array(
			$uid,$sDate,$eDate,$props,'',$_uid
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_type_bag_s(){
		$spName = 'sp_type_bag_s';
		$dbName = 'config';
		$param = array(
			''
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	
	public function manage_sp_charge_s($uid, $order_id, $sDate, $eDate, $goods_id, $_uid){
		$spName = 'sp_charge_s';
		$dbName = 'manage';
		$param = array(
			$uid, $order_id, $sDate, $eDate, '',$goods_id, $_uid
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function config_sp_play_s($gid){
		$spName = 'sp_play_s';
		$dbName = 'config';
		$param = array(
			$gid,''
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function mge_sp_get_gold_rank($gid){
		$spName = 'sp_get_gold_rank';
		$dbName = 'manage';
		$param = array(
				$gid, 1
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function mge_sp_room_id_s($pid, $sDate, $eDate){
		$spName = 'sp_room_id_s';
		$param = array(
		);
		return $this->execSp($spName, $param);
	}
	
	public function config_sp_room_id_s(){
		$spName = 'sp_room_id_s';
		$dbName = 'config';
		$param = array(
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function mge_sp_report_comeback_n($gid, $clientId, $sDate, $eDate){
		$spName = 'sp_report_comeback_n';
		$param = array(
				$gid, $clientId, $sDate, $eDate
		);
		return $this->execSp($spName, $param);
	}
	
	public function manage_sp_event_cal_comeback($gid, $clientId, $sDate, $eDate){
		$spName = 'sp_report_comeback_n';
		$dbName = 'manage';
		$param = array(
				$gid, $clientId, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_income_s_by_goods_id($gid, $sDate, $eDate){
		$spName = 'sp_report_income_s_by_goods_id';
		$dbName = 'manage';
		$param = array(
			-1, $gid, -1, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_event_cal_players($gid, $clientId, $sDate, $eDate){
		$spName = 'sp_report_players';
		$dbName = 'manage';
		$param = array(
				$gid, $clientId, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	/**
	 * 查询某渠道下(含)全部子渠道信息
	 * @param unknown $cid
	 */
	public function manage_sp_web_get_clientsub($uid){
		$spName = 'sp_web_get_clientsub';
		$dbName = 'manage';
		$param = array(
				$uid,
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	/**
	 * 获取比赛场
	 * @param unknown $gid
	 * @return unknown
	 */
	public function config_sp_node_gameids($gid){
		$spName = 'sp_node_gameids';
		$dbName = 'config';
		$param = array(
				$gid,
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function config_sp_prop_i($i_id, $i_name, $i_photo, $i_indate, $i_url_id, $i_show_id, $i_game_id, $i_attribute, $i_summary, $i_description, $i_op_name){
		$spName = 'sp_prop_i';
		$dbName = 'config';
		$param = array(
			$i_id, $i_name, $i_photo, $i_indate, $i_url_id, $i_show_id, $i_game_id, $i_attribute, $i_summary, $i_description, $i_op_name
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function config_sp_prop_u($i_id, $i_name, $i_photo, $i_indate, $i_url_id, $i_show_id, $i_game_id, $i_attribute, $i_summary, $i_description, $i_op_name, $jump, $jumpVal){
		$spName = 'sp_prop_u';
		$dbName = 'config';
		$param = array(
				$i_id, $i_name, $i_photo, $i_indate, $i_url_id, $i_show_id, $i_game_id, $i_attribute, $i_summary, $i_description, $i_op_name, $jump, $jumpVal
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function config_sp_prop_d($i_id){
		$spName = 'sp_prop_dn';
		$dbName = 'config';
		$param = array(
			$i_id
		);
	
		return $this->execSp($spName, $param, $dbName);
	}
	
	/**
	 * 查询某个时间段内的比赛场信息
	 * @param unknown $cascade
	 * @param unknown $sDate
	 * @param unknown $eDate
	 * @return unknown
	 */
	public function mge_sp_report_match($cascade, $sDate, $eDate){
		$spName = 'sp_report_match';
		$param = array(
				$cascade,
				$sDate,
				$eDate
		);
		return $this->execSp($spName, $param);
	}
	

	/**
	 * 新增用户,角色ID在存储过程中写死为50(渠道商)
	 * @param unknown $acount
	 * @param unknown $password
	 * @param unknown $remark
	 * @param unknown $role
	 * @return unknown
	 */
	public function manage_sp_web_addclientuser($acount, $password, $remark, $conn_db_id){
		$spName = 'sp_web_addclientuser';
		$dbName = 'manage';
		$param = array(
				$acount,
				$password,
				$remark,
				$conn_db_id,
		);
		return $this->execSp($spName, $param, $dbName);
	}

    public function manage_sp_news1_company_u_limit($id, $limit){
        $spName = 'sp_news1_company_u_limit';
        $dbName = 'manage';
        $param = array(
            $id,
            $limit,
        );
        return $this->execSp($spName, $param, $dbName);
    }
    public function manage_sp_news1_company_u_limit_1($id, $limit){
        $spName = 'sp_news1_company_u_limit_1';
        $dbName = 'manage';
        $param = array(
            $id,
            $limit,
        );
        return $this->execSp($spName, $param, $dbName);
    }
	
	/**
	 * 获取比赛场的数据
	 * @param unknown $sDate
	 * @param unknown $eDate
	 * @param unknown $cascade
	 * @return unknown
	 */
	public function mge_sp_event_cal_match($sDate, $eDate, $cascade){
		$spName = 'sp_report_match';
		$dbName = 'manage';
		$param = array(
				$sDate,
				$eDate,
				$cascade,
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function mge_sp_event_cal_matchgold($sDate, $eDate, $cascade){
		$spName = 'sp_report_MatchGold';
		$dbName = 'manage';
		$param = array(
				$sDate,
				$eDate,
				$cascade,
		);
		return $this->execSp($spName, $param, $dbName);
	}

	public function mge_sp_web_log_s($gid, $sDate, $eDate){
		$spName = 'sp_web_log_s';
		$dbName = 'manage';
		$param = array(
			$gid, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_robot($uid){
		$spName = 'sp_report_robot';
		$dbName = 'manage';
		$param = array(
			$uid
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_web_log_i($game_id, $user_type, $user_key, $uid, $acount, $prop_id, $prop_num, $gold){
		$spName = 'sp_web_log_i';
		$dbName = 'manage';
		$param = array(
			$game_id, $user_type, $user_key, $uid, $acount, $prop_id, $prop_num, $gold
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	/**
	 * 渠道取数
	 * @param unknown $gid
	 * @param unknown $cid
	 * @param unknown $sDate
	 * @param unknown $eDate
	 * @return unknown
	 */
	public function manage_sp_web_report_client($gid, $cid, $sDate, $eDate, $level){
		$spName = 'sp_web_report_client';
		$dbName = 'manage';
		$param = array(
				$gid, $cid, $sDate, $eDate,$level
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	/**
	 * 查看某渠道下的子渠道信息
	 * @param unknown $uid
	 */
	public function manage_sp_web_report_company($uid){
		$spName = 'sp_web_report_company';
		$dbName = 'manage';
		$param = array(
				$uid
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	/**
	 * 检查某个渠道id是否存在且属于某级渠道商
	 * @param unknown $cid
	 * @param unknown $level
	 * @return unknown
	 */
	public function manage_sp_web_check_cid($cid, $level){
		$spName = 'sp_web_check_cid';
		$dbName = 'manage';
		$param = array(
				$cid, $level
		);
		return $this->execSp($spName, $param, $dbName);
	}

    public function manage_sp_news1_web_company_u_status($uid, $status){
        $spName = 'sp_news1_web_company_u_status';
        $dbName = 'manage';
        $param = array(
            $uid, $status
        );
        return $this->execSp($spName, $param, $dbName);
    }
	
	public function mge_sp_room_date_s($sDate, $eDate, $str, $i){
		$spName = 'sp_report_room';
		$dbName = 'manage';
		$param = array(
				$sDate, $eDate, $str, $i
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_online_money_s_total($value, $sDate, $eDate){
		$spName = 'sp_online_money_s_total';
		$dbName = 'manage';
		$param = array(
			$value, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_gold_typpe_config_s($par1 = 1){
		$spName = 'sp_gold_typpe_config_s';
		$dbName = 'manage';
		$param = array(
			$par1
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_output_goods_sn($gid, $typeid, $sDate, $eDate){
		$spName = 'sp_report_output_goods_sn';
		$dbName = 'manage';
		$param = array(
			$gid, $typeid, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_report_clientGold($gid, $uid, $sDate, $eDate){
		$spName = 'sp_report_clientGold';
		$dbName = 'manage';
		$param = array(
				$gid, $uid, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_online_money_s_day($gid, $dateStr){
		$spName = 'sp_online_money_s_day';
		$dbName = 'manage';
		$param = array(
			$gid, $dateStr
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function mge_sp_prop_date_s($pid, $sDate, $eDate){
		$spName = 'sp_report_prop';
		$dbName = 'manage';
		$param = array(
				$pid, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_online_hall_s_total($type, $value, $sDate, $eDate){
		$spName = 'sp_online_hall_s_total';
		$dbName = 'manage';
		$param = array(
			$type, $value, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_online_hall_s_day($gid, $dateStr){
		$spName = 'sp_online_hall_s_day';
		$dbName = 'manage';
		$param = array(
			$gid, $dateStr
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	
	public function manage_sp_report_income_s_by_pay_id($pm1 = -1, $gid, $pm2 = -1, $sDate, $eDate){
		$spName = 'sp_report_income_s_by_pay_id';
		$dbName = 'manage';
		$param = array(
			$pm1, $gid, $pm2, $sDate, $eDate
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function mge_sp_report_hold_gold_0_s($gid){
		$spName = 'sp_report_hold_gold_0_s';
		$dbName = 'manage';
		$param = array(
				$gid
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function mge_sp_sys_delete_client($clientid){
		$spName = 'sp_sys_delete_client';
		$param = array(
				$clientid
		);
		return $this->execSp($spName, $param);
	}
	
	public function manage_sp_web_delete_client($clientid){
		$spName = 'sp_web_delete_client';
		$dbName = 'manage';
		$param = array(
				$clientid
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	
	public function mge_sp_report_hold_gold_7_s($gid){
		$spName = 'sp_report_hold_gold_7_s';
		$dbName = 'manage';
		$param = array(
				$gid
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function mge_sp_report_hold_gold_30_s($gid){
		$spName = 'sp_report_hold_gold_30_s';
		$dbName = 'manage';
		$param = array(
				$gid
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function mge_sp_report_hold_gold_90_s($gid){
		$spName = 'sp_report_hold_gold_90_s';
		$dbName = 'manage';
		$param = array(
				$gid
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	
	
	public function manage_sp_event_cal_goldtype($sDate, $eDate, $str, $i){
		$spName = 'sp_report_goldType';
		$dbName = 'manage';
		$param = array(
				$sDate, $eDate, $str, $i
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	public function manage_sp_feedback_s($uid, $status, $sDate, $eDate, $gid){
		$spName = 'sp_feedback_s';
		$dbName = 'manage';
		$param = array(
				$uid,
				'',
				$status,
				$sDate,
				$eDate,
				$gid,
				-1,	// 是否重要
				'1,10000'
		);
		return $this->execSp($spName, $param, $dbName);
	}
	
	/**
	 * 插入公司数据
	 * @param unknown $pid
	 * @param unknown $level
	 * @param unknown $uid
	 * @param unknown $name
	 * @param unknown $cid
	 * @return unknown
	 */
	public function manage_sp_web_company_i($pid, $level, $uid, $name, $phone, $cid, $email, $intoration){
		$spName = 'sp_web_company_i';
		$dbName = 'manage';
		$param = array(
				$pid, $level, $uid, $name, $phone,$cid,$email,$intoration,
		);
		$ret = $this->execSp($spName, $param, $dbName);
		
	}
	
	
	/**
	 * 获取某个渠道的全部信息
	 * @param unknown $cid
	 */
	public function manage_sp_get_client_info($uid){
		$spName = 'sp_web_company_s_by_uid';
		$dbName = 'manage';
		$param = array(
			$uid
		);
		$ret = $this->execSp($spName, $param, $dbName);
		
		// 获取失败
		if (isset($ret['msg'])) {
			$this->errorMsg = $ret['msg'];
			return false;
		}
		
		if (!isset($ret[0])) {
			$this->errorMsg = 'manage_sp_get_client_info执行异常,不合法的返回数据,中断执行';
			return false;
		}
		
		return $ret[0];
	}
}