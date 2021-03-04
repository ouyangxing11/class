<?php
// header("Content-type: text/html; charset=utf-8");
// header("Access-Control-Allow-Origin:*");
// header('Access-Control-Allow-Methods:POST');
// header('Access-Control-Allow-Headers:x-requested-with, content-type');
require("qrcode/qrlib.php");
/**
 * 生成宣传海报
 * @param array  参数,包括图片和文字
 * @param string $filename 生成海报文件名,不传此参数则不生成文件,直接输出图片
 * @return [type] [description]
 */

$poster_face = 'http://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKhRIkNd1nIAU7TjwsleHVBPSBPO2SdANKI8KTV0D4B2u6JfouE9yMU4GQT8IJHwmQh7FMj3KB6jQ/132';
$qrcode = 'https://ksb.91renrenshi.com/?shopid=1';
$poster_img = "https://hs-1251609649.cos.ap-guangzhou.myqcloud.com/newhdp%2Flive_cover%2F929%2Fb2e0689b0e5736e0860e1bfdacaccf76.png";
//$poster_url = "https://testksb.91renrenshi.com/static/poster";
$poster_url = "https://poster.91renrenshi.com";
//$poster_url = "http://192.168.3.43:9000/static/poster";
$poster_id = 1;
$poster_nick = "小吴同志";
$poster_course = "众志成城-铁军团队是如何炼成的";
$poster_recommend='这个课程很棒，必须分享给你';
$poster_update = "更新至第Ｘ章节";
$company= "扫码领取课程兑换券";
$type = "column";
$code_number='';
$starttime='时间:2020-06-18 08:00 至 06-20 20:00';
$address='地址:深圳市龙华新区民治1970科技小镇';
$msg = $_GET['msg'];
$data = json_decode($msg,true);
if (isset($data["poster_id"])) $poster_id = $data["poster_id"];
if (isset($data["qrcode"]))  $qrcode = urldecode($data["qrcode"]);
if (isset($data["poster_img"])) $poster_img = $data["poster_img"];
if (isset($data["face"])) $poster_face = $data["face"];
if (isset($data["poster_update"])) $poster_update = $data["poster_update"];
if (isset($data["poster_nick"])) $poster_nick = urldecode($data["poster_nick"]);
if (isset($data["poster_course"])) $poster_course = $data["poster_course"];
if (isset($data["poster_recommend"])) $poster_recommend = $data["poster_recommend"];
if (isset($data["type"])) $type = $data["type"];
if (isset($data["code_number"])) $code_number = $data["code_number"];
if (isset($data["poster_company"])) $company = $data["poster_company"];
if (isset($data["starttime"])) $starttime = $data["starttime"];
if (isset($data["address"])) $address = $data["address"];
// print_r($poster_id);die;
//echo $poster_update;die;
$poster_course = mb_strlen($poster_course)>27?mb_substr($poster_course,0,24)." ···":$poster_course;
$config = array(
    'background' => array(
        'width' => 750,
        'height' => 1334,
        'image' => '',
        'color' => '255,255,255'//背景纯白
    )
);

$poster_data = [];

