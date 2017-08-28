<?php
/**
 * @author yangf@songplay.cn
 * @date 2016-5-13
 */
defined('BASEPATH') OR exit('No direct script access allowed');



// 是否开启cookie缓存账户和密码
$config['is_cache_uname_pwd'] = true;

// 加密KEY
$config['KEY'] = 'ecc7d2af36b29a22c95471d679e41bdf';

// 用户认证SESSION标记
$config['USER_AUTH_KEY'] = 'authId';

// 超管
$config['ADMIN'] = array(
	'yangf@songplay.cn',
);

