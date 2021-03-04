<?php
namespace myclass;

use myclass\DESDZFP;
class DZFP
{
    protected $desdzfp = null;
    protected $fppix = 'HS'; // 订单号前缀   // 正式用 `HS`


    protected $isDev = true;  //测试环境
    protected $identity = '93363DCC6064869708F1F3C72A0CE72A713A9D425CD50CDE'; // 测试
    protected $sh = '339901999999142';                                       // 测试税号

//    protected $isDev = false;  //正式环境
//    protected $identity = 'A0138BA45284F9C8C60E82C869F8D926C18DB20078CCB7BC';	// 正式
//    protected $sh = '914403005956807024';										// 华师税号

    public function __construct() {
        $this->desdzfp = new DESDZFP();
    }

    // 开票请求

    /**
     * typeid=`c` 纸质发票 购方名称，购方税号必填
     * typeid=`p` 电子发票 购方名称，购方税号，邮箱必填
     * typeid=`s` 专票 购方名称 购方税号 地址 开户行 电话 银行账号必填
     */
    public function post($data=[],$operator=[]){
        // 操作员信息
        $operator['name'] = session('name');
        $operator['open_timne'] = date('Y-m-d H:i:s');

        if( !$data || !$data['orderDetails'] )return ['code'=>1,'info'=>'订单详情不能为空'];
        $tsfs = -1;
        if($data['typeid'] == 'p'){
            $tsfs = 0;
        }
        $data['taxno'] = preg_replace('# #','',$data['taxno']);
        $arr = [
            'identity'=>$this->identity,
            'order'=>[
                'buyername'=>trim($data['company']),                             // 购方名称  *
                'phone'=>trim($data['contacttel'])?trim($data['contacttel']):0,  // 购方手机(开票成功会短信提醒购方) *
                'taxnum'=>$data['taxno'],                                        // 购方税号 *
                'address'=>trim($data['address']),                               // 购方地址 *
                'account'=>trim($data['bankaddr']).' '.trim($data['account']),   // 购方银行账号
                'telephone'=>trim($data['contacttel']),                           // 购方电话 eg：0755-28888888
                'orderno'=>$this->fppix.$data['id'],                             // 订单号 *
                'invoicedate'=>$operator['open_timne'],                          // 开票时间 *
                'clerk'=>'谢雨廷',                                                 // 开票员 *  固定值
                'saleaccount'=>'招商银行深圳民治支行755939633210805',         // 销方账号 （自己公司的账号）
                'salephone'=>'0755-23088556',                                    // 销方电话   （自己公司的电话）
                'saleaddress'=>'深圳市龙华区民治街道民治社区1970科技园1栋301',   // 销方地址 （自己公司的地址）
                'saletaxnum'=>$this->sh,                                         // 销方税号  // $data['taxno'] （自己公司的税号）*  914403005956807024
                'kptype'=> $data['kptype'] == 2 ? 2 : 1,                                                   // 开票类型:1,正票;2,红
                'message'=>$data['kptype'] == 2 ? '对应正数发票代码:'.$data['dzfp_fpdm'].'号码:'.$data['dzfp_fphm'].'文案':1,                                                   // 备注
                'fpdm' => $data['kptype'] == 2 ? str_pad($data['dzfp_fpdm'],12,0,STR_PAD_LEFT ) : '',       //红票必须填，不满12位左补0,
                'fphm' => $data['kptype'] == 2 ? str_pad($data['dzfp_fphm'],8,0,STR_PAD_LEFT ) : '',       //红票必须填，不满8位左补0,
                'message'=>$data['remark'],                                                   // 备注
                'payee' =>'张玉凤',                                                // 收款人 固定值
                'checker'   => '宋良宏',                                            // 复核人 固定值
//                'fpdm' => '125999915630',                                         // 对应蓝票发票代码
//                'fphm' => '00130865',                                             // 对应蓝票发票号码
                'tsfs' =>$tsfs,                                                      // 推 送 方 式 :-1, 不 推送;0,邮箱;1,手机(默认);2,邮箱、手机
                'email' => trim($data['email']),                                    // 推送邮箱（tsfs 为 0或 2 时，此项为必填）
//                'qdbz' => '0',                                                    // 清单标志:0,根据项目名称数，自动产生清单;1,将项目信息打印至清单
//                'qdxmmc' => '1',                                                  // 清单项目名称:打印清单时对应发票票面项目名称，注意：税总要求清单项目名称为（详见销货清单）
//                'dkbz' => '0',                                                    // 代开标志:0 非代开;1代开。代开蓝票备注文案要求包含：代开企业税号:***,代开企业名称:***；代开红票备注文案要求：对应正数发票代码:***号码:***代开企业税号 :*** 代 开 企 业 名称:***
//                'deptid' => '9F7E9439CA8B4C60A2FF',                               // 部门门店 id（诺诺系统中的 id
//                'clerkid' => '3F7EA439CA8B4C60A2FFF3EA3290B084',                  // 开票员 id（诺诺系统中的 id）
                'invoiceLine' => trim($data['typeid']),                             // 发票种类，p 电子增值税普通发票，c 增值税普通发票(纸票)，s增值税专用发票，e收购发票(电子)，f 收购发票(纸质)
//                'cpybz' => '0',                                                   // 成品油标志：0 非成品油，1 成品油
            ],
        ];
        $adjustGoods = [];
        $tmp=[];
        $maxMoney = 0;
        $maxID = 0;
        //税收分类编码
        if ($data['goods'] == '培训费') {
            $spbm = 307020102;
        } else if ($data['goods'] == '咨询费') {
            $spbm = 304060399;
        }
        // 找出最大金额项
        if($data['adjust'] == -1){
            foreach ($data['orderDetails'] as $k=>$v){
                if($maxMoney < $v['moneyperday']){
                    $maxMoney = $v['moneyperday'];
                    $maxID = $v['orderid'];
                    $adjustGoods = [
                        'goodsname'=>trim($data['goods']),
                        'num' => -1,
                        'price'=>floatval($data['adjust_num']),
                        'hsbz'=>0,
                        'taxrate'=>number_format($data['taxrate'],2),
                        'unit'=>'天',
                        'fphxz'=> 1,
                        'spbm' => $spbm,  //商品编码
                    ];
                }
            }
        }elseif($data['adjust'] == 1){
            $adjustGoods = [
                'goodsname'=>trim($data['goods']),
                'num' => $data['kptype'] == 2 ? -1 : 1,
                'price'=>floatval($data['adjust_num']),
                'hsbz'=>0,
                'taxrate'=>number_format($data['taxrate'],2),
                'unit'=>'天',
                'fphxz'=> 0
            ];
        }else{
            $expense = array_sum(array_column($data['orderDetails'],'expense'));
            if(!empty($expense)){
                $adjustGoods = [
                    'goodsname'=>trim($data['goods']),
                    'num' => $data['kptype'] == 2 ? -1 : 1,
                    'price'=>$expense,
                    'hsbz'=>0,
                    'taxrate'=>number_format($data['taxrate'],2),
                    'unit'=>'天',
                    'fphxz'=> 0,
                    'spbm' => $spbm,  //商品编码
                ];
            }
        }
        $countMoney = 0; // 发票累计金额,最大值 100,000 (十万)

        foreach ($data['orderDetails'] as $k=>$v){
            //线上课程判断
            if (empty($v['hours'])) {
                $countMoney += floatval($v['moneyperday']*$v['totalday']-$v['coupon_money']);
            } else {
                $countMoney += floatval($v['moneyperday']*$v['hours']-$v['coupon_money']);
            }
            if ($v['coupon_money'] > 0) {
                //含优惠券金额
                $_tmp = [[
                    'goodsname'=>trim($data['goods']),                                    // * 商品名称
                    'num'=>$data['kptype'] == 2 ? -$v['totalday'] : $v['totalday'],                                               // 数量；数量、单价必须都不填，或者都必填，不可只填一个；当数量、单价都不填时，不含税金额、税额、含税金额都必填。建议保留小数点后 8位
                    'price'=>floatval($v['moneyperday']),                                     // 单价；数量、单价必须都不填，或者都必填，不可只填一个；当数量、单价都不填时，不含税金额、税额、含税金额都必填。建议保留小数点后 8位。
                    'hsbz'=>0,                                                      // * 单价含税标志，0:不含税,1:含税
                    'taxrate'=>number_format($data['taxrate'],2),         // * 税率
//                    'spec' => '',                                                   // 规格型号
                    'unit' => '天',                                                  // 单位
                    'spbm'=>$spbm,                                                     // * 税收分类编码
//                    'zsbm' => '',                                                   // 自行编码
                    'fphxz'=>2,                                                      // * 发票行性质:0,正常 行;1,折扣行;2,被折扣行
//                    'yhzcbs' => '',                                                 // 优惠政策标识:0,不使 用;1,使用
//                    'kce'=>'1000',                                                 // 扣除额扣除额，小数点后两位。差额征收的发票目前 只支持一行 明细。不含税差额 =不含税金额- 扣除额；税额 = 不含税差额*税率
//                'taxfreeamt'=>$v['totalday']*$v['moneyperday']-$v['coupon_money'],            // 不含税金额
//                'tax'=> ($v['totalday']*$v['moneyperday']-$v['coupon_money'])*$data['taxrate'],                                                     // 税额
//                'taxamt'=>($v['totalday']*$v['moneyperday'] + $v['expense']-$v['coupon_money'] )* (1+number_format($data['taxrate'],2)) // 含税金额
                ],[
                    'goodsname'=>trim($data['goods']),                                    // * 商品名称
                    'num'=>'',                                               // 数量；数量、单价必须都不填，或者都必填，不可只填一个；当数量、单价都不填时，不含税金额、税额、含税金额都必填。建议保留小数点后 8位
                    'price'=>'',                                       // 单价；数量、单价必须都不填，或者都必填，不可只填一个；当数量、单价都不填时，不含税金额、税额、含税金额都必填。建议保留小数点后 8位。
                    'hsbz'=>0,                                                      // * 单价含税标志，0:不含税,1:含税
                    'taxrate'=>number_format($data['taxrate'],2),         // * 税率
//                    'spec' => '',                                                   // 规格型号
                    'unit' => '天',                                                  // 单位
                    'spbm'=>$spbm,                                                     // * 税收分类编码
//                    'zsbm' => '',                                                   // 自行编码
                    'fphxz'=>1,                                                      // * 发票行性质:0,正常 行;1,折扣行;2,被折扣行
//                    'yhzcbs' => '',                                                 // 优惠政策标识:0,不使 用;1,使用
//                    'kce'=>'1000',                                                 // 扣除额扣除额，小数点后两位。差额征收的发票目前 只支持一行 明细。不含税差额 =不含税金额- 扣除额；税额 = 不含税差额*税率
                    'taxfreeamt'=>-$v['coupon_money'],            // 不含税金额
                    'tax'=> -$v['coupon_money']*$data['taxrate'], // 税额
                    'taxamt'=>-$v['coupon_money']* (1+number_format($data['taxrate'],2)) // 含税金额
                ]];
            } else {
                $_tmp = [
                    'goodsname'=>trim($data['goods']),                                    // * 商品名称
                    'num'=>$data['kptype'] == 2 ? (empty($v['hours']) ? -$v['totalday'] : -$v['hours']) : (empty($v['hours']) ? $v['totalday'] : $v['hours']),                                                   // 数量；数量、单价必须都不填，或者都必填，不可只填一个；当数量、单价都不填时，不含税金额、税额、含税金额都必填。建议保留小数点后 8位
                    'price'=>floatval($v['moneyperday']),                                     // 单价；数量、单价必须都不填，或者都必填，不可只填一个；当数量、单价都不填时，不含税金额、税额、含税金额都必填。建议保留小数点后 8位。
                    'hsbz'=>0,                                                      // * 单价含税标志，0:不含税,1:含税
                    'taxrate'=>number_format($data['taxrate'],2),         // * 税率
//                    'spec' => '',                                                   // 规格型号
                    'unit' => empty($v['hours']) ? '天' : '小时',                                                          // 单位
                    'spbm'=>$spbm,                                                     // * 税收分类编码
//                    'zsbm' => '',                                                   // 自行编码
                    'fphxz'=>0,                                                      // * 发票行性质:0,正常 行;1,折扣行;2,被折扣行
//                    'yhzcbs' => '',                                                 // 优惠政策标识:0,不使 用;1,使用
//                    'kce'=>'1000',                                                 // 扣除额扣除额，小数点后两位。差额征收的发票目前 只支持一行 明细。不含税差额 =不含税金额- 扣除额；税额 = 不含税差额*税率
//                'taxfreeamt'=>$v['totalday']*$v['moneyperday']-$v['coupon_money'],            // 不含税金额
//                'tax'=> ($v['totalday']*$v['moneyperday']-$v['coupon_money'])*$data['taxrate'],                                                     // 税额
//                'taxamt'=>($v['totalday']*$v['moneyperday'] + $v['expense']-$v['coupon_money'] )* (1+number_format($data['taxrate'],2)) // 含税金额
                ];
            }

            if( ($data['adjust'] == -1) && ($v['orderid'] == $maxID) ){
                $_tmp['fphxz'] = 2;
                $tmp[] = $_tmp;
                $tmp[] = $adjustGoods;
                $countMoney -= floatval($data['adjust_num']);
            }else{
                if (isset($_tmp[0])) {
                    //多维
                    $tmp = array_merge($tmp,$_tmp);
                } else {
                    //正常
                    $tmp[] = $_tmp;
                }

            }

        }

        if( ($data['adjust'] == 1) || !empty($adjustGoods) ){
            $tmp[] = $adjustGoods;
            $prices = $adjustGoods['price'];
            $countMoney += floatval($prices);
        }
        // 限制订单详情数目，限制8条
        if( $data['adjust'] == 0 ){
            if( count($tmp) > 8){
                return ['code'=>1,'info'=>'发票详情条目不能超过8条'];
            }
        }else{
            if( count($tmp) > 9){
                return ['code'=>1,'info'=>'发票详情条目不能超过8条'];
            }
        }

        $arr['order']['detail'] = $tmp;
        $order = $this->desdzfp->encrypt($arr);
        if($this->isDev){
            $url = 'http://nnfpdev.jss.com.cn/shop/buyer/allow/cxfKp/cxfServerKpOrderSync.action';   // 测试地址
        }else{
            $url = 'http://nnfp.jss.com.cn/shop/buyer/allow/cxfKp/cxfServerKpOrderSync.action';       // 正式地址
        }
        //初始化
        $post_data = ["order" => $order];


        try{
            $res = $this->vpost($url,$post_data);
            $res = json_decode($res,true);
            if($res['status'] == '0000'){
                // 开票申请成功，返回流水号,此时处于中间状态，需要查询开票结果才能确定是否完成开票！
                return ['code'=>0,'info'=>'成功','data'=>$res['fpqqlsh']];
            }else{
                return ['code'=>1,'info'=>$res['message']];
            }
        }catch (\Exception $e){
            return ['code'=>1,'info'=>$e->getMessage()];
        }
    }