$poster_data[0] = array(
    'image' => array(
        array(
            'url' => $poster_img,     //二维码资源
            'stream' => 0,
            'left' => 0,
            'top' => 0,
            'right' => 0,
            'bottom' => 0,
            'width' => 750,
            'height' => 1334,
            'opacity' => 100
        ),
    ),
    'Qrcode' => array(
        array(
            'url' => $qrcode,     //二维码资源
            'stream' => 0,
            'left' => 550,
            'top' => 1095,
            'new_w'=>145,
            'new_h'=>145,//1
            'right' => 0,
            'bottom' => 0,
            'size' => 1,
            'opacity' => 100
        )
    )
);
$poster_data[1] = array(
    'image' => array(
        array(
            'url' => $poster_face,
            'stream' => 0,
            'left' => 315,
            'top' => 66,
            'right' => 0,
            'bottom' => 0,
            'width' => 132,
            'height' => 132,
            'opacity' => 100
        ),
        array(
            'url' => $poster_url . '/poster_4.png',     //二维码资源
            'stream' => 0,
            'left' => 0,
            'top' => 0,
            'right' => 0,
            'bottom' => 0,
            'width' => 750,
            'height' => 1334,
            'opacity' => 100
        ),
        array(
            'url' => $poster_img,     //二维码资源
            'stream' => 0,
            'left' => 18,
            'top' => 320,
            'right' => 0,
            'bottom' => 0,
            'width' => 714,
            'height' => 400,
            'opacity' => 100
        )
    ),
    'Qrcode' => array(
        array(
            'url' => $qrcode,     //二维码资源
            'stream' => 0,
            'left' => 590,
            'top' => 1065,
            'right' => 0,
            'bottom' => 0,
            'new_w'=>130,
            'new_h'=>130,//1
            'size' => 2,
            'opacity' => 100
        )
    ),
    'text' => array(
        array(
            'text' => $poster_nick,
            'left' => 50,
            'top' => 210,
            'fontPath' => 'msyhbd.ttc',     //字体文件
            'fontSize' => 25,             //字号
            'fontColor' => '0,0,0',       //字体颜色
            'angle' => 0,
            'align' => "center",
        ),
        array(
            'text' =>$poster_recommend,
            'left' => 220,
            'top' => 250,
            'fontPath' => 'msyh.ttc',     //字体文件
            'fontSize' => 17,             //字号
            'fontColor' => '125,125,125',       //字体颜色
            'angle' => 0,
            'align' => "center",
        ),
        array(
            'text' => $poster_course,
            'left' => 90,
            'top' => 800,
            'fontPath' => 'msyhbd.ttc',     //字体文件
            'fontSize' => 30,             //字号
            'fontColor' => '36,36,36',       //字体颜色
            'angle' => 0,
            'align' => "left",
        ),
        array(
            'text' => $poster_update,
            'left' => 90,
            'top' => 930,
            'fontPath' => 'msyh.ttc',     //字体文件
            'fontSize' => 19,             //字号
            'fontColor' => '151,151,151',       //字体颜色
            'angle' => 0,
            'align' => "left",
        )
    ),
);
$poster_data[2] = array(
    'image' => array(
        array(
            'url' => $poster_face,
            'stream' => 0,
            'left' => 64,
            'top' => 1055,
            'right' => 0,
            'bottom' => 0,
            'width' => 132,
            'height' => 132,
            'opacity' => 100
        ),
        array(
            'url' => $poster_url . '/poster_2.png',     //二维码资源
            'stream' => 0,
            'left' => 0,
            'top' => 0,
            'right' => 0,
            'bottom' => 0,
            'width' => 750,
            'height' => 1334,
            'opacity' => 100
        ),
        array(
            'url' => $poster_img,     //二维码资源
            'stream' => 0,
            'left' => 41,
            'top' => 122,
            'right' => 0,
            'bottom' => 0,
            'width' => 670,
            'height' => 375,
            'opacity' => 100
        )
    ),
    'Qrcode' => array(
        array(
            'url' => $qrcode,     //二维码资源
            'stream' => 0,
            'left' => 530,
            'top' => 810,
            'new_w'=>150,
            'new_h'=>150,//1
            'right' => 0,
            'bottom' => 0,
            'size' => 2,
            'opacity' => 100
        )
    ),
    'text' => array(
        array(
            'text' => $poster_nick,
            'left' => 220,
            'top' => 1101,
            'fontPath' => 'msyhbd.ttc',     //字体文件
            'fontSize' => 19,             //字号
            'fontColor' => '0,0,0',       //字体颜色
            'angle' => 0,
            'align' => "left",
        ),
        array(
            'text' => $poster_recommend,
            'left' => 220,
            'top' => 1140,
            'fontPath' => 'msyh.ttc',     //字体文件
            'fontSize' => 17,             //字号
            'fontColor' => '0,0,0',       //字体颜色
            'angle' => 0,
            'align' => "left",
        ),
        array(
            'text' => $poster_course,
            'left' => 90,
            'top' => 550,
            'fontPath' => 'msyhbd.ttc',     //字体文件
            'fontSize' => 30,             //字号
            'fontColor' => '36,36,36',       //字体颜色
            'angle' => 0,
            'align' => "left",
        ),
        array(
            'text' => $poster_update,
            'left' => 90,
            'top' => 666,
            'fontPath' => 'msyhbd.ttc',     //字体文件
            'fontSize' => 19,             //字号
            'fontColor' => '151,151,151',       //字体颜色
            'angle' => 0,
            'align' => "left",
        ),
    ),
);

