<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 金币分析
 * @author yangf@songplay.cn
 * @date 2016-9-27
 */

class AnalGold extends CI_Controller{
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		// 获取金币发放类型配置
		echo $this->views->setRow(
				array(
						array(
								'type'=>'select',
								'name'=>'gid',
								'list'=>$this->config->item('gameList'),
						),
						array(
								'type'=>'sedate',
						),
						array(
								'type'=>'button',
								'col'=>1,
								'onclick'=>array(
									'type'=>'reload',
								),
						),
				)
		)->setRow(
			array(
				array(
					'type'=>'table',
					'thList'=>array('购买', '使用道具', '赠送', '奖励', '推广返利', '特殊处理', '道具合成', '比赛回收', '小游戏', '小额消耗', '台费消耗', 'AI回收', '发放总值', '发放总值', '回收总值', '其他', '系统总况', '金币总数'),
					'colList'=>array('quantity_22', 'quantity_7', 'quantity_20001'=>array(
							'class'=>'details-control', 'orderable'=>false, 'details'=>array(
								'quantity_2'=>'注册送',
								'quantity_53'=>'新手送',
								'quantity_3'=>'登录送',
								'quantity_4'=>'破产送',
							)
					), 'quantity_20002'=>array(
							'class'=>'details-control', 'orderable'=>false, 'details'=>array(
								'quantity_11'=>'比赛',
								'quantity_14'=>'活动',
								'quantity_20003'=>'任务',	// 9 20 21 23
								'quantity_52'=>'互动',
								'quantity_61'=>'万人场奖池',
							)
					), 'quantity_57', 'quantity_20004'=>array('class'=>'details-control', 'orderable'=>false, 'details'=>array(
						'quantity_5'=>'后台发放',
						'quantity_51'=>'特殊帐号',
					),), 'quantity_6', 'quantity_20005'=>array('class'=>'details-control', 'orderable'=>false, 'details'=>array(
						'quantity_12'=>'报名费',
						'quantity_58'=>'增购',
						'quantity_59'=>'重购',
					),), 'quantity_20006'=>array('class'=>'details-control', 'orderable'=>false, 'details'=>array(
						'quantity_13'=>'转盘',
						'quantity_60'=>'万人场',
						'quantity_45'=>'红黑牌',
					),), 'quantity_20007'=>array('class'=>'details-control', 'orderable'=>false, 'details'=>array(
						'quantity_18'=>'送小费',
						'quantity_26'=>'互动消耗',
						'quantity_42'=>'零头消耗',
					),), 'quantity_1', '杨琼的回收', 'X', 'Y', 'Z', 'X + Y + Z', 'total'),
				),
			)
		)->done();
	}
	
	/**
	 * 获取th字符串
	 * @param unknown $data
	 * @return string
	 */
	private function getThObj($data){
		$thStr = '';
		foreach ($data as $value) {
			$gname = isset($value['gname']) ? $value['gname'] : '未定义列';
			$thStr .= '<th>'.$gname.'</th>';
		}
		return $thStr;
	}
	
	/**
	 * 获取Col数据对象
	 * @param unknown $data
	 * @return string
	 */
	private function getColObj($data){
		$colObj = array();
		foreach ($data as $k=>$value) {
			$tid = isset($value['tid']) ? $value['tid'] : 'post_date';
			$colObj[$k]['data'] = $tid != 'post_date' ? 'quantity_'.$tid : $tid;
			$colObj[$k]['defaultContent'] = 0;
		}
		return json_encode($colObj, True);
	}
	
	/**
	 * 表格ajax数据源
	 */
	public function ajaxTable(){
		$gid = get_extra('gid');
		$sDate = get_extra('sDate');
		$eDate = get_extra('eDate');
		
		// echo json_encode($this->getData($type, $sDate, $eDate, $gid), TRUE);
		
		$sendData = $this->getData(-1, $sDate, $eDate, $gid);
		
		
		$this->getTotalData($sendData, 'quantity_total');
		
		$elseData = $this->getData(0, $sDate, $eDate, $gid);
		
		// 其他列的标记为quantity_10000
		$this->getTotalData($elseData, 'quantity_10000', TRUE);
		
		$result = $this->dataext->tableDataMerge('post_date',array('sort'=>'post_date desc','sortType'=>'date'), $sendData, $elseData);
		
		// 格式化数据
		foreach ($result as $key=>&$value) {
			foreach ($value as $k=>&$v) {
				if ($k == 'post_date') {
					continue;
				};
				$v = number_format($v);
			}
		}
		echo json_encode(array('data'=>$result));
	}
	
	/**
	 * 调用存储过程获取数据
	 * @param string $sDate
	 * @param string $eDate
	 * @param number $gid
	 * @return unknown
	 */
	private function getData($type = -1, $sDate = '', $eDate = '', $gid = 44){
		$result = $this->dbapp->manage_sp_report_output_goods_sn($gid, $type, $sDate, $eDate);
		
		// 修改重复的字段名
		$result = $this->dataext->calData($result,array(),
				$cals = array(
						array('field'=>'out_type',
								'valType'=>'custom',
								'action' =>'$val[\'quantity_\'.$val[\'out_type\']] = $val[\'quantity\'];',
				))
		);
		
		// 删掉多余的字段
		$result = $this->dataext->calData($result,array('quantity'=>false, 'out_type' => false));
		
		//合并所有数据
		$result = $this->dataext->tableDataMerge('post_date',array('sort'=>'post_date desc','sortType'=>'date'), $result);

		
		// 数据处理
 		foreach ($result as &$value) {
			if (isset($value['quantity_99'])) {
				$value['quantity_99'] = '-'.$value['quantity_99'];
			}
		}
		
		// $this->getElseData($result, $param);
		
		return $result;
	}
	
	/**
	 * 获取总数,第二列为新总数列的列名,第三名表示是否删除其他列
	 * @param unknown $data
	 * @param string $colName
	 * @param string $isDel
	 */
	private function getTotalData(&$data, $colName = '', $isDel = FALSE){
		foreach ($data as $key=>&$value) {
			$data[$key][$colName] = 0;
			
			foreach ($value as $k=>&$v) {
				if ($k == 'post_date' || $k == $colName || $k == 'quantity_85') {
					continue;
				};
				$data[$key][$colName] += $v;
				
				if ($isDel) {
					unset($data[$key][$k]);
				}
			}
		}
	}
}