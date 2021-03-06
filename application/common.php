<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * [getListStocks 得到股票列表]
 * @return [string] [返回一个字符串]
 */
function getStock($key,$prefix='',$suffix=''){
	if(is_array($key)){
		$k = '';
		for ($i=0; $i < count($key); $i++) { 
			$k .= completion($key[$i],$prefix,$suffix).",";
		}
		$key = $k;
	}else{
		$key = completion($key,$prefix,$suffix);
	}
	$url = 'http://hq.sinajs.cn/list='.$key;
	$tmp = sendGetCurl($url);
	$tmpArr = explode(';',$tmp);
	array_pop($tmpArr);
	
	for ($i=0; $i < count($tmpArr); $i++) { 
		$tArr = explode('=',$tmpArr[$i]);
		if($tArr[1] == ""){
			exit(JN(['status'=>'failed','data'=>'股票不存在，或者网络错误']));
		}

		if($prefix == ''){
			$keys[] = mb_substr($tArr[0],12);
		}else{
			$keys[] = mb_substr($tArr[0],14);
		}
		
		$values[] = explode(',',$tArr[1]);
	}

	if(is_array($keys[0][0])){
		return JN(['status'=>'failed','data'=>'接口内部错误，请检查',]);
	}

	if(count($keys) != count($values)){
		return JN(['status'=>'failed','data'=>'数组的长度不相等',]);
	}
	return array_combine($keys,$values);
}

/**
 * [sendGetCurl 发送get method curl 请求]
 * @return [sting] [字符串类型]
 */
function sendGetCurl($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper("get")); 
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($ch);
	//去掉字符串中所有的空格
	$rule=array(" ","　","\t","\n","\r",'"');
	$take=array("","","","","","");
	$output = iconv('GBK', 'UTF-8', str_replace($rule,$take,$output));
	curl_close($ch);
	return  $output;
}

/**
 * [JN json返回格式]
 */
function JN($val){
	header("Content-type: application/json");
	return json_encode($val);
}

/**
 * [completion 补全股票代码]前缀后缀不能同时存在
 * @return [string] [返回字符串]
 */
function completion($str,$prefix='',$suffix=''){
	if(strlen($str) < 6){
        exit(JN(['status'=>'failed','data'=>'股票代码长度不足6位']));
    }
	if($prefix !='' && $prefix != "s_"){
		$prefix='s_';
	}
	if($suffix !='' && $suffix != "_i"){
		$suffix='_i';
	}
	$tmp = $str{0};
	if($tmp === '6'){
		return $prefix."sh".$str.$suffix;
	}else if($tmp === '0' || $tmp === '3'){
		return $prefix."sz".$str.$suffix;
	}else{
		exit(JN(['status'=>'failed','data'=>'股票不存在']));
	}
}

/**
 * [filterSpecialCharacter 过滤掉特殊字符]
 * @return [string] [返回字符串]
 */
function filterSpecialCharacter($str){
	//需要过滤字符的数组
	$vowels = ['"',"'",'#'];
	//需要替换成的数组
	$yummy = [''];
	return str_replace($vowels,'',$str);
}

/**
 * [redis 链接redis]
 * @return [boolean] [description]
 */
function redis(){
	$redis = new \Redis();
    $redis->connect('127.0.0.1', 6379);
    return $redis;
}

/**
 * [getWeekFirstDay Get the first day of the week]
 * @return [date] [日期]
 */
function getWeekFirstDay(){
	//当前日期  
	$sdefaultDate = date("Y-m-d");  
	//$first =1 表示每周星期一为开始日期 0表示每周日为开始日期  
	$first=1;  
	//获取当前周的第几天 周日是 0 周一到周六是 1 - 6  
	$w=date('w',strtotime($sdefaultDate));  
	//获取本周开始日期，如果$w是0，则表示周日，减去 6 天  
	$week_start=date('Y-m-d',strtotime("$sdefaultDate -".($w ? $w - $first : 6).' days')); 
	return $week_start;
}
//生成随机字符串
function getRandChar($length){
   $str = null;
   $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
   $max = strlen($strPol)-1;
   for($i=0;$i<$length;$i++){
    $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
   }
   return $str;
  }
// 定义一个函数获取客户端IP地址
function getIP(){
    if (getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if(getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if(getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else $ip = "Unknow IP";
    return $ip;
}
function is_https()
    {
        if ( ! empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        {
            return TRUE;
        }
        elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        {
            return TRUE;
        }
        elseif ( ! empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off')
        {
            return TRUE;
        }
  
        return FALSE;
    }



/**
 * [cookieDecrypt cookie解密]
 * @return [type] [description]
 */
function cookieDecrypt($data){
    /* 打开加密算法和模式 */
    $td = mcrypt_module_open('rijndael-256', '', 'ofb', '');
    $ks = mcrypt_enc_get_key_size($td);
    /* 创建密钥 */
    $key = substr(md5('sjqcj.com'), 0, $ks);
    /* 获取初始向量，并且检测密钥长度。*/
    $data = base64_decode($data);
    $iv_dec = substr($data, 0, $ks);
    $data_dec = substr($data, $ks);
    /* 初始化解密模块 */
    mcrypt_generic_init($td, $key, $iv_dec);
    /* 解密数据 */
    $decrypted = mdecrypt_generic($td, $data_dec);
    //结束解密，执行清理工作，并且关闭模块 
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    return trim($decrypted);
}


/**
 * [encryption cookie加密]
 * @return [type] [description]
 */
function cookieEncrypt($data){
    /* 打开加密算法和模式 */
    $td = mcrypt_module_open('rijndael-256', '', 'ofb', '');
    /* 创建初始向量，并且检测密钥长度。 
     * Windows 平台请使用 MCRYPT_RAND。 */
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_RANDOM);
    $ks = mcrypt_enc_get_key_size($td);
    /* 创建密钥 */
    $key = substr(md5('sjqcj.com'), 0, $ks);
    /* 初始化加密 */
    mcrypt_generic_init($td, $key, $iv);

    /* 加密数据 */
    $encrypted = mcrypt_generic($td, $data);

    $encrypted = $iv.$encrypted;

    $encrypted_base64 = base64_encode($encrypted);

    /* 结束加密，执行清理工作 */
    mcrypt_generic_deinit($td);
    return $encrypted_base64;
}