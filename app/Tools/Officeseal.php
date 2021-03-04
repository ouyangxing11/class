<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2020/3/24
 * Time: 9:49
 */

namespace myclass;


class Officeseal
{
    protected $appcode = 'e1c857ef97364c87bd5b735bc94f50d0';
    protected $host = "https://stamp.market.alicloudapi.com";
    protected $path = "/api/predict/ocr_official_seal";

    //公章识别
    public function image_search($img_path = '') {
        $result = $this->vpost('','POST',$img_path);
        $start = strpos($result,'{');
        $res = json_decode(substr($result,$start),true);
        return $res;
    }

    public function vpost($querys='',$method='GET',$img_path='') {
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $this->appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type".":"."application/json; charset=UTF-8");
        $querys = "";
        $bodys = "{\"image\":\"$img_path\"}";

        $url = $this->host . $this->path;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$".$this->host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
        return curl_exec($curl);
    }


    //文字识别
    public function word_search($url = '', $param = '') {
        if (empty($url) || empty($param)) {
            return false;
        }

        $postUrl = $url;
        $curlPost = $param;
        $curl = curl_init();//初始化curl
        curl_setopt($curl, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($curl, CURLOPT_HEADER, 0);//设置header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($curl);//运行curl
        curl_close($curl);

        return $data;
    }


    //获得文字识别token
    public function get_word_token() {
        $url = 'https://aip.baidubce.com/oauth/2.0/token';
        $post_data['grant_type']       = 'client_credentials';
        $post_data['client_id']      = 'IzfdCRRzaNU7oGu33CHynlir';
        $post_data['client_secret'] = 'HKHdUM3tVr4jTKNtymz6DNqVRKclIK7t';
        $o = "";
        foreach ( $post_data as $k => $v )
        {
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $post_data = substr($o,0,-1);
        $res = vpost($url,$post_data);
        return $res;
    }

    //测试文字
    public function recognize_text($img) {
        $token_arr = $this->get_word_token();
        $token_arr = json_decode($token_arr,true);
        $token = "{$token_arr['access_token']}";
        $url = 'https://aip.baidubce.com/rest/2.0/ocr/v1/general_basic?access_token=' . $token;
//        $img = file_get_contents('[本地文件路径]');
//        $img = base64_encode($img);
        $bodys = array(
            'url' => $img
        );
        $res = vpost($url, $bodys);
        return $res;
    }

}