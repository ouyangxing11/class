<?php
namespace myclass;

use app\common\model\UkyRead;
use app\miaokeapp\model\UkyDatabase;
use think\Db;
class Notice{
    public static $model=['系统','订单','课程','提现','课代表','开票'];
    public static $columnType=['column'=>'课程','live'=>'直播','vip'=>'会员','bigcolumn'=>'专栏'];
    public static $msg = [
        //pc
        '课程上新提醒'=>['url'=>'marketList','url_type'=>'course','model'=>'课程','title'=>'课程上新提醒','content'=>'内容市场上新啦！新上架课程【num】门，内容市场为您精选优师优课，拓宽市场边界，助力店铺业绩、收入节节攀升，赶快去看看吧！'],
        '课程下架提醒'=>['url'=>'courseManage_index','url_type'=>'','model'=>'课程','title'=>'课程下架提醒','content'=>'由于运营需要，课程【course】已下架，感谢您对该课程的关注与支持。注：若该内容为收费课程，付费用户仍可在个人中心继续收看及学习。'],
        
        '直播上新提醒'=>['url'=>'marketList','url_type'=>'live','model'=>'课程','title'=>'直播上新提醒','content'=>'内容市场上新啦！新上架直播【num】门，内容市场为您精选优师优课，拓宽市场边界，助力店铺业绩、收入节节攀升，赶快去看看吧！'],
        '直播下架提醒'=>['url'=>'live_index','url_type'=>'','model'=>'课程','title'=>'直播下架提醒','content'=>'由于运营需要，直播【course】已下架，感谢您对该直播的关注与支持。注：若该内容为收费直播，付费用户仍可在个人中心继续收看及学习。'],
        
        '会员上新提醒'=>['url'=>'marketList','url_type'=>'vip','model'=>'课程','title'=>'会员上新提醒','content'=>'内容市场上新啦！新上架会员【num】门，内容市场为您精选优师优课，拓宽市场边界，助力店铺业绩、收入节节攀升，赶快去看看吧！'],
        '会员下架提醒'=>['url'=>'vip_index','url_type'=>'','model'=>'课程','title'=>'会员下架提醒','content'=>'由于运营需要，会员【course】已下架，感谢您对该会员的关注与支持。注：若该内容为收费会员，付费用户仍可在个人中心继续收看及学习。'],
        
        '新订单提醒' =>['url'=>'orderDetails','url_type'=>'','model'=>'订单','title'=>'新订单提醒','content'=>'恭喜您！用户【nickname】购买了【type】【course】，购买数量【num】件，店铺获得收益【order_price】元，课代表【user_name】获得收益【price】元，赶快去看看吧！'],
        
        '课代表申请提醒' =>['url'=>'member','url_type'=>'auth','model'=>'课代表','title'=>'新的课代表申请提醒','content'=>'用户【user_name】申请成为课代表，请及时审核。课代表越多，订单成交量越多，赶快去招募课代表吧！'],
        '课代表入驻提醒' =>['url'=>'member','url_type'=>'list','model'=>'课代表','title'=>'新的课代表入驻提醒','content'=>'恭喜您！用户【user_name】已经成为入驻成为课代表，邀请人【nickname】。课代表越多，订单成交量越多，赶快去招募课代表吧！'],
        '课代表申请提现提醒' =>['url'=>'promotion_review','url_type'=>'','model'=>'课代表','title'=>'新的课代表申请提现提醒','content'=>'课代表【user_name】申请提现【price】元，分享是一座天平，你给予他人多少，他人便回报你多少，赶快去审核吧！注：若超过5天未进行提现审核系统将自动通过审核。'],
        
        '店铺提现成功提醒'    =>['url'=>'','url_type'=>'','model'=>'提现','title'=>'店铺提现成功提醒','content'=>'店铺在【date】申请提现【price】元已经到账，相关款项已转账到绑定微信的零钱账户，到账微信账号【nickename】，请及时查收！'],
        '店铺提现失败提醒'    =>['url'=>'','url_type'=>'','model'=>'提现','title'=>'店铺提现失败提醒','content'=>'店铺在【date】申请提现【price】元提现失败，可能提现失败的原因：1.微信账号未实名认证；2.平台付款账户异常；3.提现金额或频率异常；4.其他：微信支付平台原因。建议检查微信实名认证情况，如有疑问请联系平台客服处理！'],
        '店铺提现审核失败提醒'=>['url'=>'','url_type'=>'','model'=>'提现','title'=>'店铺提现审核失败提醒','content'=>'店铺在【date】申请提现【price】元审核未通过，可能审核未通过的原因：1.账户资金异常；2.提现申请信息异常；3.其他：发票相关或其他异常情况。建议检查提现申请信息是否准确，如有疑问请联系平台客服处理！'],

        '新团购需求提醒' =>['url'=>'order_group','url_type'=>'','model'=>'团购','title'=>'新团购需求提醒','content'=>'您有一个团购需求待处理！用户【nickname】提交需求【course】，购买数量【num】件，订单总金额【order_price】元，请联系【user_mobile】确认需求并提交平台处理。'],
        '团购需求通过提醒' =>['url'=>'group_buy','url_type'=>'','model'=>'团购','title'=>'团购需求通过提醒','content'=>'团购需求审核通过！用户【nickname】提交的团购需求【course】已审核通过，【num】张课程券已发放到用户[个人中心-团购课程]，赶快去通知客户吧！'],
        '团购需求驳回提醒' =>['url'=>'order_group','url_type'=>'','model'=>'团购','title'=>'团购需求驳回提醒','content'=>'团购需求已驳回！用户【nickname】提交的团购需求【course】经课师宝平台审核已驳回，如有疑问请及时联系客服处理。'],
        // //h5
        '上架提醒'        =>['url'=>'','url_type'=>'','model'=>'课程','title'=>'新课程上架提醒','content'=>'新【type】【course】已上架，推广成功可获得收益【price】元！推广课程收益多，成交一笔分一笔，赶快去赚钱吧！'],
        //'直播提醒'        =>['url'=>'','url_type'=>'','model'=>'课程','title'=>'直播即将开始提醒','content'=>'您预约的直播课程【直播经济—播商思维与人人短视频营销】即将开始，点击查看详情马上进入直播间！'],
        '课程提醒'        =>['url'=>'','url_type'=>'','model'=>'课程','title'=>'课程章节已更新','content'=>'您【type】的课程【course】章节已更新，赶快去学习吧！'],

        '开票成功提醒'    =>['url'=>'','url_type'=>'','model'=>'开票','title'=>'您提交的订单开票申请已通过','content'=>'您在【date】提交的订单开票申请已通过，发票将以邮件形式发送到您预留的接收邮箱，请您及时查收！因部分邮箱的自动拦截功能，若收件箱未查看到相关邮件，请检查垃圾邮件信箱。'],
        '开票失败提醒'    =>['url'=>'','url_type'=>'','model'=>'开票','title'=>'您提交的订单开票申请未通过','content'=>'您在【date】提交的订单开票申请，当前开票状态为【已撤销】，如有疑问请及时联系店铺管理员处理。'],

        //'订单提醒'        =>['url'=>'','url_type'=>'','model'=>'订单','title'=>'店铺成交了新的订单','content'=>'店铺成交了新的订单，获得收益【price】元！用户【nickname】在您的店铺购买了【course】，成交金额【order_price】元。分享精彩课程，成交多多，收益多多，赶快去赚钱吧！'],
        '推广订单提醒'    =>['url'=>'','url_type'=>'','model'=>'订单','title'=>'您有新的推广订单','content'=>'恭喜您获得课代表收益【price】元！【nickname】购买【course】，订单金额【order_price】元，当前您已累计获得课代表收益【total_price】元。推广课程收益多，成交一笔分一笔，赶快去赚钱吧！'],
        
        '邀请成功提醒'    =>['url'=>'','url_type'=>'','model'=>'课代表','title'=>'课代表邀请成功','content'=>'课代表邀请成功！微信用户【nickname】通过您的分享招募申请课代表成功，点击查看详情。'],
        
        '提现成功提醒'    =>['url'=>'','url_type'=>'','model'=>'提现','title'=>'您的提现申请已成功','content'=>'您【date】提交的提现申请已到账！到账金额【price】元，请及时查看您的微信零钱账户。推广课程收益多，成交一笔分一笔，赶快去赚钱吧！'],
        '提现失败提醒'    =>['url'=>'','url_type'=>'','model'=>'提现','title'=>'您申请的提现到账失败','content'=>'您【date】申请提现【price】元，当前申请结果为【提现失败】，请联系店铺或重新发起提现。'],
        '提现审核失败提醒'=>['url'=>'','url_type'=>'','model'=>'提现','title'=>'您申请的提现审核不通过','content'=>'您【date】申请提现【price】元，当前申请结果为【提现审核未通过】。如有疑问，请及时联系店铺管理员处理。'],

        '自有课程上架提醒'=>['url'=>'courseManage_index','url_type'=>'','model'=>'课程','title'=>'自有课程上架提醒','content'=>'您申请到内容市场的课程【course】已上架，内容市场为您拓宽市场边界，助力店铺业绩、收入节节攀升，赶快去看看吧！'],
        '自有课程审核未通过提醒'=>['url'=>'courseManage_index','url_type'=>'','model'=>'课程','title'=>'自有课程审核未通过提醒','content'=>'很抱歉，您申请到内容市场的课程【course】审核未通过，驳回理由：【reason】。快去修该课程内容重新申请吧！'],
        '自有课程下架提醒'=>['url'=>'courseManage_index','url_type'=>'','model'=>'课程','title'=>'自有课程下架提醒','content'=>'很抱歉，您申请到内容市场的课程【course】已下架，原因：该课程中有违反课师宝内容市场商品上架规则的内容，如色情、涉暴、涉政、营销广告（手机号、公司LOGO、公众号二维码、营销网站等）等违规内容。'],
        '自有课程删除提醒'=>['url'=>'courseManage_index','url_type'=>'','model'=>'课程','title'=>'自有课程删除提醒','content'=>'很抱歉，您申请到内容市场的课程【course】已被删除，原因：该课程中有违反课师宝内容市场商品上架规则的内容，如色情、涉暴、涉政、营销广告（手机号、公司LOGO、公众号二维码、营销网站等）等违规内容。'],

        '课程禁用提醒' => ['url'=>'','url_type'=>'','model'=>'课程','title' => '课程禁用提醒','content'=>'您的自有课程【title】因违反【商品上架规则】已被禁用，备注：【tag】。'],
        '课程启用提醒' => ['url'=>'','url_type'=>'','model'=>'课程','title' => '课程启用提醒','content'=>'恭喜您，您申请启用的课程【title】审核已通过，审核员会尽快帮您进行上架，请耐心等待！'],
        '章节禁用提醒' => ['url'=>'','url_type'=>'','model'=>'课程','title' => '章节禁用提醒','content'=>'您的自有课程【title】【chapter】章节因违反「商品上架规则」已被禁用，备注：【tag】。'],
        '章节启用提醒' => ['url'=>'','url_type'=>'','model'=>'课程','title' => '章节启用提醒','content'=>'恭喜您，您申请启用的章节【title】【chapter】章审核已通过，审核员会尽快帮您进行上架，请耐心等待！'],
        '课程禁用提醒1' => ['url'=>'','url_type'=>'','model'=>'课程','title' => '课程禁用提醒','content'=>'您上架的课程【title】因违反「商品上架规则」已被禁用。'],
        '课程启用提醒1' => ['url'=>'','url_type'=>'','model'=>'课程','title' => '课程启用提醒','content'=>'您好，禁用的课程【title】修改审核通过，审核员会尽快上架，请耐心等候！'],
        
        '新增报名提醒' => ['url'=>'meeting_details','url_type'=>'user','model'=>'活动','title' => '新增报名提醒','content'=>'活动【course】有新的报名！客户信息【username-mobile】[company-duty]，敬请留意！'],
        '报名审核提醒' => ['url'=>'meeting_details','url_type'=>'check','model'=>'活动','title' => '报名审核提醒','content'=>'报名信息待审核！客户信息【username-mobile】[company-duty]，报名活动【course】，请注意及时处理！'],
        '新需求提醒' => ['url'=>'meeting_details','url_type'=>'group','model'=>'活动','title' => '新需求提醒','content'=>'您有一个团购需求待处理！用户【nickname】提交需求【course】，购买数量【num】件，订单总金额【order_price】元，客户信息【user_mobile】，报名信息【公司名称：company；职位：duty】，请注意及时处理！'],
        '课代表新增报名提醒' => ['url'=>'','url_type'=>'','model'=>'活动','title' => '新增报名提醒','content'=>'您有新的客户报名！客户信息【username-mobile】[company-duty]，报名活动【course】，敬请留意！'],
        '报名成功通知' => ['url'=>'','url_type'=>'','model'=>'活动','title' => '报名成功通知','content'=>'您报名的活动【course】审核已通过，活动时间【date】，活动地点【address】，届时请出示参会凭证进行签到。'],
        '报名驳回通知' => ['url'=>'','url_type'=>'','model'=>'活动','title' => '报名驳回通知','content'=>'您报名的活动【course】已被审核驳回，驳回原因【note】，相关报名费用将在1-7个工作日内原路退回，请注意查收。如有疑问请联系工作人员。'],
        '报名取消通知' => ['url'=>'','url_type'=>'','model'=>'活动','title' => '报名取消通知','content'=>'您报名的活动【course】已被取消报名，相关报名费用将在1-7个工作日内原路退回，请注意查收。如有疑问请联系工作人员。'],

    ];
    public function demo(){
        $toUser = 14;//用户id,0为店铺消息
        $dataid = 1;//关联的数据id ，没有为0
        $memberid=1;//机构id
        $re =  Notice::noticeInsert($toUser,$memberid,'开票成功提醒',
        [
            'price'=>"98.00",
            'nickname'=>'微信昵称',
            'course'=>'课程名称',
            'order_price'=>"998.00",
            'total_price'=>"9999.99",
        ],$dataid);
        return $re;
    }
    public static function newRedis(){
     
        $redis = new \Redis();
        $redis->connect('106.52.69.140', 6379);
        $redis->auth('hsjj!@#$%ok');
        //$redis->select(3);//正式用3
        $redis->select(2);//正式用3
        return $redis;
    }
    //机构所有的员工
    public static function memberUser($memberid=''){
        
        $KSB = new UkyDatabase();
        $table = $KSB->table('dt_user');
        $memberid>0 && $table->where('memberid','=',$memberid);
        $userList = $table->where('deleted','=',0)->where('isstaff','=',1)->field('id,memberid')->select();
        return $userList;
    }
    //新消息,给机构所有员工发，批量
    public static function noticeInsertAll($memberid,$type,$data='',$dataid=0){
        
        $userList = self::memberUser($memberid);
        foreach($userList as $val){
            self::noticeInsert($val['id'],$val['memberid'],$type,$data,$dataid);
        }
        return true;
    }
    //新消息
    //toUserid 用户id,type 消息类型 中文,data 动态内容数据，替换，dataid 关联表数据id
    public static function noticeInsert($toUserid,$memberid,$type,$content=[],$dataid=0){
        $data['userid']   = $toUserid;
        $data['dataid']   = $dataid;
        $data['type']     = $type;
        $data['memberid'] = $memberid;
        $data['model']    = self::$msg[$type]['model'];
        $data['title']    = self::$msg[$type]['title'];
        $data['content']  = self::$msg[$type]['content'];
        foreach($content as $key=>$val){
            $data['content']  = str_ireplace("【".$key."】","【".$val."】",$data['content']);
            $data['content'] = str_ireplace("[" . $key . "]",  $val , $data['content']);
        }
        $data['read']     = 0;
        $data['addtime']  = date('Y-m-d H:i:s');
        $redis = self::newRedis();
        $noticeid = $redis->get('notice-id');
        if(!$noticeid){
            $KSB = new UkyDatabase();
            $noticeid = $KSB->table('dt_notice')->max('id');
            $noticeid && $redis->set('notice-id',$noticeid);
        }
        $data['id'] = $redis->incr('notice-id');
        $redis->rpush('notice-list',json_encode($data)); 
        return true;
    }
    //订单提醒
    public static function noticeOrder($orderInfo){
        
        $KSB = new UkyDatabase();
        $type = isset(self::$columnType[$orderInfo['type']])?self::$columnType[$orderInfo['type']]:'课程';
        $userInfo = $KSB->table('dt_user')->where('id','=',$orderInfo['userid'])->field('nickname')->find();
        $promoterInfo = $KSB->table('dt_user')->where('id','=',$orderInfo['promoter_id'])->field('nickname')->find();
        //机构端，新订单提醒
        self::noticeInsertAll($orderInfo['memberid'],'新订单提醒',
        ['nickname'=>$userInfo['nickname'],'type'=> $type,
        'course'=>$orderInfo['name'],'num'=>$orderInfo['num'],
        'order_price'=>$orderInfo['member_meet'],
        'user_name'=>$promoterInfo?$promoterInfo['nickname']:'无','price'=>$promoterInfo?$orderInfo['promoter_meet']:'0',
        ],
        $orderInfo['id']);
        //H5，推广订单提醒
        if($promoterInfo && 0<$orderInfo['promoter_meet']){
            $price = $KSB->table('dt_promoter_money')->where('promoter_id','=',$orderInfo['promoter_id'])->where('type','=',1)->sum('income');
            self::noticeInsert($orderInfo['promoter_id'],$orderInfo['memberid'],'推广订单提醒',
            ['price'=>$orderInfo['promoter_meet'],'nickname'=>$userInfo['nickname'],'course'=>$orderInfo['name'],
            'order_price'=>$orderInfo['price'],'total_price'=>$price],
            $orderInfo['id']);
        }
    }
    //下架通知
    public static function liveDownNotice($id,$course,$type){
        
        if($type=='chapter'){return true;}
        $KSB = new UkyDatabase();
        $table = $KSB->table('dt_member m');
        $table->join("dt_user u","u.memberid=m.id");
        $table->join("(select memberid from dt_basket where column_id=$id and type='$type') b","b.memberid=m.id");
        $table->where('m.deleted','=',0);
        $table->where('u.deleted','=',0);
        $table->where('u.isstaff','=',1);
        $table->field('m.id,u.id as userid');
        $list = $table->select();
        if($type=='column'){
            $title = '课程下架提醒';
        }elseif($type=='live'){
            $title = '直播下架提醒';
        }elseif($type=='vip'){
            $title = '会员下架提醒';
        }
        foreach($list as $val){
            Notice::noticeInsert($val['userid'],$val['id'],$title,['course'=>$course],$id);
        }
        return true;
    }
    //课程更新通知
    public static function chapterUpNotice($info,$type){ 
        if($type!='chapter'){return true;}
        $ksb = new UkyRead();
        $id = $info['column_id'];
        $title = $ksb->table('dt_column')->where('id','=',$id )->value('title');
        $buyList = $ksb->table('dt_user_buys')
        ->where('dataid','=',$id)
        ->where('expiretime','>',date('Y-m-d H:i:S'))
        ->group('user_id')
        ->column("user_id,memberid,dataid,'购买' as type",'user_id');
        $collectList = $ksb->table('dt_live_collect')
        ->where('did','=',$id)
        ->where('deleted','=',0)
        ->group('userid')
        ->column("userid as user_id,memberid,did as dataid,'收藏' as type",'userid');
        foreach($buyList as $val){
            Notice::noticeInsert($val['user_id'],$val['memberid'],'课程提醒',['course'=>$title,'type'=>$val['type']],$info['column_id']);
            if(isset($collectList[$val['user_id']]))unset($collectList[$val['user_id']]);
        }
        foreach($collectList as $val){
            Notice::noticeInsert($val['user_id'],$val['memberid'],'课程提醒',['course'=>$title,'type'=>$val['type']],$info['column_id']);
        }
        return true;
    }
}