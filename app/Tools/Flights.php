<?php
namespace myclass;
class Flights {
	var $result; // 结果
	var $content; // 内容
	var $list; // 列表
	
	
	public function getDomain($url) {

		$result=$this->fetch($url);
		
		$myfile = fopen("log.txt", "w") or die("Unable to open file!");
		fwrite($myfile, $result);
		fclose($myfile);
		
		$result=$this->str_cut($result,'<ul class="result">','<div class="spread spread_test_height">');
		
		$url=$this->GetArray('<cite>','<\/cite>',$result);
		
		$domain=$this->isDomain($url);
		
		return $domain;
	}

	
	public function getWeblink($url){
		$curren_url="http://".$url;
		$result=$this->task($curren_url);
		$url=$this->GetArray('<a href="','"',$result);
		
		foreach($url as $k=>$v){
			$url[$k]=$this->rel2abs($v,$curren_url);
		}
		
		return $url;
	}
	
	
	public function removeHtml($string){
		
		
		$preg = "#<(style|script)[\s\S]*?<\/(style|script)>#i";
		$string = preg_replace($preg,"",$string,-1);    //第四个参数中的3表示替换3次，默认是-1，替换全部

		$string = strip_tags($string);
		$string = trim($string);
		$string = str_replace("\t","",$string);
		$string = str_replace("\r\n","",$string);
		$string = str_replace("\r","",$string);
		$string = str_replace("\n","",$string);
		$string = str_replace(" ","",$string);
		
		$myfile = fopen("log.txt", "w") or die("Unable to open file!");
		fwrite($myfile, $string);
		fclose($myfile);

		return trim($string);
	}
	
	
	
	function rel2abs($url, $base){
		$url_arr=parse_url($url);
		$base_arr=parse_url($base);
		
		$B_URL=$base_arr['scheme'] . '://' . $base_arr['host'] . (isset($base_arr['port']) ? ':' . $base_arr['port'] : '');
		if(isset($url_arr["scheme"])){
			if($url_arr["scheme"]=="javascript") return "no";
			if($url_arr["host"]==$base_arr["host"]){
				return $url;
			}else{
				return "no";
			}
		}
		if(substr($url,0,1)=="/")return $B_URL.$url;
		if(substr($url,0,3)=="../")return $B_URL."/".substr($url,3);
		if(substr($url,0,2)=="./")return $B_URL."/".substr($url,2);
		return $B_URL."/".$url;
	}


	function str_cut($str, $start, $end) {
		$content = strstr ( $str, $start );
		$content = substr ( $content, strlen ( $start ), strpos ( $content, $end ) - strlen ( $start ) );
		return $content;
	}
	
	public function GetArray($start,$end,$content){
		$preg="#".$start.'(.*?)'.$end."#";
		preg_match_all($preg,$content,$result);//php正则表达式
		$return=array_map('strtolower',$result[1]);
		$return=array_unique($return);
		return $return;		
	}
	
	
	public function saveData($db,$table,$data){
				
		$count = $db->count($table, ["domain" =>$data["domain"]]);

		if($count>0){
			$return=$db->update($table,$data,["domain" => $data["domain"]]);
		}else{
			$return=$db->insert($table,$data);
		}
		
	}
	
	
	public function isDomain($url_arr){
		$domain=[];
		
		foreach($url_arr as $k=>$v){
			$ext=["com","cn","net"];
			$arr=explode(".",$v);
			if($arr[0]=="www"){
				$domain[]=explode("/",$v)[0];
				continue;
			}
			if(in_array($arr[1],$ext)){
				$domain[]=explode("/",$v)[0];
				continue;
			}
		}
		
		return $domain;
	}

	public function getContact($content){

		//preg_match_all("#市(.*?)#",$content,$result["addr"]);//php正则表达式
		//print_r($content);
		//$content="aa 053-53552599 ";
		
		preg_match('#(\d{3,4})-(\d{4}-\d{3,4}|\d{7,8})#',$content,$result["tel"]); //固定电话
		preg_match('#((地址).*?.(室|号|<|电话|\s))#',$content,$result["addr"]); //固定电话
		
		preg_match_all("#(1-*[3-9]{1}[0-9]{9})#",$content,$result["mobile"],PREG_SET_ORDER);//php正则表达式
		preg_match_all("/([a-z0-9\-_\.]+@[a-z0-9]+\.[a-z0-9\-_\.]+)/", $content, $result["email"]);
		
		print_r($result);
		
		$data=[];
		foreach($result as $k=>$v){
			if(count($v)>0){
				if(isset($v[0][0])) $data[$k]=$v[0][0];
				if(isset($v[0])) if(strlen($data[$k])<10) $data[$k]=$v[0];
			}
		}
		return $data;
	}
	
	
	