$poster_data[3] = array(
    'image' => array(
        array(
            'url' => $poster_face,
            'stream' => 0,
            'left' => 50,
            'top' => 855,
            'right' => 0,
            'bottom' => 0,
            'width' => 132,
            'height' => 132,
            'opacity' => 100
        ),
        array(
            'url' => $poster_url . '/poster_3.png',     //二维码资源
            'stream' => 0,
            'left' => 0,
            'top' => 0,
            'right' => 0,
            'bottom' => 0,
            'width' => 750,
            'height' => 1334,
            'opacity' => 100
        ),
        array(
            'url' => $poster_img,     //二维码资源
            'stream' => 0,
            'left' => 24,
            'top' => 67,
            'right' => 0,
            'bottom' => 0,
            'width' => 702,
            'height' => 393,
            'opacity' => 100
        )
    ),
    'Qrcode' => array(
        array(
            'url' => $qrcode,     //二维码资源
            'stream' => 0,
            'left' => 250,
            'top' => 1004,
            'new_w'=>130,
            'new_h'=>130,//1
            'right' => 0,
            'bottom' => 0,
            'size' =>2,
            'opacity' => 100
        )
    ),
    'text' => array(
        array(
            'text' => $poster_nick,
            'left' => 200,
            'top' => 900,
            'fontPath' => 'msyhbd.ttc',     //字体文件
            'fontSize' => 25,             //字号
            'fontColor' => '0,0,0',       //字体颜色
            'angle' => 0,
            'align' => "left",
        ),
        array(
            'text' => $poster_recommend,
            'left' => 200,
            'top' => 940,
            'fontPath' => 'msyh.ttc',     //字体文件
            'fontSize' => 17,             //字号
            'fontColor' => '0,0,0',       //字体颜色
            'angle' => 0,
            'align' => "left",
        ),
        array(
            'text' => $poster_course,
            'left' => 90,
            'top' => 550,
            'fontPath' => 'msyhbd.ttc',     //字体文件
            'fontSize' => 30,             //字号
            'fontColor' => '36,36,36',       //字体颜色
            'angle' => 0,
            'align' => "left",
        ),
        array(
            'text' => $poster_update,
            'left' => 90,
            'top' => 700,
            'fontPath' => 'msyh.ttc',     //字体文件
            'fontSize' => 19,             //字号
            'fontColor' => '151,151,151',       //字体颜色
            'angle' => 0,
            'align' => "left",
        )
    ),
);

$poster_data[4] = array(
    'image' => array(
        array(
            'url' => $poster_face,
            'stream' => 0,
            'left' => 308,
            'top' => 50,
            'right' => 0,
            'bottom' => 0,
            'width' => 132,
            'height' => 132,
            'opacity' => 100
        ),
        array(
            'url' => $poster_url . '/poster_1.png',     //二维码资源
            'stream' => 0,
            'left' => 0,
            'top' => 0,
            'right' => 0,
            'bottom' => 0,
            'width' => 750,
            'height' => 1334,
            'opacity' => 100
        ),
        array(
            'url' => $poster_img,     //二维码资源
            'stream' => 0,
            'left' => 40,
            'top' => 302,
            'right' => 0,
            'bottom' => 0,
            'width' => 670,
            'height' => 375,
            'opacity' => 100
        )
    ),
    'Qrcode' => array(
        array(
            'url' => $qrcode,     //二维码资源
            'stream' => 0,
            'left' => 195,
            'top' => 1050,
            'new_w'=>170,
            'new_h'=>170,//1
            'right' => 0,
            'bottom' => 0,
            'size' => 3.65,
            'opacity' => 100
        )
    ),
    'text' => array(
        array(
            'text' => $poster_nick,
            'left' => 308,
            'top' => 200,
            'fontPath' => 'msyhbd.ttc',     //字体文件
            'fontSize' => 19,             //字号
            'fontColor' => '255,255,255',       //字体颜色
            'angle' => 0,
            'align' => "center",
        ),
        array(
            'text' => $poster_recommend,
            'left' => 220,
            'top' => 240,
            'fontPath' => 'msyh.ttc',     //字体文件
            'fontSize' => 17,             //字号
            'fontColor' => '255,255,255',       //字体颜色
            'angle' => 0,
            'align' => "center",
        ),
        array(
            'text' => $poster_course,
            'left' => 90,
            'top' => 780,
            'fontPath' => 'msyhbd.ttc',     //字体文件
            'fontSize' => 30,             //字号
            'fontColor' => '36,36,36',       //字体颜色
            'angle' => 0,
            'align' => "left",
        ),
        array(
            'text' => $poster_update,
            'left' => 100,
            'top' => 950,
            'fontPath' => 'msyh.ttc',     //字体文件
            'fontSize' => 19,             //字号
            'fontColor' => '151,151,151',       //字体颜色
            'angle' => 0,
            'align' => "left",
        )
    ),
);
$poster_data[5] = array(
    'image' => array(
        array(
            'url' => $poster_face,
            'stream' => 0,
            'left' => 20,
            'top' => 50,
            'right' => 0,
            'bottom' => 0,
            'width' => 132,
            'height' => 132,
            'opacity' => 100
        )
    ,
        array(
            'url' => $poster_url . '/poster_5.png',     //二维码资源
            'stream' => 0,
            'left' => 0,
            'top' => 0,
            'right' => 0,
            'bottom' => 0,
            'width' => 750,
            'height' => 1334,
            'opacity' => 100
        ),
        array(
            'url' => $poster_img,     //二维码资源
            'stream' => 0,
            'left' => 28,
            'top' => 210,
            'right' => 0,
            'bottom' => 0,
            'width' => 694,
            'height' => 389,
            'opacity' => 100
        )
    ),
    'Qrcode' => array(
        array(
            'url' => $qrcode,     //二维码资源
            'stream' => 0,
            'left' => 70,
            'top' => 1000,
            'right' => 0,
            'new_w'=>150,
            'new_h'=>150,//1
            'bottom' => 0,
            'size' => 2,
            'opacity' => 100
        )
    ),
    'text' => array(
        array(
            'text' => $poster_nick,
            'left' => 150,
            'top' => 95,
            'fontPath' => 'msyhbd.ttc',     //字体文件
            'fontSize' => 19,             //字号
            'fontColor' => '255,255,255',       //字体颜色
            'angle' => 0,
            'align' => "left",
        ),
        array(
            'text' => $poster_recommend,
            'left' => 150,
            'top' => 130,
            'fontPath' => 'msyh.ttc',     //字体文件
            'fontSize' => 17,             //字号
            'fontColor' => '255,255,255',       //字体颜色
            'angle' => 0,
            'align' => "left",
        ),
        array(
            'text' => $poster_course,
            'left' => 80,
            'top' => 700,
            'fontPath' => 'msyh.ttc',     //字体文件
            'fontSize' => 30,             //字号
            'fontColor' => '36,36,36',       //字体颜色
            'angle' => 0,
            'align' => "left",
        ),
        array(
            'text' => $poster_update,
            'left' => 80,
            'top' => 800,
            'fontPath' => 'msyh.ttc',     //字体文件
            'fontSize' => 19,             //字号
            'fontColor' => '151,151,151',       //字体颜色
            'angle' => 0,
            'align' => "left",
        )
    ),
);

