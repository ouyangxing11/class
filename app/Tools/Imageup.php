<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/20
 * Time: 11:24
 */

namespace myclass;

if(is_dir('../../vendor/autoload.php')){
    require_once   '../../vendor/autoload.php';
}
use Qcloud\Cos\Client;
use think\Db;
use think\Request;
use tools\Help;

class Imageup
{

    public function __construct($file_path)
    {
//        $this->path = $file_path."/".date("Y")."/".date("m");
        $this->path = $file_path;
    }
    /**
     * @return \Guzzle\Http\Url|string|array
     * 普通上传图片
     */
    public function upload_img($size = 0, $rename = 0, $is_source = 0)
    {
        // 获取表单上传文件 例如上传了001.jpg
//        $teacherid = USER_ID;
        $file = request()->file('file');
//print_r($file);die;
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $size = 3;
            if ($is_source) {
                $size = 100;
            }
            $bef_info=$file->getInfo();

            if($bef_info['size']>$size*1024*1024){
                return ['code'=>1,"msg"=>"图片大小超过".$size."M","url"=>"","size"=>$bef_info['size']];
            }
            $tmp_name_arr=explode(".",$bef_info["name"]);
            $tmp_image_arr=explode("/",$bef_info["type"]);

            if(in_array(end($tmp_image_arr),['jpg','jpeg','png','gif'])){
                $ext = '.'.end($tmp_image_arr);
                $info_ext = end($tmp_image_arr);
            }else{
                $ext = ".".end($tmp_name_arr);
                $info_ext = end($tmp_name_arr);
            }
//            $path = "uploads/teacher/".$teacherid."/cases/";
            $path = $this->path;
            if ($rename != 0) {
                $new_file= $path."/".$bef_info["name"];
            } else {
                $new_file= $path."/".md5(uniqid(md5(microtime(true)),true)).$ext;
            }
            $tmp_file=$bef_info["tmp_name"];
            $cosClient = new Client(config("qcos"));
            try {
                $result = $cosClient->Upload(
                    $bucket = "hs",
                    $key = $new_file,
                    $body = fopen($tmp_file, 'rb')
                );
                $signedUrl = $cosClient->getObjectUrl($bucket, $new_file);

                if($signedUrl) {
                    if(substr($signedUrl,0,5)!="https"){
                        $signedUrl = str_replace("http","https",$signedUrl);
                    }

                    $sourceType = [
                        '1' => [ //文档
                            'doc',
                            'docx',
                            'ppt',
                            'pptx',
                            'txt',
                            'pdf',
                            'epub',
                        ],
                        '2' => [ //音频
                            'mp3'
                        ],
                        '3' => [ //视频
                            'mp4'
                        ],
                        '4' => [ //图片
                            'jpg',
                            'jpeg',
                            'png '
                        ],
                    ];
                    $source = false;
                    foreach ($sourceType as $k => $item) {
                        if (in_array($info_ext, $item)) {
                            $source = $k;
                        }
                    }
                    if ($is_source) {
                        if ($source == false) return 0;
                    }
                    $fileinfo = [
                        'name' => pathinfo($bef_info['name'], PATHINFO_FILENAME),
                        'type' => $bef_info['type'],
                        'format_size' => Help::getFileSize($bef_info['size']),
                        'size' => $bef_info['size'],
                        'ext' => $info_ext,
                        'source_type' => $source,
                        'url' => $signedUrl,
                    ];

                    return ['code'=>0,"msg"=>"上传成功","url"=>$signedUrl, 'file_info' => $fileinfo];
//                    return $signedUrl;
                }
            } catch (\Exception $e) {
                return ['code'=>1,"msg"=>"$e\n"] ;
            }
        }else{
            // 上传失败获取错误信息
            return ['code'=>1,"msg"=>'上传失败'] ;
        }

    }


    public function audio(){
        $file = request()->file('file');
        $bef_info=$file->getInfo();
        $url = $bef_info['tmp_name'];
        $tmp_name_arr=explode(".",$bef_info["name"]);
        $ext = end($tmp_name_arr);
        $path = $this->path;
        $new_file= $path."/".md5(uniqid(md5(microtime(true)),true)).".$ext";
//        echo $new_file;die;
        $cosClient = new Client(config("qcos"));
        try {
            $result = $cosClient->Upload(
                $bucket = "hs",
                $key = $new_file,
                $body = fopen($url, 'rb')
            );
            $signedUrl = $cosClient->getObjectUrl($bucket, $new_file);
            if($signedUrl) {
                if(substr($signedUrl,0,5)!="https"){
                    $signedUrl = str_replace("http","https",$signedUrl);
                }
                return ['code'=>0,"msg"=>"上传成功","url"=>$signedUrl];
//                    return $signedUrl;
            }
        } catch (\Exception $e) {
            return ['code'=>1,"msg"=>"$e\n"] ;
        }

    }


    public function upload_doc($file,$itemno,$type){
            //$server = "http://192.168.3.208:7070/";
            //$server="http://test.myeln.com.cn/";
//            $server=__PUBLIC__ ."/";
            $url = $file;
            $path = $this->path."/".$itemno.".$type";
//            $newpath = ".".$path;
            $cosClient = new Client(config("qcos"));
            try {
                $result = $cosClient->Upload(
                    $bucket = "hs",
                    $key = $path,
                    $body = fopen($url, 'rb')
                );
                $signedUrl = $cosClient->getObjectUrl($bucket, $path);
                if($signedUrl) {
                    return ['code'=>0,"msg"=>"上传成功","url"=>$signedUrl];
//                    return $signedUrl;
                }
            } catch (\Exception $e) {
                return ['code'=>1,"msg"=>"$e\n"] ;
            }

    }


    public function ppt_to_img($file){
        $url = $file;
        $path = $this->path."/".md5(uniqid(md5(microtime(true)),true)).".png";
//            $newpath = ".".$path;
        $cosClient = new Client(config("qcos"));
        try {
            $result = $cosClient->Upload(
                $bucket = "hs",
                $key = $path,
                $body = fopen($url, 'rb')
            );
            $signedUrl = $cosClient->getObjectUrl($bucket, $path);
            if($signedUrl) {
                if(substr($signedUrl,0,5)!="https"){
                    $signedUrl = str_replace("http","https",$signedUrl);
                }
//                return ['code'=>0,"msg"=>"上传成功","url"=>$signedUrl];
                    return $signedUrl;
            }
        } catch (\Exception $e) {
            return "";
        }

    }


    /**
     * @return \Guzzle\Http\Url|string|array
     * 批量上传图片
     */
    public function upload_all_img($path='',$size = 0){
        // 获取表单上传文件 例如上传了001.jpg
//        $teacherid = USER_ID;
        $file = request()->file('file');
        $returnUrl = [];
        $errorName  = [];
        $successName = [];
        $bef_info=$file->getInfo();
        if($size && $bef_info['size']>$size*1024*1024){
            return ['code'=>1,"msg"=>"图片大小超过".$size."M","url"=>"","size"=>$bef_info['size']];
        }
        //$tmp_name_arr=explode(".",$bef_info["name"]);
        //$ext=".".end($tmp_name_arr);
        $path = $path?$path:$this->path;
        $new_file= $path."/".$bef_info["name"];
        $tmp_file=$bef_info["tmp_name"];
        $cosClient = new Client(config("qcos"));
        try {
            $result = $cosClient->Upload(
                $bucket = "hs",
                $key = $new_file,
                $body = fopen($tmp_file, 'rb')
            );
            $signedUrl = $cosClient->getObjectUrl($bucket, $new_file);
            if($signedUrl) {
                return $signedUrl;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }
    /**
     * @param string $page  小程序页面路径
     * @param string $scene  参数值
     * @param bool $ishyline 是否透明 false不透明
     * @return bool|\Guzzle\Http\Url|string  返回图片地址
     * 生产带参数小程序码
     */
    public function wxqrcode($page,$scene,$ishyline=false,$appid){
        $config = new \miniprogram\Miniprogram($appid);
        $token = $config->get_access_token();
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=$token";
        $data = [
            "scene"=>"$scene",
            "page"=>"$page",
            "is_hyaline"=>$ishyline,
            "width"=>430
        ];
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $res = https_post($url,$data);
        file_put_contents("qrcode.png", $res);
        $base64_image ="data:image/jpeg;base64,".base64_encode( $res );
//        $path = "static/miaoke/qrcode/";
        $img = $this->base64_image_save($base64_image);
        return $img;
    }


    /**
     * @param $page   页面路径
     * @param $scene  参数
     * @param bool $ishyline 是否透明
     * @param $appid
     * @param $headimg 用户头像
     * @return array
     */
    public function wxqrcode_head($page,$scene,$ishyline=false,$appid,$headimg){
        $config = new \miniprogram\Miniprogram($appid);
        $token = $config->get_access_token();
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=$token";
        $data = [
            "scene"=>"$scene",
            "page"=>"$page",
            "is_hyaline"=>$ishyline,
            "width"=>300
        ];
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $qrcode = https_post($url,$data);
        $headimg = file_get_contents($headimg);
        $logo = $this->yuanImg($headimg);

        //二维码与头像结合
        $sharePic = $this->qrcodeWithLogo($qrcode,$logo);
//        print_r($res);die;
        file_put_contents("qrcode.png", $sharePic);
        $base64_image ="data:image/png;base64,".base64_encode( $sharePic );
//        $path = "static/miaoke/qrcode/";
        $img = $this->base64_image_save($base64_image);
        return $img;
    }


    public function base64_image_save($base64_image_content,$size = 3){
//        echo $base64_image_content;die;
//        $base64 = str_replace('data:image/jpeg;base64,', '' ,$base64_image_content);
        $base64 = substr($base64_image_content,strpos($base64_image_content,"base64,")+7);
        $base64 = str_replace('=', '',$base64);
        $img_len = strlen($base64);
        $file_size = $img_len - ($img_len/8)*2;
        $file_size = number_format(($file_size/1024/1024),2);
        if($file_size > $size){
            $arr = array(
                'code' => 1,
                'msg' => '请上传'.$size.'M以下的图片'
            );
            return $arr;
        }

        //匹配出图片的格式
//        if (preg_match('/^data:(.*?)\/(.*?);base64,(.*?)$/', $base64_image_content, $result)){
        if (preg_match('/^data:(.*?)\/(.*?);base64,/', $base64_image_content, $result)){
//        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
            $type = $result[2];

            switch ($type)
            {
                case "x-zip-compressed":
                    $type="zip";
                    break;
                case "msword":
                    $type="doc";
                    break;
                case "vnd.openxmlformats-officedocument.wordprocessingml.document":
                    $type="docx";
                    break;
                case "vnd.openxmlformats-officedocument.spreadsheetml.sheet":
                    $type="xlsx";
                    break;
            }

            $new_file = $this->path."/".md5(uniqid(md5(microtime(true)),true)).".{$type}";
            $cosClient = new Client(config("qcos"));
            try {
                try {
                    $result = $cosClient->Upload(
                        $bucket = "hs",
                        $key = $new_file,
                        $body = base64_decode($base64)
//                        $body = base64_decode($result[3])
                    );
                    $signedUrl = $cosClient->getObjectUrl($bucket, $new_file);

//                    return $signedUrl;
                    return ['code'=>0,"msg"=>"上传成功","url"=>$signedUrl];
                } catch (\Exception $e) {
                    return ['code'=>1,"msg"=>$e->getMessage()];
                }
            } catch (\Exception $e) {
                return ['code'=>1,"msg"=>'上传失败'];
            }
        }else{
            return ['code'=>1,"msg"=>'请上传'.$size.'M以下的图片'];
        }
    }


    /**
     * 在二维码的中间区域镶嵌图片
     * @param $QR 二维码数据流。比如file_get_contents(imageurl)返回的东东,或者微信给返回的东东
     * @param $logo 中间显示图片的数据流。比如file_get_contents(imageurl)返回的东东
     * @return  返回图片数据流
     */
    public function qrcodeWithLogo($QR,$logo){
        $QR   = imagecreatefromstring ($QR);
        $logo = imagecreatefromstring ($logo);
        $QR_width    = imagesx ( $QR );//二维码图片宽度
        $QR_height   = imagesy ( $QR );//二维码图片高度
        $logo_width  = imagesx ( $logo );//logo图片宽度
        $logo_height = imagesy ( $logo );//logo图片高度
        $logo_qr_width  = $QR_width / 2.2;//组合之后logo的宽度(占二维码的1/2.2)
        $scale  = $logo_width / $logo_qr_width;//logo的宽度缩放比(本身宽度/组合后的宽度)
        $logo_qr_height = $logo_height / $scale;//组合之后logo的高度
        $from_width = ($QR_width - $logo_qr_width) / 2;//组合之后logo左上角所在坐标点
        /**
         * 重新组合图片并调整大小
         * imagecopyresampled() 将一幅图像(源图象)中的一块正方形区域拷贝到另一个图像中
         */
        imagecopyresampled ( $QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height );
        /**
         * 如果想要直接输出图片，应该先设header。header("Content-Type: image/png; charset=utf-8");
         * 并且去掉缓存区函数
         */
        //获取输出缓存，否则imagepng会把图片输出到浏览器
        ob_start();
        imagepng ( $QR );
        imagedestroy($QR);
        imagedestroy($logo);
        $contents =  ob_get_contents();
        ob_end_clean();
        return $contents;
    }
    /**
     * 剪切图片为圆形
     * @param  $picture 图片数据流 比如file_get_contents(imageurl)返回的东东
     * @return 图片数据流
     */
     public function yuanImg($picture) {
        $src_img = imagecreatefromstring($picture);
        $w   = imagesx($src_img);
        $h   = imagesy($src_img);
        $w   = min($w, $h);
        $h   = $w;
        $img = imagecreatetruecolor($w, $h);
        //这一句一定要有
        imagesavealpha($img, true);
        //拾取一个完全透明的颜色,最后一个参数127为全透明
        $bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
        imagefill($img, 0, 0, $bg);
        $r   = $w / 2; //圆半径
        $y_x = $r; //圆心X坐标
        $y_y = $r; //圆心Y坐标
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgbColor = imagecolorat($src_img, $x, $y);
                if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
            }
        }
        /**
         * 如果想要直接输出图片，应该先设header。header("Content-Type: image/png; charset=utf-8");
         * 并且去掉缓存区函数
         */
        //获取输出缓存，否则imagepng会把图片输出到浏览器
        ob_start();
        imagepng ( $img );
        imagedestroy($img);
        $contents =  ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    public function uploadFile($file){
        $path = $this->path;
        $new_file= $path."/qrcode.png";
        $cosClient = new Client(config("qcos"));
        try{
            $result = $cosClient->Upload(
                $bucket = "hs",
                $key = $new_file,
                $body = $file
            );
            $signedUrl = $cosClient->getObjectUrl($bucket, $new_file);
            if($signedUrl) {
                return $signedUrl;
            }
        } catch (\Exception $e) {
        }
        return '';
     }
    public function uploadAll($path='',$size = 0){
        // 获取表单上传文件 例如上传了001.jpg
        $files = request()->file('file');
        $cosClient = new Client(config("qcos"));
        $url = [];
        $msg = '图片';
        foreach ($files as $k=>$file){
            $bef_info=$file->getInfo();
            if($size && $bef_info['size']>$size*1024*1024){
                $msg .= $bef_info["name"].' ';
                continue;
            }
            $tmp_name_arr=explode(".",$bef_info["name"]);
            $ext=".".end($tmp_name_arr);
            $path = $path?$path:$this->path;
            $time = time();
            $name = "{$time}{$k}".$ext;
            $new_file= $path."/".$name;
            $tmp_file=$bef_info["tmp_name"];
            try {
                $result = $cosClient->Upload(
                    $bucket = "hs",
                    $key = $new_file,
                    $body = fopen($tmp_file, 'rb')
                );
                $signedUrl = $cosClient->getObjectUrl($bucket, $new_file);
                if($signedUrl) {
                    $url[$name] = $signedUrl;
                }
            } catch (\Exception $e) {

            }
        }
        $msg!='图片' && $msg .= '大小超过'.$size.'M';
        if($msg=='图片')$msg='';
        return ['list'=>$url,'msg'=>$msg];
    }
    public function uploadOne($path='',$size = 0){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        $cosClient = new Client(config("qcos"));
        $url = [];
        $bef_info=$file->getInfo();
        if($size && $bef_info['size']>$size*1024*1024){
            return  '图片'.$bef_info["name"].'大小超过'.$size.'M';
        }
        $tmp_name_arr=explode(".",$bef_info["name"]);
        $ext=".".end($tmp_name_arr);
        $path = $path?$path:$this->path;
        $time = md5(microtime(true));
        $name = "{$time}".$ext;
        $new_file= $path."/".$name;
        $tmp_file=$bef_info["tmp_name"];
        try {
            $result = $cosClient->Upload(
                $bucket = "hs",
                $key = $new_file,
                $body = fopen($tmp_file, 'rb')
            );
            $signedUrl = $cosClient->getObjectUrl($bucket, $new_file);
            if($signedUrl) {
                $url[$name] = $signedUrl;
            }
        } catch (\Exception $e) {

        }
        return ['list'=>$url];
    }
    //上传令牌
    public function signature(){
        $config = config("qcos");
//            $secret_id = "AKIDWrxnPl5jRu74lIrwEH4btufAOM4WncIi";
        $secret_id = $config['credentials']['secretId'];
        $secret_key = $config['credentials']['secretKey'];
//            $secret_key = "JMAi01mHVTAbmhLjrkNjpfEXaOvCEzUj";

        $current = time();
        $expired = $current + 1200;
        $arg_list = array(
            "secretId" => $secret_id,
            "currentTimeStamp" => $current,
            "expireTime" => $expired,
            "random" => rand());

        $orignal = http_build_query($arg_list);
        $signature = base64_encode(hash_hmac('SHA1', $orignal, $secret_key, true).$orignal);
        $msg = array(
            "code"=> 0,
            "data" => ['signature'=>$signature]
        );
        return $msg;
    }
}