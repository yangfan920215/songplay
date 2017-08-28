<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author yangf@songplay.cn
 * @date 2016-8-5
 */

class Match extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		echo $this->views->setRow(
				array(
						array(
							'type'=>'select',
							'name'=>'gid',
							'list'=>$this->config->item('gameList'),
						),
						array(
							'type'=>'select',
							'name'=>'keytype',
							'list'=>$this->config->item('userType1'),
						),
						array(
							'name'=>'ukey',
							'desc'=>'用户标识',
						),
						array(
							'type'=>'date',
						),
						array(
							'type'=>'time',
							'name'=>'stime',
							'def'=>'00:00',
						),
						array(
							'type'=>'time',
							'name'=>'etime',
							'def'=>'23:59',
						),
				)
		)->setRow(
			array(
					array(
							'type'=>'button',
							'col'=>3,
							'onclick'=>array(
									'type'=>'reload',
							),
					),
			)		
		)->setRow(
				array(
						array(
								'type'=>'table',
								'thList'=>array('牌局结束时间', '牌桌id', '牌局信息'),
								'colList'=>array(
									'post_date', 
									'desk_id', 
									'matchinfo'=>array(
										'class'=>'details-control',
										'orderable'=>false,
										"defaultContent"=>"",
										'table'=>array('pjTable', 'djTable'),
									),
								),
						),
				)
		)->done();
	}
	
	public function ajaxTable(){
		$gid = get_extra('gid');
		$keytype = get_extra('keytype');
		$ukey = get_extra('ukey');
		$date = get_extra('date');
		$stime = get_extra('stime');
		$etime = get_extra('etime');

		if ($ukey === '') {
			echo json_encode(array('data'=>array()));
			exit;
		}
		
		require $this->config->item('sys_libs_dir') . 'http.class.php';
		require $this->config->item('sys_libs_dir') . 'gasapp.class.php';
		require $this->config->item('sys_libs_dir') . 'gameapp.class.php';
		
		$gasapp = gasapp::init();
		
		$uid = getUid($keytype, $ukey, $gasapp, $this->config->item('userType'));
		
	
		$result = $this->dbapp->manage_sp_report_versus_vcr($date, $stime, $etime, $uid, $gid);
	
		foreach ($result as &$value) {
			if (!isset($value['data'])) {
				continue;
			}
			
			$strl = @unpack("va/I9b/C18c/C5d/Ie/Cf/Cg/Ch/l9i/l9j/vk", $value['data'])['k'];
			$unp = "va/I9b/C18c/C5d/Ie/Cf/Cg/Ch/l9i/l9j/vk/a" . $strl . 'l';
			$value['data'] = @unpack($unp, $value['data']);
			
			// 牌局信息
			$value['pjTable'] = array(
				'title'=>'牌局信息',
				'head'=>array('桌子ID', '公牌', '前注', '小盲用户', '大盲用户', '庄家'),
				'rows'=>array(
					array('', '', '', '', '', '',)		
				),
			);
			
			// 对局信息
			$value['djTable'] = array(
				'title'=>'对局信息',
				'head'=>array('', '0号桌', '1号桌', '2号桌', '3号桌', '4号桌', '5号桌', '6号桌', '7号桌', '8号桌'),
				'rows'=>array(
					array('用户ID'),
					array('手牌', '', '', '', '', '', '', '', '', ''),
					array('开始金币', '', '', '', '', '', '', '', '', ''),
					array('结束金币', '', '', '', '', '', '', '', '', ''),
				),
			);
			
			
			if (is_array($value['data'])) {
				foreach ($value['data'] as $key1 => $value1) {
					switch ($key1) {
						// 桌子ID
						case 'a':
							$value['pjTable']['rows'][0][0] = $value['room_id'];
							break;
							// 用户ID
						case 'b1':
						case 'b2':
						case 'b3':
						case 'b4':
						case 'b5':
						case 'b6':
						case 'b7':
						case 'b8':
						case 'b9':
							$value['djTable']['rows'][0][] = $value1 != 0 ? $value1 : '';
							break;
							// 手上扑克
						case 'c1':
						case 'c2':
							$value['djTable']['rows'][1][1] .= $value1 != 0 ? $this->getPimg($value1) : '';
							break;
						case 'c3':
						case 'c4':
							$value['djTable']['rows'][1][2] .= $value1 != 0 ? $this->getPimg($value1) : '';
							break;
						case 'c5':
						case 'c6':
							$value['djTable']['rows'][1][3] .= $value1 != 0 ? $this->getPimg($value1) : '';
							break;
						case 'c7':
						case 'c8':
							$value['djTable']['rows'][1][4] .= $value1 != 0 ? $this->getPimg($value1) : '';
							break;
						case 'c9':
						case 'c10':
							$value['djTable']['rows'][1][5] .= $value1 != 0 ? $this->getPimg($value1) : '';
							break;
						case 'c11':
						case 'c12':
							$value['djTable']['rows'][1][6] .= $value1 != 0 ? $this->getPimg($value1) : '';
							break;
						case 'c13':
						case 'c14':
							$value['djTable']['rows'][1][7] .= $value1 != 0 ? $this->getPimg($value1) : '';
							break;
						case 'c15':
						case 'c16':
							$value['djTable']['rows'][1][8] .= $value1 != 0 ? $this->getPimg($value1) : '';
							break;
						case 'c17':
						case 'c18':
							$value['djTable']['rows'][1][9] .= $value1 != 0 ? $this->getPimg($value1) : '';
							break;
							// 桌面扑克
						case 'd1':
						case 'd2':
						case 'd3':
						case 'd4':
						case 'd5':
							$value['pjTable']['rows'][0][1] .= $value1 != 0 ? $this->getPimg($value1) : '';;
							break;
							// 前注
						case 'e':
							$value['pjTable']['rows'][0][2] .= $value1;
							break;
							// 小盲用户
						case 'f':
							$value['pjTable']['rows'][0][3] .= $value1 . '号桌';
							break;
							// 大盲用户
						case 'g':
							$value['pjTable']['rows'][0][4] .= $value1 . '号桌';
							break;
							// 庄家
						case 'h':
							$value['pjTable']['rows'][0][5] .= $value1 . '号桌';
							break;
							// 豆,金币(游戏支出)
						case 'i1':
						case 'i2':
						case 'i3':
						case 'i4':
						case 'i5':
						case 'i6':
						case 'i7':
						case 'i8':
						case 'i9':
							$ki = intval(str_replace('i', '', $key1));
							$value['djTable']['rows'][2][$ki] = $value1;;
							break;
							// 豆,金币(现在)
						case 'j1':
						case 'j2':
						case 'j3':
						case 'j4':
						case 'j5':
						case 'j6':
						case 'j7':
						case 'j8':
						case 'j9':
							$kj = intval(str_replace('j', '', $key1));
							$value['djTable']['rows'][3][$kj] = $value1;;
							break;
							// 加注信息
						case 'l':
							$keyl = 4;
							$lun = 1;
							// 该论第几次操作
							$glcz = 1;
							//echo $value1;
				
							$lArr = explode('|', $value1);
							foreach ($lArr as $value2) {
								// $value['djTable']['rows'][$keyl] = array('', '', '', '', '', '', '', '', '', );
									
								$xz = explode(',', $value2);
								if (count($xz) != 2 || !isset($xz[0]) || !isset($xz[1])) {
									$glcz = 1;
									// 数据格式不正确则过滤
									continue;
								}
									
								// 椅子号
								$yz = $xz[0];
								// 下注值
								$xzxx = $xz[1];
								// 第几轮发牌
								$fp = 1;
									
								//echo $glcz.'-';
									
								if ($xzxx != -2) {
									// 描述
									$desc = '';
									switch ($xzxx) {
										case 0:
											$desc .= $glcz .'.看牌 ';
											break;
										case -1:
											$desc .= $glcz . '.弃牌 ';
											break;
										default:
											$desc .= $glcz . '.下注<span style="color:red">' . $xzxx . '</span>金币 ';
											break;
									}
				
									$glcz++;
								}else{
									$keyl++;
									$lun++;
									$desc = '';
									$glcz = 1;
								}
									
									
								$value['djTable']['rows'][$keyl][0] = '第' . $lun . '轮下注信息';
									
				
								if (!isset($value['djTable']['rows'][$keyl][$yz + 1])) {
									$value['djTable']['rows'][$keyl][$yz + 1] = '';
								}
									
								$value['djTable']['rows'][$keyl][$yz + 1] .= $desc;
									
								// 填充空值
								$tc = $yz;
								while (!isset($value['djTable']['rows'][$keyl][$tc])) {
									$value['djTable']['rows'][$keyl][$tc] = '';
									$tc--;
								}
							}
							break;
						default:
							continue;
							break;
					};
				}
				}
			}
			

		
		// 提出无用户的数据
		
	
		
		echo json_encode(array('data'=>$result));
	}
	
	private function imatch(&$data, $rum, $yz, $desc){
		$data[$rum][$yz + 1] .= $desc;
	}
	
	private function getPimg($num){
		$cardnumArr = array('A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K');
		// 方块-梅花-红桃-黑桃
		$colorArr = array('suitdiamonds', 'suitclubs', 'suithearts', 'suitspades');
		
		$fk = range(1, 13);
		$mh = range(17, 29);
		$hot = range(33, 45);
		$het = range(49, 61);
		
		if (in_array($num, $fk)) {
			$color = $colorArr[0];
			$card = $cardnumArr[$num - 1];
		}elseif(in_array($num, $mh)){
			$color = $colorArr[1];
			$card = $cardnumArr[$num - $mh[0]];
		}elseif(in_array($num, $hot)){
			$color = $colorArr[2];
			$card = $cardnumArr[$num - $hot[0]];
		}elseif(in_array($num, $het)){
			$color = $colorArr[3];
			$card = $cardnumArr[$num - $het[0]];
		}else{
			return '未知牌型';
		}
		
		return '<div class="card ' . $color . '">' . '<p>' . $card . '</p></div>';
	}
	
	
	private function bin2str($str = ''){
	    $arr = explode(' ', $str);
	    foreach($arr as &$v){
	        $v = pack("H" . strlen(base_convert($v, 2, 16)), base_convert($v, 2, 16));
	    }
	
	    return join('', $arr);
	}
}