//$poster_img = "http://hs-1251609649.cos.ap-guangzhou.myqcloud.com/newhdp%2Flive_poster%2Fcolumn%2F907%2F0%2F0efb61bc04a9a7c581205551da6b2931.png";

$poster_data[6] = array(
    'image' => array(
        array(
            'url' => $poster_url . '/poster_6.jpg',     //二维码资源
            'stream' => 0,
            'left' => 0,
            'top' => 0,
            'right' => 0,
            'bottom' => 0,
            'width' => 750,
            'height' => 1334,
            'opacity' => 100
        ),
        array(
            'url' => $poster_img,
            'stream' => 0,
            'left' => 92,
            'top' => 270,
            'right' => 0,
            'bottom' => 0,
            'width' => 578,
            'height' => 325,
            'opacity' => 100
        )
    ),
    'Qrcode' => array(
        array(
            'url' => $qrcode,     //二维码资源
            'stream' => 0,
            'new_w'=>200,
            'new_h'=>200,
            'left' => 270,
            'top' => 990,
            'right' => 0,
            'bottom' => 0,
            'size' => 6,
            'opacity' => 100
        )
    ),
    'text' => array(
        array(
            'text' => $poster_course,
            'left' => 90,
            'top' => 700,
            'fontPath' => 'msyh.ttc',     //字体文件
            'fontSize' => 30,             //字号
            'fontColor' => '25,25,25',       //字体颜色
            'angle' => 0,
            'align' => "left",
        ),
        array(
            'text' => 'live'==$type ?"开始时间":"更新",
            'left' => 90,
            'top' => 860,
            'fontPath' => 'msyh.ttc',     //字体文件
            'fontSize' => 19,             //字号
            'fontColor' => '151,151,151',       //字体颜色
            'angle' => 0,
            'align' => "left",
        ),
        array(
            'text' => "价格",
            'left' => 550,
            'top' => 860,
            'fontPath' => 'msyh.ttc',     //字体文件
            'fontSize' => 19,             //字号
            'fontColor' => '151,151,151',       //字体颜色
            'angle' => 0,
            'align' => "left",
        ),
        
        array(
            'text' => $poster_update,
            'left' => 90,
            'top' => 900,
            'fontPath' => 'msyh.ttc',     //字体文件
            'fontSize' => 19,             //字号
            'fontColor' => '56,56,56',       //字体颜色
            'angle' => 0,
            'align' => "left",
        ),
        array(
            'text' => $company,
            'left' => 90,
            'top' => mb_strlen($company)>13?100:130,
            'fontPath' => 'msyhbd.ttc',     //字体文件
            'fontSize' => 30,             //字号
            'fontColor' => '255,220,82',       //字体颜色
            'angle' => 0,
            'align' => "center",
        ),
        array(
            'text' => $poster_recommend,
            'left' => 550,
            'top' => 900,
            'fontPath' => 'msyh.ttc',     //字体文件
            'fontSize' => 19,             //字号
            'fontColor' => '56,56,56',       //字体颜色
            'angle' => 0,
            'align' => "left",
        ),
        array(
            'text' => $code_number,
            'left' => 360,
            'top' => 1220,
            'fontPath' => 'msyh.ttc',     //字体文件
            'fontSize' => 19,             //字号
            'fontColor' => '56,56,56',       //字体颜色
            'angle' => 0,
            'align' => "left",
        )
        
    ),
);

