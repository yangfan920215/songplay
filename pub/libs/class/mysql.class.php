<?php
/**
 * 数据库MySQL5驱动
 * @package class
 * @author kent.liu
 * @since 1.0.0
 * @version 1.0.0
 + build date 2012-5-17
 + support by kent.liu@139.com
 */

class mysql{
	private $sql;
	private $conn;
    
    /**
     * @param array $config 数据库配置，数组格式：array('host'=>ip, 'port'=>port, 'uid'=>dbuser, 'pwd'=>dbpass, 'db'=>dbname)
	 * @return void
	 * @since 1.0.0
     */         
    public function __construct($config) {
        $this->connect($config);
    }


	/**
	 * 连接数据库
	 * @param array $config 数据库配置，数组格式：array('host'=>ip, 'port'=>port, 'uid'=>dbuser, 'pwd'=>dbpass, 'db'=>dbname)
	 * @return void
	 * @since 1.0.0
	 */
	public function connect($config) {
		$conn = @mysqli_connect($config['host'], $config['uid'], $config['pwd'], $config['db'], $config['port']);
		
		if ($conn) {
			mysqli_set_charset($conn, str_replace('-', '', 'UTF-8'));
			$this->conn = $conn;
		}else{
			// 
			monitor($config['db'] . ' conn failed, please check the config(/conf/conf.php)');
			_exit($config['db'] . ' conn failed, please check the config(/conf/conf.php)');
		}
	}

	/**
	 * 执行存储过程
	 * 对于 SELECT 类型的存储过程，如果结果集为空，返回空数组
	 * 对于 INSERT, UPDATE, DELETE 类型的存储过程，执行成功返回 TRUE，执行失败返回 FALSE
	 + MYSQLI_ASSOC = 关联数组，常量的值为 1
	 + MYSQLI_NUM = 索引数组，常量的值为 2
	 + MYSQLI_BOTH = 关联索引数组，常量的值为 3
	 * @param string $procedure [数据库名.]存储过程名
	 * @param array $param 参数集，格式：array(array(var1, DIRECTION), array(var2, DIRECTION)...)，其中DIRECTION的取值：1=入参、2=入参和出参、4=出参，默认为入参
	 * @param int $type 结果集数组类型：1=关联、2=索引、3=索引和关联，默认值为关联数组
	 * @param int $dimension 结果集数组维数，默认值为 2 维，取值范围：0-3之间。其中0表示不返回结果集，只返回执行是否成功；1表示返回一维结果集，以此类推...
	 * @return mixed
	 * @since 1.0.1
	 * @example mysql.php mysql
	 */
	public function execute($procedure, &$param = array(), $dimension = 2, $type = 1) {
		$sql_inout = '';	//inout param sql
		$sql_out = '';		//output sql
		$arrout = array();	//output parameters list
		$result = array();	//return result sets
		$sql = 'CALL '.$procedure.'(';		//procedure sql
    
        if ($param && is_array($param)) {
            $str = '';
            $i = 0;
            foreach($param as $val) {
                $direction = isset($val[1]) ? intval($val[1]) : 1;              
                switch($direction) {
                    case 1: //input
                    	if (is_numeric($val[0])) {
							$str .= (','.$val[0]);
						} else {
							$str .= (',\''.addslashes($val[0]).'\'');
                        }
						break;
                    case 2:	//input and output
						$str .= ',@out'.$i;
						$sql_inout .= ('SET @out'.$i.'=\''.$val[0].'\'');
						$sql_out .= (',@out'.$i);
						$arrout[] = $i;
						break;
					case 4:	//output
						$str .= ',@out'.$i;
						$sql_out .= (',@out'.$i);
						$arrout[] = $i;
						break;
                }
                $i++;
            }
            $str = substr($str, 1);
			$sql_out = substr($sql_out, 1);
			$sql .= $str;       
        }
        $sql .= ')'; 
                 
		if ($sql_out) {
			$sql_out = 'SELECT '.$sql_out;
			$this->sql = $sql_inout;
			$stmt = mysqli_query($this->conn, $sql_inout);
			if ($stmt === FALSE) {
				// 链接失败,写入日志
				monitor($this->get_error());
				return false;
				// trigger_error('300|'.$this->get_error());
			}
		}
		$this->sql = $sql;
		$stmt = mysqli_query($this->conn, $sql);
		if ($stmt === FALSE) {
			// 执行错误,写入日志
			monitor($this->get_error());
			return false;
			// trigger_error('301|'.$this->get_error());
		}

		switch ($dimension) {
			case 0:
				$result = true;
				if (is_object($stmt))
					$this->clear_more_result();
				break;
			case 1:
				if (is_object($stmt)) {
					$result = mysqli_fetch_array($stmt, $type);
					$this->clear_more_result();
				}
				break;
			case 2:
				if (is_object($stmt)) {
					$result = mysqli_fetch_all($stmt, $type);
					$this->clear_more_result();
				}
				break;
			case 3:
				if (is_object($stmt))
					$result = $this->fetch_all_result($stmt, $type);
				break;
		}

		//destory mysqli_result object
		if (is_object($stmt)) mysqli_free_result($stmt);

		//change output param memory's value
		if ($sql_out) {
			$stmt = mysqli_query($this->conn, $sql_out);
			if ($stmt === FALSE) {
				trigger_error('302|'.$this->get_error());
			} else {
				$output = mysqli_fetch_array($stmt, MYSQLI_NUM);
				if ($output) {
					for ($i=0; $i<count($arrout); $i++) {
						$param[$arrout[$i]][0] = $output[$i];
					}
				}
			}
			mysqli_free_result($stmt);
		}
		return $result;
	}


	/**
	 * 获取多个结果集组成三维数组
	 * @param resource $stmt 资源对象(mysqli_result)
	 * @param int $type 结果集类型
	 * @return array
	 * @since 1.0.0
	 */
	private function fetch_all_result($stmt, $type) {
		$result = array();
		$result[] = mysqli_fetch_all($stmt, $type);
		while (mysqli_more_results($this->conn)) {
			mysqli_next_result($this->conn);
			if ($stmt2 = mysqli_store_result($this->conn)) {
				$row = mysqli_fetch_all($stmt2, $type);
				array_push($result, $row);
				mysqli_free_result($stmt2);
			}
		}
		return $result;
	}


	/**
	 * 清空多余结果集
	 * @return void
	 * @since 1.0.0
	 */
	private function clear_more_result() {
		while (mysqli_more_results($this->conn)) {
			mysqli_next_result($this->conn);
			if($rs = mysqli_store_result($this->conn)) {
				mysqli_free_result($rs);
			}
		}
	}


	/**
	 * 获取错误信息
	 * @return string
	 * @since 1.0.0
	 */
	private function get_error() {
		$errnum = mysqli_errno($this->conn);
		$errmsg = mysqli_error($this->conn);
		$result = $errnum.$errmsg;
		return $result;
	}


	/**
	 * 返回执行的SQL语句
	 * 此方法无法获取存储过程中执行的SQL
	 * @return string
	 * @since 1.0.0
	 */
	public function get_sql() {
		return $this->sql;
	}


	/**
	 * 返回数据库的版本信息
	 * @return string
	 * @since 1.0.0
	 */
	public function get_version() {
		return mysqli_get_server_info($this->conn);
	}


	public function __destruct() {
		if ($this->conn)
			mysqli_close($this->conn);
	}
}
?>