<?php
namespace myclass;
use think\Loader;
use think\Db;

class CMBHttp{
//    e0764aaf-cdc7-4fe1-95bc-9405ee0826bf
//    dd17c7ca-561b-4598-a92c-4a2a8b79c7d3
	public function __construct(){
		$this->HttpServer="http://192.168.3.8:8080";
		$this->XML="";
	}
	
	public function XML($data){
		$this->XML=$data;
	}
	
	public function fetch(){
		//print_r($this->XML);
		$return=$this->vpost($this->HttpServer,$this->XML);
// 		$return = mb_convert_encoding($return, 'utf-8','GBK');
        return $return;
		print_r($return);
	}

	/*	发送http post请求	*/
	public function vpost($url,$data){ // 模拟提交数据函数
		$curl = curl_init(); // 启动一个CURL会话
		curl_setopt($curl, CURLOPT_ACCEPT_ENCODING, "gzip,deflate");
		curl_setopt($curl, CURLOPT_URL,$url); // 要访问的地址
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
		//curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
		//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
		curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
		curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
		curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		$tmpInfo = curl_exec($curl); // 执行操作
		if (curl_errno($curl)) {
			return  ['code'=>1,'errorMsg'=>curl_error($curl)];//捕抓异常
		}
		curl_close($curl); // 关闭CURL会话
		
		return $tmpInfo; // 返回数据
	}
	
}