$poster_data[7] = array(//活动海报
    'image' => array(
        array(
            'url' => $poster_url . '/poster_6.jpg',     //二维码资源
            'stream' => 0,
            'left' => 0,
            'top' => 0,
            'right' => 0,
            'bottom' => 0,
            'width' => 750,
            'height' => 1334,
            'opacity' => 100
        ),
        array(
            'url' => $poster_img,
            'stream' => 0,
            'left' => 92,
            'top' => 270,
            'right' => 0,
            'bottom' => 0,
            'width' => 578,
            'height' => 325,
            'opacity' => 100
        )
    ),
    'Qrcode' => array(
        array(
            'url' => $qrcode,     //二维码资源
            'stream' => 0,
            'new_w'=>200,
            'new_h'=>200,
            'left' => 280,
            'top' => 990,
            'right' => 0,
            'bottom' => 0,
            'size' => 6,
            'opacity' => 100
        )
    ),
    'text' => array(
        array(
            'text' => $poster_course,
            'left' => 90,
            'top' => 700,
            'fontPath' => 'msyh.ttc',     //字体文件
            'fontSize' => 32,             //字号
            'fontColor' => '25,25,25',       //字体颜色
            'angle' => 0,
            'align' => "left",
        ),
        array(
            'text' => $starttime,
            'left' => 90,
            'top' => 850,
            'fontPath' => 'msyh.ttc',     //字体文件
            'fontSize' => 17,             //字号
            'fontColor' => '151,151,151',       //字体颜色
            'angle' => 0,
            'align' => "left",
        ),
        array(
            'text' => $address,
            'left' => 90,
            'top' => 920,
            'fontPath' => 'msyh.ttc',     //字体文件
            'fontSize' => 17,             //字号
            'fontColor' => '151,151,151',       //字体颜色
            'angle' => 0,
            'align' => "left",
        ),
        array(
            'text' => $company,
            'left' => 90,
            'top' => mb_strlen($company)>13?100:130,
            'fontPath' => 'msyhbd.ttc',     //字体文件
            'fontSize' => 30,             //字号
            'fontColor' => '255,220,82',       //字体颜色
            'angle' => 0,
            'align' => "center",
        ),
        array(
            'text' => $code_number,
            'left' => 360,
            'top' => 1220,
            'fontPath' => 'msyh.ttc',     //字体文件
            'fontSize' => 19,             //字号
            'fontColor' => '56,56,56',       //字体颜色
            'angle' => 0,
            'align' => "center",
        )
    ),
);

