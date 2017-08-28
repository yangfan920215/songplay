<?php
/**
 * 资产分布
 * @author yangf@songplay.cn
 * @date 2016-1-7
 */
class AssetsDistribution extends CI_Controller{
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		// 获取初试化页面所需报表数据
		//$chartData = $this->data(44, 0, 1);

		$this->parser->parse('analysis/assetsDistribution.html', $this->data);
	}
	
	public function ajaxPieData(){
		$gid = get_comm_req('gid');
		$userLoginType = 0;
		$chartData = $this->data($gid, $userLoginType, 1);
		echo $this->html->setPieChart('zc', $chartData, 1)->body;
	}
	
	public function ajaxTable(){
		$gid = get_extra_data('gid');
		$userLoginType = 0;
		$data = $this->data($gid, $userLoginType, 2);
		echo json_encode(array('data'=>$data));
	}
	
	/**
	 * 获取该页面所需的数据
	 * @param number $gid 游戏id
	 * @param number $userLoginType 0:所以用户,1,最近七天登录,2:最后一个月登录,3:最近三个月登录
	 * @param number $dataType	1代表图数据,2为表格数据
	 * @return array
	 */
	private function data($gid, $userLoginType, $dataType){
		//从DB拿数据
		$all = $this->dbapp->mge_sp_report_hold_gold_0_s($gid);
		$data7 = $this->dbapp->mge_sp_report_hold_gold_7_s($gid);
		$data30 = $this->dbapp->mge_sp_report_hold_gold_30_s($gid);
		$data90 = $this->dbapp->mge_sp_report_hold_gold_90_s($gid);
		 
		$data = array();
		//if(isset($all[0]) && isset($data7[0]) && isset($data30[0]) && isset($data90[0])) {
			$data = array(
					0 => array('id'=>0,'section' => '0','quantity1' => isset($all[0]['quantity1']) ? $all[0]['quantity1'] : 0, 'ratio1' => isset($all[0]['ratio1']) ? $all[0]['ratio1'] : 0, 'quantity2' => isset($data7[0]['quantity1']) ? $data7[0]['quantity1'] : 0, 'ratio2' => isset($data7[0]['ratio1']) ? $data7[0]['ratio1'] : 0, 'quantity3' => isset($data30[0]['quantity1']) ? $data30[0]['quantity1'] : 0, 'ratio3' => isset($data30[0]['ratio1']) ? $data30[0]['ratio1'] : 0, 'quantity4' => isset($data90[0]['quantity1']) ? $data90[0]['quantity1'] : 0, 'ratio4' => isset($data90[0]['ratio1']) ? $data90[0]['ratio1'] : 0),
					1 => array('id'=>1,'section' => '1-999','quantity1' => isset($all[0]['quantity2']) ? $all[0]['quantity2'] : 0, 'ratio1' => isset($all[0]['ratio2']) ? $all[0]['ratio2'] : 0, 'quantity2' => isset($data7[0]['quantity2']) ? $data7[0]['quantity2'] : 0, 'ratio2' => isset($data7[0]['ratio2']) ? $data7[0]['ratio2'] : 0, 'quantity3' => isset($data30[0]['quantity2']) ? $data30[0]['quantity2'] : 0, 'ratio3' => isset($data30[0]['ratio2']) ? $data30[0]['ratio2'] : 0, 'quantity4' => isset($data90[0]['quantity2']) ? $data90[0]['quantity2'] : 0, 'ratio4' => isset($data90[0]['ratio2']) ? $data90[0]['ratio2'] : 0),
					2 => array('id'=>2,'section' => '1000-2999','quantity1' => isset($all[0]['quantity3']) ? $all[0]['quantity3'] : 0, 'ratio1' => isset($all[0]['ratio3']) ? $all[0]['ratio3'] : 0, 'quantity2' => isset($data7[0]['quantity3']) ? $data7[0]['quantity3'] : 0, 'ratio2' => isset($data7[0]['ratio3']) ? $data7[0]['ratio3'] : 0, 'quantity3' => isset($data30[0]['quantity3']) ? $data30[0]['quantity3'] : 0, 'ratio3' => isset($data30[0]['ratio3']) ? $data30[0]['ratio3'] : 0, 'quantity4' => isset($data90[0]['quantity3']) ? $data90[0]['quantity3'] : 0, 'ratio4' => isset($data90[0]['ratio3']) ? $data90[0]['ratio3'] : 0),
					3 => array('id'=>3,'section' => '3000-4999','quantity1' => isset($all[0]['quantity4']) ? $all[0]['quantity4'] : 0, 'ratio1' => isset($all[0]['ratio4']) ? $all[0]['ratio4'] : 0, 'quantity2' => isset($data7[0]['quantity4']) ? $data7[0]['quantity4'] : 0, 'ratio2' => isset($data7[0]['ratio4']) ? $data7[0]['ratio4'] : 0, 'quantity3' => isset($data30[0]['quantity4']) ? $data30[0]['quantity4'] : 0, 'ratio3' => isset($data30[0]['ratio4']) ? $data30[0]['ratio4'] : 0, 'quantity4' => isset($data90[0]['quantity4']) ? $data90[0]['quantity4'] : 0, 'ratio4' => isset($data90[0]['ratio4']) ? $data90[0]['ratio4'] : 0),
					4 => array('id'=>4,'section' => '5000-9999','quantity1' => isset($all[0]['quantity5']) ? $all[0]['quantity5'] : 0, 'ratio1' => isset($all[0]['ratio5']) ? $all[0]['ratio5'] : 0, 'quantity2' => isset($data7[0]['quantity5']) ? $data7[0]['quantity5'] : 0, 'ratio2' => isset($data7[0]['ratio5']) ? $data7[0]['ratio5'] : 0, 'quantity3' => isset($data30[0]['quantity5']) ? $data30[0]['quantity5'] : 0, 'ratio3' => isset($data30[0]['ratio5']) ? $data30[0]['ratio5'] : 0, 'quantity4' => isset($data90[0]['quantity5']) ? $data90[0]['quantity5'] : 0, 'ratio4' => isset($data90[0]['ratio5']) ? $data90[0]['ratio5'] : 0),
					5 => array('id'=>5,'section' => '10000-19999','quantity1' => isset($all[0]['quantity6']) ? $all[0]['quantity6'] : 0, 'ratio1' => isset($all[0]['ratio6']) ? $all[0]['ratio6'] : 0, 'quantity2' => isset($data7[0]['quantity6']) ? $data7[0]['quantity6'] : 0, 'ratio2' => isset($data7[0]['ratio6']) ? $data7[0]['ratio6'] : 0, 'quantity3' => isset($data30[0]['quantity6']) ? $data30[0]['quantity6'] : 0, 'ratio3' => isset($data30[0]['ratio6']) ? $data30[0]['ratio6'] : 0, 'quantity4' => isset($data90[0]['quantity6']) ? $data90[0]['quantity6'] : 0, 'ratio4' => isset($data90[0]['ratio6']) ? $data90[0]['ratio6'] : 0),
					6 => array('id'=>6,'section' => '20000-49999','quantity1' => isset($all[0]['quantity7']) ? $all[0]['quantity7'] : 0, 'ratio1' => isset($all[0]['ratio7']) ? $all[0]['ratio7'] : 0, 'quantity2' => isset($data7[0]['quantity7']) ? $data7[0]['quantity7'] : 0, 'ratio2' => isset($data7[0]['ratio7']) ? $data7[0]['ratio7'] : 0, 'quantity3' => isset($data30[0]['quantity7']) ? $data30[0]['quantity7'] : 0, 'ratio3' => isset($data30[0]['ratio7']) ? $data30[0]['ratio7'] : 0, 'quantity4' => isset($data90[0]['quantity7']) ? $data90[0]['quantity7'] : 0, 'ratio4' => isset($data90[0]['ratio7']) ? $data90[0]['ratio7'] : 0),
					7 => array('id'=>7,'section' => '50000-99999','quantity1' => isset($all[0]['quantity8']) ? $all[0]['quantity8'] : 0, 'ratio1' => isset($all[0]['ratio8']) ? $all[0]['ratio8'] : 0, 'quantity2' => isset($data7[0]['quantity8']) ? $data7[0]['quantity8'] : 0, 'ratio2' => isset($data7[0]['ratio8']) ? $data7[0]['ratio8'] : 0, 'quantity3' => isset($data30[0]['quantity8']) ? $data30[0]['quantity8'] : 0, 'ratio3' => isset($data30[0]['ratio8']) ? $data30[0]['ratio8'] : 0, 'quantity4' => isset($data90[0]['quantity8']) ? $data90[0]['quantity8'] : 0, 'ratio4' => isset($data90[0]['ratio8']) ? $data90[0]['ratio8'] : 0),
					8 => array('id'=>8,'section' => '100000-299999','quantity1' => isset($all[0]['quantity9']) ? $all[0]['quantity9'] : 0, 'ratio1' => isset($all[0]['ratio9']) ? $all[0]['ratio9'] : 0, 'quantity2' => isset($data7[0]['quantity9']) ? $data7[0]['quantity9'] : 0, 'ratio2' => isset($data7[0]['ratio9']) ? $data7[0]['ratio9'] : 0, 'quantity3' => isset($data30[0]['quantity9']) ? $data30[0]['quantity9'] : 0, 'ratio3' => isset($data30[0]['ratio9']) ? $data30[0]['ratio9'] : 0, 'quantity4' => isset($data90[0]['quantity9']) ? $data90[0]['quantity9'] : 0, 'ratio4' => isset($data90[0]['ratio9']) ? $data90[0]['ratio9'] : 0),
					9 => array('id'=>9,'section' => '300000-999999','quantity1' => isset($all[0]['quantity10']) ? $all[0]['quantity10'] : 0, 'ratio1' => isset($all[0]['ratio10']) ? $all[0]['ratio10'] : 0, 'quantity2' => isset($data7[0]['quantity10']) ? $data7[0]['quantity10'] : 0, 'ratio2' => isset($data7[0]['ratio10']) ? $data7[0]['ratio10'] : 0, 'quantity3' => isset($data30[0]['quantity10']) ? $data30[0]['quantity10'] : 0, 'ratio3' => isset($data30[0]['ratio10']) ? $data30[0]['ratio10'] : 0, 'quantity4' => isset($data90[0]['quantity10']) ? $data90[0]['quantity10'] : 0, 'ratio4' => isset($data90[0]['ratio10']) ? $data90[0]['ratio10'] : 0),
					10 => array('id'=>10,'section' => '1000000-4999999','quantity1' => isset($all[0]['quantity11']) ? $all[0]['quantity11'] : 0, 'ratio1' => isset($all[0]['ratio11']) ? $all[0]['ratio11'] : 0, 'quantity2' => isset($data7[0]['quantity11']) ? $data7[0]['quantity11'] : 0, 'ratio2' => isset($data7[0]['ratio11']) ? $data7[0]['ratio11'] : 0, 'quantity3' => isset($data30[0]['quantity11']) ? $data30[0]['quantity11'] : 0, 'ratio3' => isset($data30[0]['ratio11']) ? $data30[0]['ratio11'] : 0, 'quantity4' => isset($data90[0]['quantity11']) ? $data90[0]['quantity11'] : 0, 'ratio4' => isset($data90[0]['ratio11']) ? $data90[0]['ratio11'] : 0),
					11 => array('id'=>11,'section' => '5000000-9999999','quantity1' => isset($all[0]['quantity12']) ? $all[0]['quantity12'] : 0, 'ratio1' => isset($all[0]['ratio12']) ? $all[0]['ratio12'] : 0, 'quantity2' => isset($data7[0]['quantity12']) ? $data7[0]['quantity12'] : 0, 'ratio2' => isset($data7[0]['ratio12']) ? $data7[0]['ratio12'] : 0, 'quantity3' => isset($data30[0]['quantity12']) ? $data30[0]['quantity12'] : 0, 'ratio3' => isset($data30[0]['ratio12']) ? $data30[0]['ratio12'] : 0, 'quantity4' => isset($data90[0]['quantity12']) ? $data90[0]['quantity12'] : 0, 'ratio4' => isset($data90[0]['ratio12']) ? $data90[0]['ratio12'] : 0),
					12 => array('id'=>12,'section' => '10000000-49999999','quantity1' => isset($all[0]['quantity13']) ? $all[0]['quantity13'] : 0, 'ratio1' => isset($all[0]['ratio13']) ? $all[0]['ratio13'] : 0, 'quantity2' => isset($data7[0]['quantity13']) ? $data7[0]['quantity13'] : 0, 'ratio2' => isset($data7[0]['ratio13']) ? $data7[0]['ratio13'] : 0, 'quantity3' => isset($data30[0]['quantity13']) ? $data30[0]['quantity13'] : 0, 'ratio3' => isset($data30[0]['ratio13']) ? $data30[0]['ratio13'] : 0, 'quantity4' => isset($data90[0]['quantity13']) ? $data90[0]['quantity13'] : 0, 'ratio4' => isset($data90[0]['ratio13']) ? $data90[0]['ratio13'] : 0),
					13 => array('id'=>13,'section' => '50000000-99999999','quantity1' => isset($all[0]['quantity14']) ? $all[0]['quantity14'] : 0, 'ratio1' => isset($all[0]['ratio14']) ? $all[0]['ratio14'] : 0, 'quantity2' => isset($data7[0]['quantity14']) ? $data7[0]['quantity14'] : 0, 'ratio2' => isset($data7[0]['ratio14']) ? $data7[0]['ratio14'] : 0, 'quantity3' => isset($data30[0]['quantity14']) ? $data30[0]['quantity14'] : 0, 'ratio3' => isset($data30[0]['ratio14']) ? $data30[0]['ratio14'] : 0, 'quantity4' => isset($data90[0]['quantity14']) ? $data90[0]['quantity14'] : 0, 'ratio4' => isset($data90[0]['ratio14']) ? $data90[0]['ratio14'] : 0),
					14 => array('id'=>14,'section' => '100000000-999999999','quantity1' => isset($all[0]['quantity15']) ? $all[0]['quantity15'] : 0, 'ratio1' => isset($all[0]['ratio15']) ? $all[0]['ratio15'] : 0, 'quantity2' => isset($data7[0]['quantity15']) ? $data7[0]['quantity15'] : 0, 'ratio2' => isset($data7[0]['ratio15']) ? $data7[0]['ratio15'] : 0, 'quantity3' => isset($data30[0]['quantity15']) ? $data30[0]['quantity15'] : 0, 'ratio3' => isset($data30[0]['ratio15']) ? $data30[0]['ratio15'] : 0, 'quantity4' => isset($data90[0]['quantity15']) ? $data90[0]['quantity15'] : 0, 'ratio4' => isset($data90[0]['ratio15']) ? $data90[0]['ratio15'] : 0),
					15 => array('id'=>15,'section' => '1000000000以上','quantity1' => isset($all[0]['quantity16']) ? $all[0]['quantity16'] : 0, 'ratio1' => isset($all[0]['ratio16']) ? $all[0]['ratio16'] : 0, 'quantity2' => isset($data7[0]['quantity16']) ? $data7[0]['quantity16'] : 0, 'ratio2' => isset($data7[0]['ratio16']) ? $data7[0]['ratio16'] : 0, 'quantity3' => isset($data30[0]['quantity16']) ? $data30[0]['quantity16'] : 0, 'ratio3' => isset($data30[0]['ratio16']) ? $data30[0]['ratio16'] : 0, 'quantity4' => isset($data90[0]['quantity16']) ? $data90[0]['quantity16'] : 0, 'ratio4' => isset($data90[0]['ratio16']) ? $data90[0]['ratio16'] : 0),
					16 => array('id'=>16,'section' => '5000000000以上','quantity1' => isset($all[0]['quantity17']) ? $all[0]['quantity17'] : 0, 'ratio1' => isset($all[0]['ratio17']) ? $all[0]['ratio17'] : 0, 'quantity2' => isset($data7[0]['quantity17']) ? $data7[0]['quantity17'] : 0, 'ratio2' => isset($data7[0]['ratio17']) ? $data7[0]['ratio17'] : 0, 'quantity3' => isset($data30[0]['quantity17']) ? $data30[0]['quantity17'] : 0, 'ratio3' => isset($data30[0]['ratio17']) ? $data30[0]['ratio17'] : 0, 'quantity4' => isset($data90[0]['quantity17']) ? $data90[0]['quantity17'] : 0, 'ratio4' => isset($data90[0]['ratio17']) ? $data90[0]['ratio17'] : 0),
			);
		//}
		
		//默认为所有用户资产分布情况，另外有其他三种选项选择，当选择其中一项时，则显示对应条件的资产数据
		$loginData = array();
		if($userLoginType == 0) {
			$loginData = $all;
		} else if($userLoginType == 1) {
			$loginData = $data7;
		} else if($userLoginType == 2) {
			$loginData = $data30;
		} else if($userLoginType == 3) {
			$loginData = $data90;
		}
		
		$chart = array(
			array(
				'label'=>'0',		
			),	
			array(
				'label'=>'1-999',
			),
			array(
				'label'=>'1000-2999',
			),
			array(
				'label'=>'3000-4999',
			),
			array(
				'label'=>'5000-9999',
			),
			array(
				'label'=>'10000-19999',
			),
			array(
				'label'=>'20000-49999',
			),
			array(
				'label'=>'50000-99999',
			),
			array(
				'label'=>'100000-299999',
			),
			array(
				'label'=>'300000-999999',
			),
			array(
				'label'=>'1000000-4999999',
			),
			array(
				'label'=>'5000000-9999999',
			),
			array(
				'label'=>'10000000-49999999',
			),
			array(
				'label'=>'50000000-99999999',
			),
			array(
				'label'=>'100000000-999999999',
			),
			array(
				'label'=>'1000000000以上',
			),
			array(
				'label'=>'5000000000以上',
			),
		);
		
		// 图表所需数据
		foreach ($chart as $key=>&$value) {
			$k = $key + 1;
			$value['data'] = isset($loginData[0]['quantity'.$k]) ? $loginData[0]['quantity'.$k] : 0;
		}
		
		if ($dataType == 1) {
			return $chart;
		}else if($dataType == 2){
			return $data;
		}
	}
}