	/**
	 * 返回html内容
	**/
	public function fetch($url) {
		
		$origin = $url; //目标网址
		$referer = $origin;
		$headers=$this->randIp($url);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36");//模拟浏览器类型
		curl_setopt($curl, CURLOPT_TIMEOUT, 150);         // 设置超时限制防止死循环
		curl_setopt($curl, CURLOPT_HEADER, 0);            // 显示返回的Header区域内容
  		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept-Encoding:gzip'));
  		curl_setopt($curl, CURLOPT_COOKIE,'__huid=11EQzQl%2FVOY5WLMZ3onpmEe72cst8rD9WSlCUjyzILgjo%3D; QiHooGUID=F553C16DD38C226A1CBD14368DE8DBA0.1577174638247; __guid=15484592.2265194484186796800.1577174643080.619; webp=1; dpr=1; screenw=1; gtHuid=1; stc_ls_sohome=RQzW2jYRKd!tTRXdM(WM; count=64; _S=r4uapcqsu1do82p8bf0d4nia06; opqopq=35cc8d4f8357c946880a4cc8d9c6f573.1577410100; erules=p2-11%7Cp1-54%7Cp4-13%7Cecl-5%7Cp3-8%7Ckd-1');
  		curl_setopt($curl, CURLOPT_ENCODING, "gzip");
  		//curl_setopt($curl,CURLOPT_REFERER,$url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);    // 获取的信息以文件流的形式返回
// 		curl_setopt($curl, CURLOPT_PROXY, "http://180.122.148.35:9999");
		$tmpInfo = curl_exec($curl);
		if (curl_errno($curl)) {
			print "Error: " . curl_error($curl);
		} else {
			curl_close($curl);
		}

		return $tmpInfo;
	}
	