if ($type=="member"){
    $poster_data[1] = array(
        'image'=>array(
            array(
                'url'=>$poster_url . '/poster_z1.jpg',
                'stream'=>0,
                'left'=>0,
                'top'=>0,
                'right'=>0,
                'bottom'=>0,
                'width'=>750,
                'height'=>1334,
                'opacity'=>100
            )
        ),
        'Qrcode'=>array(
            array(
                'url'=>$qrcode,     //二维码资源
                'stream'=>0,
                'left'=>280,
                'top'=>970,
                'right'=>0,
                'bottom'=>0,
                'size'=>4.8,
                'opacity'=>100
            )
        )
    );
    $poster_data[2]=array(
        'image'=>array(
            array(
                'url'=>$poster_url . '/poster_z2.jpg',
                'stream'=>0,
                'left'=>0,
                'top'=>0,
                'right'=>0,
                'bottom'=>0,
                'width'=>750,
                'height'=>1334,
                'opacity'=>100
            )
        ),
        'Qrcode'=>array(
            array(
                'url'=>$qrcode,     //二维码资源
                'stream'=>0,
                'left'=>520,
                'top'=>950,
                'right'=>0,
                'bottom'=>0,
                'size'=>3.8,
                'opacity'=>100
            )
        )
    );
    $poster_data[3]=array(
        'image'=>array(
            array(
                'url'=>$poster_url . '/poster_z3.jpg',
                'stream'=>0,
                'left'=>0,
                'top'=>0,
                'right'=>0,
                'bottom'=>0,
                'width'=>750,
                'height'=>1334,
                'opacity'=>100
            )
        ),
        'Qrcode'=>array(
            array(
                'url'=>$qrcode,     //二维码资源
                'stream'=>0,
                'left'=>281,
                'top'=>475,
                'right'=>0,
                'bottom'=>0,
                'size'=>3.8,
                'opacity'=>100
            )
        )
    );
    $poster_data[4]=array(
        'image'=>array(
            array(
                'url'=>$poster_url . '/poster_z4.jpg',
                'stream'=>0,
                'left'=>0,
                'top'=>0,
                'right'=>0,
                'bottom'=>0,
                'width'=>750,
                'height'=>1334,
                'opacity'=>100
            )
        ),
        'Qrcode'=>array(
            array(
                'url'=>$qrcode,     //二维码资源
                'stream'=>0,
                'left'=>520,
                'top'=>990,
                'right'=>0,
                'bottom'=>0,
                'size'=>3.8,
                'opacity'=>100
            )
        )
    );
    $poster_data[5]=array(
        'image'=>array(
            array(
                'url'=>$poster_url . '/poster_z5.jpg',
                'stream'=>0,
                'left'=>0,
                'top'=>0,
                'right'=>0,
                'bottom'=>0,
                'width'=>750,
                'height'=>1334,
                'opacity'=>100
            )
        ),
        'Qrcode'=>array(
            array(
                'url'=>$qrcode,     //二维码资源
                'stream'=>0,
                'left'=>520,
                'top'=>990,
                'right'=>0,
                'bottom'=>0,
                'size'=>3.8,
                'opacity'=>100
            )
        )
    );
}elseif ($type=="recruit"){
    $poster_data[1] = array(
        'image'=>array(
            array(
                'url'=>$poster_url . '/recruit_1.jpg',
                'stream'=>0,
                'left'=>0,
                'top'=>0,
                'right'=>0,
                'bottom'=>0,
                'width'=>750,
                'height'=>1334,
                'opacity'=>100
            )
        ),
        'Qrcode'=>array(
            array(
                'url'=>$qrcode,     //二维码资源
                'stream'=>0,
                'left'=>517,
                'top'=>1097,
                'right'=>0,
                'bottom'=>0,
                'size'=>4.2,
                'opacity'=>100
            )
        )
    );
    $poster_data[2]=array(
        'image'=>array(
            array(
                'url'=>$poster_url . '/recruit_2.jpg',
                'stream'=>0,
                'left'=>0,
                'top'=>0,
                'right'=>0,
                'bottom'=>0,
                'width'=>750,
                'height'=>1334,
                'opacity'=>100
            )
        ),
        'Qrcode'=>array(
            array(
                'url'=>$qrcode,     //二维码资源
                'stream'=>0,
                'left'=>508,
                'top'=>1135,
                'right'=>0,
                'bottom'=>0,
                'size'=>4.1,
                'opacity'=>100
            )
        )
    );
    $poster_data[3]=array(
        'image'=>array(
            array(
                'url'=>$poster_url . '/recruit_3.jpg',
                'stream'=>0,
                'left'=>0,
                'top'=>0,
                'right'=>0,
                'bottom'=>0,
                'width'=>750,
                'height'=>1334,
                'opacity'=>100
            )
        ),
        'Qrcode'=>array(
            array(
                'url'=>$qrcode,     //二维码资源
                'stream'=>0,
                'left'=>512,
                'top'=>1125,
                'right'=>0,
                'bottom'=>0,
                'size'=>4.1,
                'opacity'=>100
            )
        )
    );
    $poster_data[4]=array(
        'image'=>array(
            array(
                'url'=>$poster_url . '/recruit_4.jpg',
                'stream'=>0,
                'left'=>0,
                'top'=>0,
                'right'=>0,
                'bottom'=>0,
                'width'=>750,
                'height'=>1334,
                'opacity'=>100
            )
        ),
        'Qrcode'=>array(
            array(
                'url'=>$qrcode,     //二维码资源
                'stream'=>0,
                'left'=>518,
                'top'=>1127,
                'right'=>0,
                'bottom'=>0,
                'size'=>4.1,
                'opacity'=>100
            )
        )
    );
    $poster_data[5]=array(
        'image'=>array(
            array(
                'url'=>$poster_url . '/recruit_5.jpg',
                'stream'=>0,
                'left'=>0,
                'top'=>0,
                'right'=>0,
                'bottom'=>0,
                'width'=>750,
                'height'=>1334,
                'opacity'=>100
            )
        ),
        'Qrcode'=>array(
            array(
                'url'=>$qrcode,     //二维码资源
                'stream'=>0,
                'left'=>550,
                'top'=>1122,
                'right'=>0,
                'bottom'=>0,
                'size'=>4.1,
                'opacity'=>100
            )
        )
    );
}elseif($type=="register"){
    $poster_data[1] = array(
        'image' => array(
            array(
                'url' => $poster_url.'/register_1.jpg',     //二维码资源
                'stream' => 0,
                'left' => 0,
                'top' => 0,
                'right' => 0,
                'bottom' => 0,
                'width' => 750,
                'height' => 1334,
                'opacity' => 100
            ),
        ),
        'Qrcode' => array(
            array(
                'url'    => $qrcode,     //二维码资源
                'stream' => 0,
                'left'   => 290,
                'top'    => 1045,
                'new_w'  =>175,
                'new_h'  =>175,
                'right'  => 0,
                'bottom' => 0,
                'size'   => 6,
                'opacity' => 100
            )
        )
    );
    $poster_data[2] = array(
        'image' => array(
            array(
                'url' => $poster_url.'/register_2.jpg',     //二维码资源
                'stream' => 0,
                'left' => 0,
                'top' => 0,
                'right' => 0,
                'bottom' => 0,
                'width' => 750,
                'height' => 1334,
                'opacity' => 100
            ),
        ),
        'Qrcode' => array(
            array(
                'url'    => $qrcode,     //二维码资源
                'stream' => 0,
                'left'   => 300,
                'top'    => 895,
                'new_w'  =>185,
                'new_h'  =>185,
                'right'  => 0,
                'bottom' => 0,
                'size'   => 6,
                'opacity' => 100
            )
        )
    );
    $poster_data[3] = array(
        'image' => array(
            array(
                'url' => $poster_url.'/register_3.jpg',     //二维码资源
                'stream' => 0,
                'left' => 0,
                'top' => 0,
                'right' => 0,
                'bottom' => 0,
                'width' => 750,
                'height' => 1334,
                'opacity' => 100
            ),
        ),
        'Qrcode' => array(
            array(
                'url'    => $qrcode,     //二维码资源
                'stream' => 0,
                'left'   => 435,
                'top'    => 892,
                'new_w'  =>185,
                'new_h'  =>185,
                'right'  => 0,
                'bottom' => 0,
                'size'   => 6,
                'opacity' => 100
            )
        )
    );
}

