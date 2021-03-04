<?php
namespace myclass;


class Airplane
{
//    protected $appCode = "95e970fd9cd742ed8f8a83eb32071c79";
    protected $appCode = "a6783a63875c4f10b4fec084ccafd0a9";//公司的
    private $errcode = [
        "101"=>"接口参数错误!",
        "102"=>"接口错误!",
    ];
    protected $city = [
            ['name'=>'阿勒泰','code'=>'AAT'],
            ['name'=>'兴义','code'=>'ACX'],
            ['name'=>'百色','code'=>'AEB'],
            ['name'=>'红原','code'=>'AHJ'],
            ['name'=>'安康','code'=>'AKA'],
            ['name'=>'阿克苏','code'=>'AKU'],
            ['name'=>'鞍山','code'=>'AOG'],
            ['name'=>'安庆','code'=>'AQC'],
            ['name'=>'安顺','code'=>'AVA'],
            ['name'=>'阿拉善左旗','code'=>'AXF'],
            ['name'=>'琼海','code'=>'BAR'],
            ['name'=>'包头','code'=>'BAV'],
            ['name'=>'毕节','code'=>'BFJ'],
            ['name'=>'秦皇岛','code'=>'BPE'],
            ['name'=>'博乐','code'=>'BPL'],
            ['name'=>'北海','code'=>'BHY'],
            ['name'=>'昌都','code'=>'BPX'],
            ['name'=>'北京','code'=>'BJS'],
            ['name'=>'巴中','code'=>'BZX'],
            ['name'=>'保山','code'=>'BSD'],
            ['name'=>'广州','code'=>'CAN'],
            ['name'=>'承德','code'=>'CDE'],
            ['name'=>'常德','code'=>'CGD'],
            ['name'=>'郑州','code'=>'CGO'],
            ['name'=>'长春','code'=>'CGQ'],
            ['name'=>'朝阳','code'=>'CHG'],
            ['name'=>'酒泉','code'=>'CHW'],
            ['name'=>'赤峰','code'=>'CIF'],
            ['name'=>'长治','code'=>'CIH'],
            ['name'=>'重庆','code'=>'CKG'],
            ['name'=>'长海','code'=>'CNI'],
            ['name'=>'长沙','code'=>'CSX'],
            ['name'=>'沧源','code'=>'CWJ'],
            ['name'=>'成都','code'=>'CTU'],
            ['name'=>'常州','code'=>'CZX'],
            ['name'=>'大同','code'=>'DAT'],
            ['name'=>'达县','code'=>'DAX'],
            ['name'=>'白城','code'=>'DBC'],
            ['name'=>'稻城','code'=>'DCY'],
            ['name'=>'丹东','code'=>'DDG'],
            ['name'=>'迪庆','code'=>'DIG'],
            ['name'=>'大连','code'=>'DLC'],
            ['name'=>'大理','code'=>'DlU'],
            ['name'=>'敦煌','code'=>'DNH'],
            ['name'=>'东营','code'=>'DOY'],
            ['name'=>'大庸','code'=>'DYG'],
            ['name'=>'大庆','code'=>'DQA'],
            ['name'=>'鄂尔多斯','code'=>'DSN'],
            ['name'=>'张家界','code'=>'DYG'],
            ['name'=>'恩施','code'=>'ENH'],
            ['name'=>'延安','code'=>'ENY'],
            ['name'=>'二连浩特','code'=>'ERL'],
            ['name'=>'福州','code'=>'FOC'],
            ['name'=>'阜阳','code'=>'FUG'],
            ['name'=>'佛山','code'=>'FUO'],
            ['name'=>'抚远','code'=>'FYJ'],
            ['name'=>'富蕴','code'=>'FYN'],
            ['name'=>'广汉','code'=>'GHN'],
            ['name'=>'果洛','code'=>'GMQ'],
            ['name'=>'格尔木','code'=>'GOQ'],
            ['name'=>'夏河','code'=>'GXH'],
            ['name'=>'广元','code'=>'GYS'],
            ['name'=>'固原','code'=>'GYU'],
            ['name'=>'海口','code'=>'HAK'],
            ['name'=>'祁连','code'=>'HBQ'],
            ['name'=>'河池','code'=>'HCJ'],
            ['name'=>'邯郸','code'=>'HDG'],
            ['name'=>'黑河','code'=>'HEK'],
            ['name'=>'呼和浩特','code'=>'HET'],
            ['name'=>'合肥','code'=>'HFE'],
            ['name'=>'杭州','code'=>'HGH'],
            ['name'=>'淮安','code'=>'HIA'],
            ['name'=>'芷江','code'=>'HJA'],
            ['name'=>'海拉尔','code'=>'HLD'],
            ['name'=>'乌兰浩特','code'=>'HLH'],
            ['name'=>'哈密','code'=>'HMI'],
            ['name'=>'衡阳','code'=>'HNY'],
            ['name'=>'神农架','code'=>'HPG'],
            ['name'=>'哈尔滨','code'=>'HRB'],
            ['name'=>'普陀山','code'=>'HSN'],
            ['name'=>'和田','code'=>'HTN'],
            ['name'=>'花土沟','code'=>'HTT'],
            ['name'=>'霍林郭勒','code'=>'HUO'],
            ['name'=>'惠州','code'=>'HUZ'],
            ['name'=>'德令哈','code'=>'HXD'],
            ['name'=>'台州','code'=>'HYN'],
            ['name'=>'汉中','code'=>'HZG'],
            ['name'=>'黎平','code'=>'HZH'],
            ['name'=>'银川','code'=>'INC'],
            ['name'=>'且末','code'=>'IQM'],
            ['name'=>'庆阳','code'=>'IQN'],
            ['name'=>'咸宁','code'=>'IUO'],
            ['name'=>'景德镇','code'=>'JDZ'],
            ['name'=>'加格达奇','code'=>'JGD'],
            ['name'=>'嘉峪关','code'=>'JGN'],
            ['name'=>'井冈山','code'=>'JGS'],
            ['name'=>'西双版纳','code'=>'JHG'],
            ['name'=>'金昌','code'=>'JIC'],
            ['name'=>'吉林','code'=>'JIL'],
            ['name'=>'黔江','code'=>'JIQ'],
            ['name'=>'九江','code'=>'JIU'],
            ['name'=>'石狮','code'=>'JJN'],
            ['name'=>'晋江','code'=>'JJN'],
            ['name'=>'澜沧','code'=>'JMG'],
            ['name'=>'佳木斯','code'=>'JMU'],
            ['name'=>'济宁','code'=>'JNG'],
            ['name'=>'锦州','code'=>'JNC'],
            ['name'=>'建三江','code'=>'JSJ'],
            ['name'=>'池州','code'=>'JUH'],
            ['name'=>'衢州','code'=>'JUZ'],
            ['name'=>'鸡西','code'=>'JXA'],
            ['name'=>'九寨沟','code'=>'JZH'],
            ['name'=>'库车','code'=>'KCA'],
            ['name'=>'康定','code'=>'KGT'],
            ['name'=>'喀什','code'=>'KHG'],
            ['name'=>'南昌','code'=>'KHN'],
            ['name'=>'凯里','code'=>'KJH'],
            ['name'=>'布尔津','code'=>'HJI'],
            ['name'=>'昆明','code'=>'KMG'],
            ['name'=>'吉安','code'=>'KNC'],
            ['name'=>'赣州','code'=>'KOW'],
            ['name'=>'库尔勒','code'=>'KRL'],
            ['name'=>'克拉玛依','code'=>'KRY'],
            ['name'=>'贵阳','code'=>'KWE'],
            ['name'=>'桂林','code'=>'KWL'],
            ['name'=>'龙岩','code'=>'LCX'],
            ['name'=>'伊春','code'=>'LDS'],
            ['name'=>'临汾','code'=>'LFQ'],
            ['name'=>'光化','code'=>'LHK'],
            ['name'=>'兰州','code'=>'LHW'],
            ['name'=>'梁平','code'=>'LIA'],
            ['name'=>'丽江','code'=>'LJG'],
            ['name'=>'荔波','code'=>'LLB'],
            ['name'=>'永州','code'=>'LLF'],
            ['name'=>'吕梁','code'=>'LLV'],
            ['name'=>'临沧','code'=>'LNG'],
            ['name'=>'六盘水','code'=>'LPF'],
            ['name'=>'芒市','code'=>'LUF'],
            ['name'=>'庐山','code'=>'LUZ'],
            ['name'=>'拉萨','code'=>'LXA'],
            ['name'=>'林西','code'=>'LXI'],
            ['name'=>'洛阳','code'=>'LYA'],
            ['name'=>'连云港','code'=>'LYG'],
            ['name'=>'临沂','code'=>'LYI'],
            ['name'=>'兰州东','code'=>'LZD'],
            ['name'=>'柳州','code'=>'LZH'],
            ['name'=>'南竿','code'=>'LZN'],
            ['name'=>'泸州','code'=>'LZO'],
            ['name'=>'林芝','code'=>'LZY'],
            ['name'=>'牡丹江','code'=>'MOG'],
            ['name'=>'绵阳','code'=>'MIG'],
            ['name'=>'梅县','code'=>'MXZ'],
            ['name'=>'南充','code'=>'NAO'],
            ['name'=>'白山','code'=>'NBS'],
            ['name'=>'齐齐哈尔','code'=>'NDG'],
            ['name'=>'宁波','code'=>'NGB'],
            ['name'=>'阿里','code'=>'NGQ'],
            ['name'=>'南京','code'=>'NKG'],
            ['name'=>'宁蒗','code'=>'NLH'],
            ['name'=>'新源','code'=>'NLT'],
            ['name'=>'南宁','code'=>'NNG'],
            ['name'=>'南阳','code'=>'NNY'],
            ['name'=>'南通','code'=>'NTG'],
            ['name'=>'满洲里','code'=>'NZH'],
            ['name'=>'扎兰屯','code'=>'NZL'],
            ['name'=>'漠河','code'=>'OHE'],
//            ['name'=>'首都机场(北京)','code'=>'PEK'],
//            ['name'=>'上海浦东','code'=>'PVG'],
            ['name'=>'攀枝花','code'=>'PZI'],
            ['name'=>'阿拉善右旗','code'=>'PHT'],
            ['name'=>'日照','code'=>'RIZ'],
            ['name'=>'日喀则','code'=>'RKZ'],
            ['name'=>'巴彦淖尔','code'=>'RLK'],
            ['name'=>'若羌','code'=>'RQA'],
            ['name'=>'上海','code'=>'SHA'],
            ['name'=>'沈阳','code'=>'SHE'],
            ['name'=>'石河子','code'=>'SHF'],
            ['name'=>'山海关','code'=>'SHP'],
            ['name'=>'秦皇岛','code'=>'SHP'],
            ['name'=>'沙市','code'=>'SHS'],
            ['name'=>'石家庄','code'=>'SJW'],
            ['name'=>'上饶','code'=>'SQD'],
            ['name'=>'三明','code'=>'SQJ'],
            ['name'=>'西安','code'=>'SIA'],
            ['name'=>'汕头','code'=>'SWA'],
            ['name'=>'思茅','code'=>'SYM'],
            ['name'=>'三亚','code'=>'SYX'],
            ['name'=>'深圳','code'=>'SZX'],
            ['name'=>'青岛','code'=>'TAO'],
            ['name'=>'塔城','code'=>'TCG'],
            ['name'=>'腾冲','code'=>'TCZ'],
            ['name'=>'铜仁','code'=>'TEN'],
            ['name'=>'辽通','code'=>'TGO'],
            ['name'=>'天水','code'=>'THQ'],
            ['name'=>'吐鲁番','code'=>'TLQ'],
            ['name'=>'济南','code'=>'TNA'],
            ['name'=>'通化','code'=>'TNH'],
            ['name'=>'天津','code'=>'TSN'],
            ['name'=>'唐山','code'=>'TVS'],
            ['name'=>'图木舒克','code'=>'TWC'],
            ['name'=>'黄山','code'=>'TXN'],
            ['name'=>'屯溪','code'=>'TXN'],
            ['name'=>'太原','code'=>'TYN'],
            ['name'=>'乌兰察布','code'=>'UCB'],
            ['name'=>'乌鲁木齐','code'=>'URC'],
            ['name'=>'榆林','code'=>'UYN'],
            ['name'=>'十堰','code'=>'WDS'],
            ['name'=>'潍坊','code'=>'WEF'],
            ['name'=>'威海','code'=>'WEH'],
            ['name'=>'邵阳','code'=>'WGN'],
            ['name'=>'遵义茅台','code'=>'WMT'],
            ['name'=>'文山','code'=>'WNH'],
            ['name'=>'温州','code'=>'WNZ'],
            ['name'=>'乌海','code'=>'WUA'],
            ['name'=>'武汉','code'=>'WUH'],
            ['name'=>'武夷山','code'=>'WUS'],
            ['name'=>'忻州','code'=>'WUT'],
            ['name'=>'无锡','code'=>'WUX'],
            ['name'=>'梧州','code'=>'WUZ'],
            ['name'=>'万州','code'=>'WXN'],
            ['name'=>'信阳','code'=>'XAI'],
            ['name'=>'襄阳','code'=>'XFN'],
            ['name'=>'西昌','code'=>'XIC'],
            ['name'=>'兴城','code'=>'XEN'],
            ['name'=>'襄樊','code'=>'XFN'],
            ['name'=>'西昌','code'=>'XIC'],
            ['name'=>'锡林浩特','code'=>'XIL'],
            ['name'=>'兴宁','code'=>'XIN'],
            ['name'=>'咸阳','code'=>'XIY'],
            ['name'=>'厦门','code'=>'XMN'],
            ['name'=>'西宁','code'=>'XNN'],
            ['name'=>'徐州','code'=>'XUZ'],
            ['name'=>'宜宾','code'=>'YBP'],
            ['name'=>'运城','code'=>'YCU'],
            ['name'=>'宜春','code'=>'YIC'],
            ['name'=>'阿尔山','code'=>'YIE'],
            ['name'=>'宜昌','code'=>'YIH'],
            ['name'=>'伊宁','code'=>'YIN'],
            ['name'=>'义乌','code'=>'YIW'],
            ['name'=>'营口','code'=>'YKH'],
            ['name'=>'依兰','code'=>'YLN'],
            ['name'=>'延吉','code'=>'YNJ'],
            ['name'=>'烟台','code'=>'YNT'],
            ['name'=>'盐城','code'=>'YNG'],
            ['name'=>'松原','code'=>'YSQ'],
            ['name'=>'泰州','code'=>'YTY'],
            ['name'=>'玉树','code'=>'YUS'],
            ['name'=>'岳阳','code'=>'YYA'],
            ['name'=>'张掖','code'=>'YZA'],
            ['name'=>'昭通','code'=>'ZAT'],
            ['name'=>'中山','code'=>'ZGN'],
            ['name'=>'中卫','code'=>'ZHY'],
//            ['name'=>'中川机场(兰州)','code'=>'ZGC'],
            ['name'=>'张家口','code'=>'ZQZ'],
            ['name'=>'湛江','code'=>'ZHA'],
            ['name'=>'珠海','code'=>'ZUH'],
            ['name'=>'遵义','code'=>'ZYI'],
        ];


