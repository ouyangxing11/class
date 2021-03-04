<?php
namespace myclass;


class Train
{
//    protected $appcode = "95e970fd9cd742ed8f8a83eb32071c79";
    protected $appcode = "a6783a63875c4f10b4fec084ccafd0a9";//公司的
    private $errcode = [
        "201"=>"车次为空",
        "202"=>"起始站或到达站为空",
        "203"=>"没有信息"
    ];
    //车次查询
    public function line_search($line){
        $host = "https://jisutrain.market.alicloudapi.com";
        $path = "/train/line";
        $querys = "trainno=G34";
        $querys = "trainno={$line}";
//        $bodys = "";
        $res = $this->vpost($host,$path,$querys);
        $res = json_decode($res,true);
        if($res['msg']=='ok' && $res['status']==0){
            $data['data'] = $res['result'];
            $data['code'] = 0;
        }else{
            $data = ["code"=>1,"data"=>[],"msg"=>$this->errcode[$res['status']]];
        }
        return $data;
    }

    //站站查询
    public function station_search($start,$end,$ishigh,$date){
        $host = "https://jisutrain.market.alicloudapi.com";
        $path = "/train/station2s";
        $querys = "date={$date}&end={$end}&start={$start}";
        $res = $this->vpost($host,$path,$querys);
        $res = json_decode($res,true);
        if($res['msg']=='ok' && $res['status']==0){
            $data['data'] = $res['result'];
            $data['code'] = 0;
        }else{
            $data = ["code"=>1,"data"=>[],"msg"=>$this->errcode[$res['status']]];
        }
        return $data;
    }

    //余票查询
    public function rest_search($start,$end,$date){
        $host = "https://jisutrain.market.alicloudapi.com";
        $path = "/train/ticket";
//        $querys = "date=2015-10-20&end={$end}&start={$start}";
        $querys = "date={$date}&end={$end}&start={$start}";
//        $bodys = "";
        $res = $this->vpost($host,$path,$querys);
        $res = json_decode($res,true);
        if($res['msg']=='ok' && $res['status']==0){
            $data['data'] = $res['result'];
            $data['code'] = 0;
        }else{
            $data = ["code"=>1,"data"=>[],"msg"=>$this->errcode[$res['status']]];
        }
        return $data;
    }


    public function vpost($host,$path,$querys){
        $method = "GET";
        $appcode = $this->appcode;
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        $url = $host . $path . "?" . $querys;
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
        $res = curl_exec($curl);
        return $res;
    }

}