$poster = new Poster();
$poster->create($config, $poster_data[$poster_id], $filename);


class Poster
{
    function create($config = array(), $poster = array(), $filename = "")
    {
        $this->config = $config;
        ob_clean();

      if (empty($filename)) header("content-type: image/png");

        $imageDefault = array(
            'left' => 0,
            'top' => 0,
            'right' => 0,
            'bottom' => 0,
            'width' => 100,
            'height' => 100,
            'opacity' => 100
        );
        $textDefault = array(
            'text' => '',
            'left' => 0,
            'top' => 0,
            'fontSize' => 32,       //字号
            'fontColor' => '255,255,255', //字体颜色
            'angle' => 0,
        );

        $canvas = $this->background($config);

        if (!empty($poster['image'])) {
            foreach ($poster['image'] as $key => $val) {
                $val = array_merge($imageDefault, $val);
                $canvas = $this->imgLayer($val, $canvas);
            }
        }

        if (!empty($poster['text'])) {
            foreach ($poster['text'] as $key => $val) {
                $val = array_merge($textDefault, $val);
                $this->textLayer($val, $canvas);
            }
        }

        if (!empty($poster['Qrcode'])) {
            foreach ($poster['Qrcode'] as $key => $val) {
                $val = array_merge($textDefault, $val);
                $this->Qrcode($val, $canvas);
            }
        }

        //生成图片
        if (!empty($filename)) {
            $res = imagejpeg($canvas, $filename, 90); //保存到本地
            imagedestroy($canvas);
            if (!$res) return false;
            return $filename;
        } else {
            imagepng($canvas);     //在浏览器上显示
            imagedestroy($canvas);
        }
    }


