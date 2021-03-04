<?php
namespace App\Http\Controllers\Common;


class FsoketOpenController
{
    public function socket(){
        $srv_ip = 'm.black-unique.com';//你的目标服务地址.
        $srv_port = 80;//端口
        $url = 'https://m.black-unique.com/gas_station/my_list'; //接收你post的URL具体地址
//        $url = 'https://api.ejiayou.com/v1/phpUtils/encrypt.do'; //接收你post的URL具体地址
        $fp = '';
        $errno = 0;//错误处理
        $errstr = '';//错误处理
        $timeout = 30;//多久没有连上就中断
        $post_str = "page=1&page_num=20&oil_code=95&sort_type=1&latitude=22.51079&longitude=113.92077";//要提交的内容.
//        $post_str = "content=81";//要提交的内容.
        //打开网络的 Socket 链接。
        $fp = fsockopen($srv_ip,$srv_port,$errno,$errstr,$timeout);
        if (!$fp){
            echo('fp fail');
        }
        $content_length = strlen($post_str);
        $post_header = "GET / HTTP/1.1\r\n";
//        $post_header .= "Accept:*/*\r\n";
        $post_header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $post_header .= "User-Agent: PostmanRuntime/7.26.8\r\n";
//        $post_header .= "Referer: ".$url."\r\n";
        $post_header .= "Host: ".$srv_ip."\r\n";
        $post_header .= "Content-Length: ".$content_length."\r\n";
        $post_header .= "Connection: Close\r\n\r\n";
        $post_header .= $post_str."\r\n\r\n";
        fwrite($fp,$post_header);

        $inheader = 1;
        while(!feof($fp)){//测试文件指针是否到了文件结束的位置
            $line = fgets($fp,128);
            //去掉请求包的头信息
//             echo $line;

            if ($inheader && ($line == "\n" || $line == "\r\n")) {
                $inheader = 0;
            }
            if ($inheader == 0) {
                echo $line;
            }
        }
        fclose($fp);
        unset ($line);
    }


    public function test(){
        $fp = fsockopen("www.baidu.com", 80, $errno, $errstr, 30);
        if (!$fp) {
            echo "$errstr ($errno)<br />\n";
        } else {
            $out = "GET / HTTP/1.1\r\n";
            $out .= "Host: www.baidu.com\r\n";
            $out .= "Connection: Close\r\n\r\n";
            fwrite($fp, $out);
            while (!feof($fp)) {
                echo fgets($fp, 128);
            }
            fclose($fp);
        }
    }

}