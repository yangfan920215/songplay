<?php
/**
 * xml操作类
 * @package class
 * @author jiangxinyu
 * @since 1.0.1
 * @version 1.0.1
 + build date 2013-10-5
 + support by jxy918@163.com
 */

class xml {
	/**
	 * 解析XML文档并生成数组后返回（标准解析）
	 * @param string $xml xml字符串内容
	 * @return array
	 * @since 1.0.1 
	 */
	public static function xml2array($xml = '') {
		return self::gamexml2array($xml);
	}

	/**
	 * php数组转成xml(标准解析）
	 * @param array $data  数组
	 * @param string $rootNodeName 自定义根节点名称
	 * @param SimpleXMLElement $xml 点单xml对象
	 * @return string XML
	 * @since 1.0.1     
	 */
	public static function array2xml($xmlary = array(), $root = 'data'){	
		$xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>'."\n<$root>\n";
        $xml .= xml::array2nodexml($xmlary);
        $xml .="</$root>";
        return $xml;	
	}

	/**
	 * 游戏列表xml转化成php数组
	 * @param string $xml xml字串
	 * @return array
	 * @since 1.0.1           
	 */
    public static function gamexml2array($xml = '') {
        $opened = $array = array(); 
        $opened[1] = 0;          
        $xml_parser = xml_parser_create();
        xml_parser_set_option( $xml_parser, XML_OPTION_CASE_FOLDING, 0 );
        xml_parser_set_option( $xml_parser, XML_OPTION_SKIP_WHITE, 1 );
        xml_parse_into_struct( $xml_parser, $xml, $xmlarray );
        xml_parser_free( $xml_parser );        
        $array = array_shift($xmlarray); 
        unset($array["level"]); 
        unset($array["type"]); 
        $arrsize = count($xmlarray);  
        for($j = 0; $j < $arrsize; $j++){ 
            $val = $xmlarray[$j];          
            switch($val["type"]){ 
                case "open": 
                    $opened[$val["level"]] = 0; 
                case "complete": 
                    $index = ""; 
                    for($i = 1; $i < ($val["level"]); $i++){ 
                        $index .= "[" . $opened[$i] . "]"; 
                    }
                    $path = explode('][', substr($index, 1, -1)); 
                    $value = &$array;
                    foreach($path as $segment){ 
                        $value = &$value['child'][$segment];
                    }
                    //增加value为空的闭合项
                    if(!isset($val['value']) && $val['type'] == 'complete') {
                    	$val['value'] = '';
                    }
                    $value = $val; 
                    unset($value["level"]); 
                    unset($value["type"]); 
                    if($val["type"] == "complete"){ 
                        $opened[$val["level"]-1]++;
                    } 
                break; 
                case "close":
                    if(isset($opened[$val["level"]-1])) {
                        $opened[$val["level"]-1]++;
                    } 
                    unset($opened[$val["level"]]); 
                break; 
            } 
        }
        return $array; 
    }

    /**
     * 游戏列表数组转化成相应的xml(非标准，游戏配置xml调用)
     * @param array $xmlary 游戏列表数组
     * @param string $root 根节点
     * @return string
     * @since 1.0.1
     */
    public static function array2gamexml($xmlary = array(), $root = 'data', $rootattr = array()){
		if(is_array($rootattr) && count($rootattr)) {
			$str = '';
			foreach($rootattr as $k=>$v) {
				$str .= $k.'="'.$v.'" ';	
			}
			$xml = "<$root $str>\n";
		} else {
			$xml = "<$root>\n";
		}
        $xml .= xml::array2nodexml($xmlary);
        $xml .="</$root>";
        return $xml;    
    }
	
    /**
     * 游戏列表数组子节点递归转化成相应的xml(非标准，游戏配置xml调用)
     * @param array $xmlary 游戏列表数组
     * @return string
     * @since 1.0.1
     */
    public static function array2nodexml($xmlary = array()) {
        $o = $tag =  '';
        foreach($xmlary as $va) {
        	if(is_array($va)) {
		        foreach($va as $val) {
		        	//解析tag
		            if(isset($val['tag'])) {
		                $o .= '<'.$val['tag'];
		                $tag = $val['tag']; 
		            }		            
		            if($tag) {
			             //解析attr
			              if(isset($val['attributes']) && is_array($val['attributes'])) {
			                  foreach($val['attributes'] as $k=>$v) {
			                      $o .= ' '.$k.'="'.$v.'"';
			                  }
			              }			              
			              if(isset($val['child']) && is_array($val['child']) ){
				              	//递归调用子节点
				              	$o .= ">\n";
				              	$o .= self::array2nodexml(array($val['child']));
				              	$o .= "</{$tag}>\n";
			              }
			              if(isset($val['value'])) {
			                  $o .= ">{$val['value']}</{$tag}>\n";
			              } elseif(!isset($val['child'])) {
			                    $o .= " />\n";
			              }
		            }
		        }
        	}
        }
        return $o;
    }
}
?>