	public function randIP($url){
		$ip_long = array(
				array('607649792', '608174079'), //36.56.0.0-36.63.255.255
				array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255
				array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
				array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
				array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
				array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
				array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
				array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
				array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
				array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255
		);
		$rand_key = mt_rand(0, 9);
		$ip= long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
		$headers['CLIENT-IP'] = $ip;
		$headers['X-FORWARDED-FOR'] = $ip;
		
		$headers['Accept'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3';
		$headers['Accept-Language'] = 'zh-CN,zh;q=0.9';
		$headers['Connection'] =  'keep-alive';
		$headers['Content-Type'] =  'application/x-www-form-urlencoded; charset=UTF-8';
		$headers['Origin'] =$url;
		$headers['Referer'] =$url;
		$headers['User-Agent']='Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36';
		$headers['X-Requested-With'] ='XMLHttpRequest';

		
	
		$headerArr = array();
		foreach( $headers as $n => $v ) {
			$headerArr[] = $n .':' . $v;
		}
		return $headerArr;
	}
	
	public function WordCount($seg_list=[],$top=50){
		$cw=[];
		$ct=[];
		$i=0;
		foreach($seg_list as $key=>$val){
			if(mb_strlen($val)>=2){
				if(!in_array($val,$cw)){
		
					$ct[$val]=1;
					$cw[]=$val;
				}else{
					try {
						$ct[$val]=$ct[$val]+1;
					} catch (Exception $e) {
						//print_r($val."EEEEEEEEEEEEEEEEEEEEE"."<br>");
					}
				}}
		}
			
		arsort($ct);
		$ct=array_slice($ct,0,$top);
		return $ct;
	}
	
	
	public function getFlight($Num="3u8787",$fdate="20200112"){
				
		$data=$this->fetch("http://www.variflight.com/flight/fnum/{$Num}.html?fdate={$fdate}&AE71649A58c77");
		$data=$this->str_cut($data,'<ul id="list">','</ul>');
        preg_match_all('/<li style=\"position: relative;\">(.*?)<\/li>/is',$data,$matches);

        $info = [];
        if (!empty($matches[1])) {
            foreach ($matches[1] as $k=>$v) {
                $temp_status = $this->str_cut($v,'<span class="w150 blu_cor">','</span>');
                $temp_status = empty($temp_status) ? $this->str_cut($v,'<span class="w150 gre_cor">','</span>') : $temp_status;  //状态
                $temp_url = $this->str_cut($v,'<a class="searchlist_innerli" href="','">');
                $temp_url = "http://www.variflight.com".$temp_url;  //抓取详情地址，拿到起始和到达的城市和日期
                $temp_data = $this->fetch($temp_url);
                $temp_start = $this->str_cut($temp_data,'<div class="f_title f_title_a">','</div>');
                preg_match('/<span class=\"date\">(.*?)<\/span>/is',$temp_start,$matches_start);
                $start_date = substr(trim($matches_start[1]),0,10);  //起始日期
                $temp_end = $this->str_cut($temp_data,'<div class="f_title f_title_c">','</div>');
                preg_match('/<span class=\"date\">(.*?)<\/span>/is',$temp_end,$matches_end);
                $end_date = substr(trim($matches_end[1]),0,10);  //结束日期


                $start_city = $this->str_cut($temp_data,'<div class="cir_l curr">','</div>');  //起始城市
                $end_city = $this->str_cut($temp_data,'<div class="cir_r" style="left:100%;">','</div>');  //到达城市

                $tktime = $this->str_cut($v,'<span class="w150" dplan="','">'); //起飞时间
                $artime = $this->str_cut($v,'<span class="w150" aplan="','">'); //到达时间

                //计算航班的飞行时长
                $start = strtotime($start_date.$tktime);
                $end = strtotime($end_date.$artime);
                $diff_time = $end-$start;
                $hour = floor($diff_time/3600);
                $minute = floor($diff_time%3600/60);
                $costime = $hour.'小时'.$minute.'分钟';

                $info[] = [
                    'flightno' => $Num,  //航号
                    'airlinecompany' => $this->str_cut($v,'align="','">'),  //航空公司
                    'tktime' => $tktime,  //起飞时间
                    'artime' => $artime,   //到达时间
                    'trip' =>  $this->GetArray('<span class="w150">','</span>',$v),    //出发地和目的地
                    'status' => $temp_status,
                    'url' => $this->str_cut($v,'<a class="searchlist_innerli" href="','">'),
//                    'ontimerate' => $this->str_cut($v,'<img src="','"/>'),    //准点率
                    'start_date' => $start_date,  //起始日期
                    'end_date' => $end_date,     //结束日期
                    'start_city' => trim(strip_tags($start_city)),  //起始城市
                    'end_city' => trim(strip_tags($end_city)),  //到达城市,去除标签和空格
                    'costtime' => $costime  //时长
                ];
            }
        }

		return $info;
		print_r($info);die;
		
		
		
		
		
		
		$data=$this->str_cut($data,'<div class="del_com">','<div id="footer">');
		$data=preg_replace('/\s+/','',$data);
		$info["Title"]=$this->str_cut($data,'航班详情">','</h1>');
		$info["FlightNo"]=$this->str_cut($data,'主飞航班：','</p>');
		$info["Mileage"]=$this->str_cut($data,'总里程：<span>','</span>');
		$info["Time"]=$this->str_cut($data,'全程时长：<span>','</span>');
		$info["Age"]=$this->str_cut($data,'机型/机龄：<span>','</span>');
		
		if($info["Title"]=="") return $this->getFlight();
		return $info;
	}

	//智行火车数据
	public function trains($from,$to,$date) {
	    $from = urlencode($from);
	    $to = urlencode($to);
	    $url = "https://www.suanya.cn/pages/trainList?fromCn=$from&toCn=$to&fromDate=$date";
        $data=$this->fetch("$url");
        $data=$this->str_cut($data,'<div class="lisBox" data-v-9079722a>','<div id="tranferList" data-v-9079722a></div>');
        preg_match_all('/<div data-v-9079722a><div class=\"tbody\" data-v-9079722a><div class=\"railway_list\" data-v-9079722a>(.*?)<\/div><\/div><\/div>/is',$data,$matches);
        $list = [];

        if (!empty($matches[1])) {
            foreach ($matches[1] as $k=>$v) {
                $list[$k]['trainno'] = $this->str_cut($v,'<div class="w1" data-v-9079722a><div data-v-9079722a><strong data-v-9079722a>',"</strong></div>");  //车次
                $time = $this->str_cut($v,'<div class="w2" data-v-9079722a>',"</div></div>");
                $time = array_values(array_filter(explode(' ',str_replace(array("\r\n", "\r", "\n"), "", strip_tags($time)))));
                $list[$k]['departuretime'] = $time[0];  //开始时间
                $list[$k]['arrivaltime'] = $time[1].' '.(isset($time[2]) ? $time[2] : ''); //结束时间

                $fromTo = $this->str_cut($v,'<div class="w3" data-v-9079722a>',"</div></div>");  //起始终点站
                $fromTo = explode(' ',strip_tags($fromTo));
                $list[$k]['station'] = $fromTo[1];  //起始站
                $list[$k]['endstation'] = $fromTo[3];    //终点站

                $fee = $this->str_cut($v,'<div class="w4" data-v-9079722a>','</div></div>');
                $list[$k]['costtime'] = trim(strip_tags($fee));  //用时

                $ticket = $this->str_cut($v,'<div class="w5" data-v-9079722a>','</div></div>');
//                $ticket = explode('|',str_replace(array("\r\n", "\r", "\n"), "|", strip_tags($ticket)));
                $ticket = explode('|',strip_tags(str_replace(array("\r\n", "\r", "\n","</b>"), "|", $ticket)));
                $list[$k]['ticket'] = "";
                for ($i = 0;$i < count($ticket); $i = $i+2) {
                    if (trim($ticket[$i])) {
                        $list[$k]['ticket'] .= trim($ticket[$i]).'<br/>';  //座位
                    }
                }

            }
        }
        return $list;
    }
	
}