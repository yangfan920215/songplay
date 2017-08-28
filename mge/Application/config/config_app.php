<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 页面权限划分
$config['permissions'] = array(
	'getData'=>1,
	'ajaxData'=>1,
	'ajaxDataTable'=>1,
	'ajaxMorris'=>1,
	'send'=>1,
	'recovery'=>1,
	'echoMorrisDate'=>1,
);

// 站点标题
$config['title'] = '91德州管理平台';

$config['sys_libs_dir'] = dirname($_SERVER['DOCUMENT_ROOT']).'/pub/libs/class/';

// 下载站点根目录
$config['down_file'] = '/data/web/html/down.songplay.cn/';

$config['down_url'] = '119.28.19.213:8003';

// 默认选中游戏(德州)
$config['defGameId'] = 44;

/*
 * 默认搜索栏日期值
 */
$config['searchDate'] = array(
	'sDate'=>date('Y-m-d', strtotime('-1 day')),
	'eDate'=>date('Y-m-d', strtotime('-1 day')),
);

// 游戏列表
$config['gameList'] = array(
		44=>'德州扑克',
		100=>'斗地主',
);

$config['gameListv1'] = array(
	array('id'=>44, 'name'=>'德州扑克', 'check'=>true),
);

// 游戏列表
$config['cList'] = array(
		2=>'二级渠道',
		3=>'三级渠道',
);
$config['userType1'] = array(
	0=>'UID',
	1=>'GUID',
	2=>'UNAME',
	3=>'EMAIL',
);
$config['userType'] = array(
	array('id'=>0, 'name'=>'UID', 'pname'=>'uid'),
	array('id'=>1, 'name'=>'GUID', 'pname'=>'guid'),
	array('id'=>2, 'name'=>'UNAME', 'pname'=>'uname'),
	array('id'=>3, 'name'=>'EMAIL', 'pname'=>'email'),
);

// 房间id第一位对应游戏id
$config['gidRoomId'] = array(
	'1'=>44,
	'7'=>100,
);

$config['ptype'] = array(
		array('id'=>0, 'name'=>'道具'),
		array('id'=>1, 'name'=>'礼包'),
		array('id'=>2, 'name'=>'商品'),
		array('id'=>3, 'name'=>'金币'),
);

$config['getPropType'] = array(
	array('id'=>'购买', 'name'=>'购买'),
	array('id'=>'获赠', 'name'=>'获赠'),
	array('id'=>'道具使用产生', 'name'=>'道具使用产生'),
	array('id'=>'抽奖', 'name'=>'抽奖'),
	array('id'=>'其它', 'name'=>'其它'),
	array('id'=>0, 'name'=>'自定义'),
);

$config['error'] = array(
	1=>'缺少必须的参数',
	2=>'config:gameList配置错误',
);

$config['dateInterval'] = array(
	'sDate'=>1,
	'eDate'=>8,	
);

$config['site'] = array(
	array(
		'dname'=>'show.songplay.cn',
		'desc'=>'活动中心',
	),
	array(
		'dname'=>'notify.songplay.cn',
		'desc'=>'支付中心',
	),
	array(
		'dname'=>'api.songplay.cn',
		'desc'=>'对外接口中心',
	),
	array(
		'dname'=>'inside.songplay.cn',
		'desc'=>'对内接口中心',
	),
);

$config['upcid'] = 217;

/**
 * 各个用户链接数据库配置表,0为默认配置
 */
$config_db_match = array(
	0=>array(
		'manage'=>'baseManage',
		'config'=>'baseConfig',
	),
	1=>array(
		'manage'=>'manage_91',
		'config'=>'config_91',
	),
	2=>array(
		'manage'=>'manage_pkt',
		'config'=>'config_pkt',
	),
);

// 用户独立DB链接配置
$config['conn_db'] = array(
    '0'=>array(
        'config'=>'baseConfig',
        'manage'=>'baseManage',
        'bl'=>'0.8',
    ),
    // 默认
	'1'=>array(
		'config'=>'baseConfig',
		'manage'=>'baseManage',
		'bl'=>'0.8',
	),	
);

$config['prop_img_down_url'] = '/data/web/html/down.songplay.cn/dzpk/android/00000/0.0.0/1/';
