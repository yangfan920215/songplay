<?php
/**
 * 文件上传处理类
 * @author yangf@songplay.cn
 * @date 2016-7-28
 */

/**
 * 如果是使用form表单提交遵循如下
 * 
 * 1.必须设置form标签属性enctype="multipart/form-data"
 * 2.必须有一个隐藏的表示上传文件最大长度的表单域,例:<input type="hidden" name="MAX_FILE_SIZE" value="1000000" /> 代指最大文件大小为1MB左右
 */

/**
 * php.ini中关于文件上传的指令设置
 * 
 * file_uploads: 控制是否允许HTTP方式的文件上传.允许值为On或Off,默认On
 * upload_tmp_dir: 指定上传的文件临时保存目录.如果没有设置,将使用系统默认值(默认为空),可能是/temp
 * upload_max_filesize: 控制上传文件大小,默认为2M,如果文件大于该值,PHP会创建一个文件大小为0的占位符文件
 * post_max_size: 控制PHP可以接受的,通过POST方法上传数据最大值,该值必须大于upload_max_filesize,因为它是所有POST数据的大小,包含了upload_max_filesize
 */

/*
 * 注意事项
 * 
 * 1.上传文件在脚本执行完毕后没有移动、复制或更改文件名称,该文件会被删除
 * 2.需要处理的数据全部会保存在$_FILES中
 * 3.若移动文件到某目录下,该目录已有该命名文件,原文件会被覆盖
 * 4.移动文件最好移动到非web服务器目录
 */

/**
 * 使用方式
 * $fileUp = fileUp::init(array(array('dzpk', 'application/octet-stream'), array('desc', 'text/plain')), '/data/web/html/www/web/down/mge/');
 * 
 * 获取上传信息:echo $fileUp->getUpStatus();
 */


/**
 * 
 */

class fileUp{
	
	/**
	 * 保存上传文件信息
	 * @var unknown
	 */
	private $resInfo;
	
	
	private $up_dir;
	
	/**
	 * 单例模式
	 */
	private function __construct($file, $up_dir){
		$this->up_dir = $up_dir;
		
		// 支持以数组方式接收
		if (is_array($file)) {
			foreach ($file as $value) {
				if (isset($value[0]) && isset($value[1])) {
					$this->parse_file($value[0], $value[1]);
				}
			}
		}
	}
	
	/**
	 * 获取单例
	 * @param unknown $file
	 * @return fileUp
	 */
	public static function init(array $file, $up_dir){
		return isset(self::$obj) ? self::$obj : new fileUp($file, $up_dir);
	}
	
	/**
	 * 获取上传成功失败信息
	 * @return Ambigous <boolean, string, unknown>
	 */
	public function getUpStatus(){
		$errmsg = '';
		
		foreach ($this->resInfo as $value) {
			$errmsg .= isset($value['errorMsg']) ? $value['errorMsg'] : '';
		}
		
		return $errmsg === '' ? true : $errmsg;
	}
	
	
	/**
	 * 检查上传文件信息是否异常
	 * @param unknown $fileName
	 * @return boolean
	 */
	private function parse_file($fileName, $fileType){
		if (!isset($_FILES[$fileName])) {
			// 某上传文件没有被找到
			$this->resInfo[$fileName]['errorMsg'] =  $_FILES[$fileName]['name'] . ' can\'t be find!\n';
			return false;
		}
		
		// 出现未知错误,文件信息未被记录
		if (!isset($_FILES[$fileName]['name']) || !isset($_FILES[$fileName]['type']) || !isset($_FILES[$fileName]['tmp_name']) || !isset($_FILES[$fileName]['size'])) {
			// 某上传文件没有被找到
			$this->resInfo[$fileName]['errorMsg'] =  $_FILES[$fileName]['name'] . ' error: fileInfo can\'t be read!\n';
			return false;
		}
		
		// 出现异常错误
		if (isset($_FILES[$fileName]['error']) && $_FILES[$fileName]['error'] !== 0) {

			// 异常信息
			switch ($_FILES[$fileName]['error']){
		      case 1: 
		      	$this->resInfo[$fileName]['errorMsg'] = $_FILES[$fileName]['name'] . ':上传文件过大,上传失败.';
		      break;
		      case 2: 
		      	$this->resInfo[$fileName]['errorMsg'] = $_FILES[$fileName]['name'] . ':上传文件超出表单元素约定最大值,上传失败.';
		      break;
		      case 3: 
		      	$this->resInfo[$fileName]['errorMsg'] = $_FILES[$fileName]['name'] . ':文件只被部分上传,上传失败.';
		      break;
		      case 4: 
		      	$this->resInfo[$fileName]['errorMsg'] = $_FILES[$fileName]['name'] . ':没有任何上传文件,上传失败.';
		      break;
		   	  case 6:   
		   		$this->resInfo[$fileName]['errorMsg'] = $_FILES[$fileName]['name'] . ':没有指定临时目录,上传失败.';
		      break;
		   	  case 7:  
		   	  	$this->resInfo[$fileName]['errorMsg'] = $_FILES[$fileName]['name'] . ' 提交文件无法被写入硬盘,上传失败.';
		      break;
		      default:
		      	$this->resInfo[$fileName]['errorMsg'] = $_FILES[$fileName]['name'] . ' 未知异常,上传失败(' . $_FILES[$fileName]['error'] . ')';
		      break;
		    }
			return false;
		}
		
		// 文件类型不匹配
		if ($fileType != $_FILES[$fileName]['type']) {
			$this->resInfo[$fileName]['errorMsg'] = $_FILES[$fileName]['name'] . ':文件类型与配置不一致,上传中止;';
			return false;
		}
		
		// 检查上传路径是否为默认路径
		if (!is_uploaded_file($_FILES[$fileName]['tmp_name'])) {
			$this->resInfo[$fileName]['errorMsg'] = $_FILES[$fileName]['name'] . ':可能是文件攻击,上传中止;';
			return false;
		}
		
		// 移动文件到指定目录
		if (!move_uploaded_file($_FILES[$fileName]['tmp_name'], $this->up_dir . $_FILES[$fileName]['name'])) {
			$this->resInfo[$fileName]['errorMsg'] = $_FILES[$fileName]['name'] . ':移动文件失败,上传中止;';
			return false;
		}
		
		// 最好清除掉html或php标记 strip_tags()
		
		// 记录信息
		$this->resInfo[$fileName]['name'] = $_FILES[$fileName]['name'];
		$this->resInfo[$fileName]['tmp_name'] = $_FILES[$fileName]['tmp_name'];
		$this->resInfo[$fileName]['size'] = $_FILES[$fileName]['size'];
		$this->resInfo[$fileName]['type'] = $_FILES[$fileName]['type'];
	}

}