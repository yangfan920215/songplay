<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 版本控制
 * @author phoenix
 *
 */

class Ver extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		
		// 加载类库
		$this->load->helper(array('form'));
		$this->load->library('zip');
	}
	
	public function index(){
		$this->parser->parse('technology/pver/ver.html', $this->data);
	}
	
	/**
	 * 版本提交
	 */
	public function submit(){
		// 加载上传配置
		$config['upload_path'] = './Application/third_party/Ver';
		$config['allowed_types'] = '*';
		$config['overwrite'] = true;
		$config['max_size'] = '8192';
		$this->load->library('upload', $config);
		
		// 接收资源信息;
		if (!$this->upload->do_upload('userfile')){
			// 接收失败,打印错误信息
			$error = array('error' => $this->upload->display_errors());
			$this->data['result'] =  var_export($error);
		}else{
			// 上传成功,开始解压文件,$data为上传包信息
			$data = array('upload_data' => $this->upload->data());
			
			// 解析数据命名,看包是否已经指定渠道
			$pag_name_arr = explode('_', $data['upload_data']['raw_name']);
			$pag_name_count = count($pag_name_arr);

			if (in_array($pag_name_count, array(2, 3))) {
				$gName = $pag_name_arr[0];	// 游戏名
				
				if ($pag_name_count == 2) {
					$cName = 'game';	// 渠道文件名
					$fileName = $pag_name_arr[1];	// 文件名
				}else{
					$cName = $pag_name_arr[1];
					$fileName = $pag_name_arr[2];
				}
				
				// 路径参数
				$verDown = $this->config->item('down_file').$gName.'/android/'.$cName.'/';	// 最终zip文件存放地址
				$filePver =  $data['upload_data']['file_path'].'pver/'.$gName.'/'.$cName;	// 解压文件保存地址
				$fileTemp = $data['upload_data']['file_path'].$fileName;	// 临时文件存储
				
				// 若该文件的保存和下载路径不存在,则创建之
				if ($this->mkDirs($filePver) && $this->mkDirs($verDown)) {
					
					// 判断文件是否已经被解压过
					if (!file_exists($filePver.'/'.$fileName)) {
							
						// 文件解压
						$zip = new ZipArchive;
						if ($zip->open($data['upload_data']['full_path']) === TRUE) {
							// 解压压缩包
							$zip->extractTo($data['upload_data']['file_path'].$fileName);
							
							// 进行覆盖操作
							if ($dh  =  opendir($filePver)){
								while (( $fileList  =  readdir ( $dh )) !==  false ) {
										
									// 屏蔽系统文件
									if ($fileList == '.' || $fileList == '..') {
										continue;
									}
									
									// 读取上传文件解压后的文件夹
									foreach ($this->get_dir_files($fileTemp) as $value) {
										// 替换文件名
										$listFileName = str_replace($fileName, $fileList, $value);
										
										// 替换
										$listFileName = str_replace('Ver', 'Ver/pver/'.$gName.'/'.$cName, $listFileName);
										
										// 若文件路径存在,则替换文件,若不存在,尝试完善目录
										if (file_exists($listFileName)) {
											unlink($listFileName);
										}else {
											$this->mkDirs(dirname($listFileName));
										}
										
										// 拷贝文件
										copy($value, $listFileName);
									}
									
									// 查看本文件的压缩包是否已经存在
									if (file_exists($verDown.$fileList.'.zip')) {
										unlink($verDown.$fileList.'.zip');
									}
									
									// 生成新的压缩包
									exec('cd '.$filePver.'/'.$fileList.';zip -r '.$verDown.$fileList.'.zip *');
									
									@$zip->close(); //关闭处理的zip文件
								}
								
							}
							// 将上传文件的解压文件夹迁移到存储目录
							rename($fileTemp, $filePver.'/'.$fileName);
							// 将上传文件重命名并迁移到下载目录
							rename($data['upload_data']['full_path'], $verDown.$fileName.'.zip');
							
							$this->data['result'] = '遍历成功！下载URL为http://' . $this->config->item('down_url') .'/'.$gName.'/android/'.$cName.'/'.$fileName.'.zip';
						}else {
							$this->data['result'] = '解压失败！';
						}
					}else{
						$this->data['result'] = '该压缩包已经被上传过,请检查文件命名！';
					}
				}else{
					$this->data['result'] = '解压文件保存地址和最终zip文件存在地址创建或授权失败！';
				}
			}else {
				$this->data['result'] = '文件命名不符合约定！';
			}
		}
		echo $this->data['result'];
	}
	
	private function coverDir($dir){
		// 定义初始目录
		
	}
	
	/**
	 * 迭代创建目录
	 * @param unknown $dir
	 * @return boolean
	 */
	private function mkDirs($dir){
		if(!is_dir($dir)){
			if(!$this->mkDirs(dirname($dir))){
				return false;
			}
			if(!mkdir($dir, 0777)){
				return false;
			}
		}
		return true;
	}
	
	/**
	 * 迭代获取全部文件
	 * @param unknown $dir
	 * @return multitype:unknown |Ambigous <multitype:string , multitype:>
	 */
	private function get_dir_files($dir){
		if (is_file($dir)) {
			return array($dir);
		}
		$files = array();
		if (is_dir($dir) && ($dir_p = opendir($dir))) {
			$ds = DIRECTORY_SEPARATOR;
			while (($filename = readdir($dir_p)) !== false) {
				if ($filename=='.' || $filename=='..'){ 
					continue; 
			}
				$filetype = filetype($dir.$ds.$filename);
				if ($filetype == 'dir') {
					$files = array_merge($files, $this->get_dir_files($dir.$ds.$filename));
				} elseif ($filetype == 'file') {
					$files[] = $dir.$ds.$filename;
				}
			}
			closedir($dir_p);
		}
		return $files;
	}
	
	/**
	 * 创建压缩文件
	 * @param unknown $path
	 * @param unknown $zip
	 * @param unknown $files
	 */
	private function addFileToZip($path, $zip, $files) {
		//打开当前文件夹由$path指定。
		$handler = opendir($path);
		
		// !== false 是为了防止死循环
		while (($filename = readdir($handler)) !== false) {
			if ($filename != "." && $filename != "..") {
				if (is_dir($path . "/" . $filename)) {
					$this->addFileToZip($path . "/" . $filename, $zip, $files);
				} else { //将文件加入zip对象
					$filess = substr(strstr(strstr($path, $files), '/'), 1);
					$zip->addFile($path . "/" . $filename, $filess.'/'.$filename);
				}
			}
		}
		@closedir($path);
	}
}