<?php


namespace App\http\controllers;
use GuzzleHttp\Exception\RequestException;
use WechatPay\GuzzleMiddleware\WechatPayMiddleware;
use WechatPay\GuzzleMiddleware\Util\PemUtil;
use GuzzleHttp\HandlerStack;

class WxTestController
{
    public function wx_send(){
        $path = __DIR__."/../../cert/";
        $config = [
            // 必要配置
            'cert_path'          => $path.'apiclient_cert.pem', // XXX: 绝对路径！！！！
            'key_path'           => $path.'apiclient_key.pem',      // XXX: 绝对路径！！！！
        ];
//        echo $config['cert_path'];die;
// 商户相关配置，
        $merchantId = '1000100'; // 商户号
        $merchantSerialNumber = 'XXXXXXXXXX'; // 商户API证书序列号
//        $merchantPrivateKey = PemUtil::loadPrivateKey('./path/to/mch/private/key.pem'); // 商户私钥文件路径
        $merchantPrivateKey = PemUtil::loadPrivateKey($config['key_path']); // 商户私钥文件路径

// 微信支付平台配置
//        $wechatpayCertificate = PemUtil::loadCertificate('./path/to/wechatpay/cert.pem'); // 微信支付平台证书文件路径
        $wechatpayCertificate = PemUtil::loadCertificate($config['cert_path']); // 微信支付平台证书文件路径

// 构造一个WechatPayMiddleware
        $wechatpayMiddleware = WechatPayMiddleware::builder()
            ->withMerchant($merchantId, $merchantSerialNumber, $merchantPrivateKey) // 传入商户相关配置
            ->withWechatPay([ $wechatpayCertificate ]) // 可传入多个微信支付平台证书，参数类型为array
            ->build();

// 将WechatPayMiddleware添加到Guzzle的HandlerStack中
        $stack = \GuzzleHttp\HandlerStack::create();
        $stack->push($wechatpayMiddleware, 'wechatpay');

// 创建Guzzle HTTP Client时，将HandlerStack传入，接下来，正常使用Guzzle发起API请求，WechatPayMiddleware会自动地处理签名和验签
        // 创建Guzzle HTTP Client时，将HandlerStack传入，接下来，正常使用Guzzle发起API请求，WechatPayMiddleware会自动地处理签名和验签
        $client = new \GuzzleHttp\Client(['handler' => $stack]);

        try {
            $resp = $client->request(
                'POST',
                'https://api.mch.weixin.qq.com/v3/marketing/favor/users/o4GgauInH_RCEdvrrNGrntXDu6D4/coupons', //请求URL
                [
                    // JSON请求体
                    'json' => [
                        "stock_id" => "9856000",
                        "out_request_no" => "89560002019101000121",
                        "appid" => "wx233544546545989",
                        "stock_creator_mchid" => "8956000",
                    ],
                    'headers' => [ 'Accept' => 'application/json' ]
                ]
            );
            $statusCode = $resp->getStatusCode();
            if ($statusCode == 200) { //处理成功
                echo "success,return body = " . $resp->getBody()->getContents()."\n";
            } else if ($statusCode == 204) { //处理成功，无返回Body
                echo "success";
            }
        } catch (RequestException $e) {
            // 进行错误处理
            echo $e->getMessage()."\n";
            if ($e->hasResponse()) {
                echo "failed,resp code = " . $e->getResponse()->getStatusCode() . " return body = " . $e->getResponse()->getBody() . "\n";
            }
            return;
        }

    }

}