    /**
     * 航班站点查询
     */
    public function station_search($arrive_code,$leave_code,$date){
//        $url= "https://market.aliyun.com/products/57002002/cmapi028585.html?spm=5176.730005.productlist.d_cmapi028585.18093524BDqQF0&innerSource=search_%E8%88%AA%E7%8F%AD%20%E6%90%BA%E7%A8%8B#sku=yuncode2258500000";
        $host = "http://airinfo.market.alicloudapi.com";
        $path = "/airInfos";
        $method = "POST";
        $appcode = $this->appCode;
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
        $querys = "";
//        $bodys = "arrive_code=arrive_code&leave_code=leave_code&query_date=query_date";
        $bodys = "arrive_code={$arrive_code}&leave_code={$leave_code}&query_date={$date}";
        $url = $host . $path;
        $res = $this->vpost($method,$url,$headers,$host,$bodys);
        $res = json_decode($res,true);
        if($res['errCode']==0){
            $result = $res['flightInfos'];
            foreach ($result as $k=>$v){
                $result[$k] = key_strtolower($v);
            }
            $data['data'] = $result;
            $data['code'] = 0;
        }else{
            $data = ["code"=>1,"data"=>[],"msg"=>$this->errcode[$res['status']]];
        }
        return $data;
    }


    //站站查询--新的更全面的
    public function new_station_search($arrive_code,$leave_code,$date){
//        $host = "http://airinfo.market.alicloudapi.com";
        $host = "https://flight.market.alicloudapi.com";
        $path = "/flight/query";
        $method = "GET";
        $appcode = "95e970fd9cd742ed8f8a83eb32071c79";
//        $appcode = "你自己的AppCode";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        $querys = "city={$leave_code}&date={$date}&endcity={$arrive_code}";
        $bodys = "";
        $url = $host . $path . "?" . $querys;
        $res = $this->vpost($method,$url,$headers,$host,$bodys);
        $res = json_decode($res,true);
        if($res['msg']==0 && $res['status']==0){
            $result = $res['result']['list'];
            foreach ($result as $k=>$v){
                $result[$k] = key_strtolower($v);
            }
            $data['data'] = $result;
            $data['code'] = 0;
        }else{
            $data = ["code"=>1,"data"=>[],"msg"=>$this->errcode[$res['status']]];
        }
        return $data;
    }