    public function background($config)
    {
        $canvas = imagecreatetruecolor($config['background']['width'], $config['background']['height']);
        $bgcolor = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $bgcolor);
        return $canvas;
    }


    function imgLayer($data, $canvas)
    {

        $url_arr = parse_url($data["url"]);
        if (isset($url_arr["scheme"])) {
            $image = imagecreatefromstring($this->vget($data["url"]));
        } else {
            $images_info = getimagesize($data["url"]);
            $imagesFun = 'imagecreatefrom' . image_type_to_extension($images_info[2], false);//生成对应的函数名
            $image = $imagesFun($data["url"]);//调用生成的函数名
        }

        $oWidth = imagesx($image);
        $oHeight = imagesy($image);

        imagecopyresampled($canvas, $image, $data["left"], $data["top"], 0, 0, $data["width"], $data["height"], $oWidth, $oHeight);

        return $canvas;


    }

    function textLayer($val, $canvas)
    {

        $page_width = $this->config["background"]["width"];
        $font_pix = [12 => 18, 14 => 19, 17 => 23, 18 => 25, 19 => 26, 20 => 27, 21 => 28, 22 => 29, 23 => 30, 24 => 32, 25 => 33, 30 => 39,32=>42];
        $text_width = (strlen($val['text']) + mb_strlen($val['text'], 'UTF8')) / 2 * ($font_pix[$val["fontSize"]] / 2);
//        if ($val["align"] == "center") {
//            $text_left = ($page_width - $text_width) / 2;
//        } elseif ($val["align"] == "left") {
            $part_num = round((strlen($val['text']) + mb_strlen($val['text'], 'UTF8')) * ($font_pix[$val["fontSize"]] / 2)) / 500;
            $singer_num = round(560 / ($font_pix[$val["fontSize"]]));
            $part = array();
            $j=0;
//            print_r($val);die;
            for ($i = 0; $i < $part_num; $i++) {
                $part[$i] = $val;
                $str_num = $singer_num;
                $singer_str='';
                for(;$str_num>0;$j++){
                    $txt = mb_substr($val['text'],$j,1);
                    if(strlen($txt)>1)$str_num=$str_num-1;
                    else$str_num=$str_num-(2/3);
                    $singer_str .=$txt;
                }
	           if($singer_str=='')break;
                $part[$i]['text'] = $singer_str;//mb_substr($val['text'], $i * $singer_num, $singer_num);
                $part[$i]['top'] = $val['top'] + $i * $font_pix[$val["fontSize"]] + 20 * $i;
                list($R, $G, $B) = explode(',', $part[$i]['fontColor']);
                $fontColor = imagecolorallocate($canvas, $R, $G, $B);
                $part[$i]['left'] = $part[$i]['left'] < 0 ? $backgroundWidth - abs($part[$i]['left']) : $part[$i]['left'];
                $part[$i]['top'] = $part[$i]['top'] < 0 ? $backgroundHeight - abs($part[$i]['top']) : $part[$i]['top'];
                if ($part[$i]['align'] == "center") {
                    $str_num = (strlen($part[$i]['text']) + mb_strlen($part[$i]['text'], 'UTF8')) * $font_pix[$part[$i]['fontSize']] / 4;
//                    var_dump($str_num);die;
                    $text_left = $page_width>$str_num?($page_width-$str_num)/2:$val['left'];
//                    var_dump($val["left"]);die;
//                    if($text_left<$val["left"])$text_left = $val["left"];
//                    echo $text_left;die;
                } elseif ($part[$i]['align'] == "left") {
                    $text_left = $part[$i]['left'];
                } else {
                    $text_left = $val['left'];
                }
                imagettftext($canvas, $part[$i]['fontSize'], $part[$i]['angle'], $text_left, $part[$i]['top'], $fontColor, $part[$i]['fontPath'], $part[$i]['text']);
            }
            return $canvas;
//        } else {
//            $text_left = $val['left'];
//        }

        list($R, $G, $B) = explode(',', $val['fontColor']);
        $fontColor = imagecolorallocate($canvas, $R, $G, $B);
        $val['left'] = $val['left'] < 0 ? $backgroundWidth - abs($val['left']) : $val['left'];
        $val['top'] = $val['top'] < 0 ? $backgroundHeight - abs($val['top']) : $val['top'];
        imagettftext($canvas, $val['fontSize'], $val['angle'], $text_left, $val['top'], $fontColor, $val['fontPath'], $val['text']);
        return $canvas;
    }


    public function QRcode($val, $canvas)
    {


        $errorCorrectionLevel = 'L';  //容错级别
        $matrixPointSize = $val["size"];      //生成图片大小
        $filename = 'qrcode/' . microtime() . '.png';

        QRcode::png($val["url"], $filename, $errorCorrectionLevel, $matrixPointSize, 2);

        $image = imagecreatefromstring(file_get_contents($filename));

        $oWidth = imagesx($image);
        $oHeight = imagesy($image);

        $val["new_h"] = isset($val["new_h"]) ?$val["new_h"]:$oWidth;
        $val["new_w"] = isset($val["new_w"]) ?$val["new_w"]:$oHeight;
        imagecopyresampled($canvas, $image, $val["left"], $val["top"], 0, 0, $val["new_h"], $val["new_w"], $oWidth, $oHeight);
        return $canvas;
    }


    private function vget($url)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
// 		curl_setopt($curl, CURLOPT_HTTPHEADER, $heard); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno' . curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据
    }

}