    /**
     * 开票结果查询接口
     * @param string $fpqqlsh
     * @return bool|int|mixed|string
     */
    public function get($fpqqlsh=''){
        $arr = [
            'identity'=>$this->identity,
            'fpqqlsh'=>$fpqqlsh
        ];
        $order = $this->desdzfp->encrypt($arr);
        if($this->isDev){
            $url = 'http://nnfpdev.jss.com.cn/shop/buyer/allow/ecOd/queryElectricKp.action';   // 测试地址
        }else{
            $url = 'http://nnfp.jss.com.cn/shop/buyer/allow/ecOd/queryElectricKp.action';   // 正式地址
        }
        $post_data = ["order" => $order];
        $res = $this->vpost($url,$post_data);
        return !isset($res['errorMsg']) ? json_decode($res,true) : [];
    }

    /**
     * 根据订单号查询发票请求流水号接口
     * @param string $orderno
     * @return bool|int|mixed|string
     */
    public function getByOrderNumber($orderno=[]){
        foreach ($orderno as $k=>$v){
            $orderno[$k] = $this->fppix.$v;
        }
        $arr = [
            'identity'=>$this->identity,
            'orderno'=>$orderno
        ];

        $order = $this->desdzfp->encrypt($arr);
        if($this->isDev){
            $url = 'http://nnfpdev.jss.com.cn/shop/buyer/allow/ecOd/queryElectricKp.action';   // 测试地址
        }else{
            $url = 'https://nnfp.jss.com.cn/shop/buyer/allow/ecOd/queryElectricKp.action';   // 正式地址
        }
        $post_data = ["order" => $order];

        //print_r($post_data);

        $res = $this->vpost($url,$post_data);
        return json_decode($res,true);
    }

    // 取消开票
    public function cancel($fpqqlsh='',$fpdm='',$fphm=''){
        $arr = [
            'identity'=>$this->identity,
            'order'=>['fpdm'=>$fpdm,'fphm'=>$fphm,'fpqqlsh'=>$fpqqlsh]
        ];
        $order = $this->desdzfp->encrypt($arr);
        if($this->isDev){
            $url = 'http://nnfpdev.jss.com.cn/shop/buyer/allow/cxfKp/invalidInvoice.action';
        }else{
            $url = 'http://nnfp.jss.com.cn/shop/buyer/allow/cxfKp/invalidInvoice.action';
        }
        $post_data = ["order" => $order];
        $res = $this->vpost($url,$post_data);
        return json_decode($res,true);
    }

    /*	发送http post请求	*/
    public function vpost($url,$data){ // 模拟提交数据函数
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
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