    /**
     * 航班号查询
     */
    public function line_search($flightNo,$date){
        //$url = "https://market.aliyun.com/products/57124001/cmapi032412.html?spm=5176.730005.productlist.d_cmapi032412.18093524BDqQF0&innerSource=search_%E8%88%AA%E7%8F%AD%20%E6%90%BA%E7%A8%8B#sku=yuncode2641200006";
        $host = "http://plane.market.alicloudapi.com";
        $path = "/ai_market/ai_airplane/get_airplane_info";
        $method = "GET";
        //阿里云APPCODE
        $appcode = $this->appCode;
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        array_push($headers, "Content-Type:application/json; charset=utf-8");
        //参数配置
        //日期，如：20190208
        $DATE = "20190208";
        //航班编号，如：MU5128
        $FLIGHT_ID = "MU5128";

        $querys = "DATE=".$date.
            "&FLIGHT_ID=".$flightNo;
        $bodys = "";
        $url = $host . $path . "?" . $querys;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $data = curl_exec($curl);
        if($data){
            $data = json_decode($data,true);
        }else{
            $data = [];
        }
        return $data;
    }

    public function city(){
      return $this->city;
    }

    public function vpost($method,$url,$headers,$host,$bodys){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        if($method=="POST"){
            curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
        }
        $res = curl_exec($curl);
        return $res;
    }

}