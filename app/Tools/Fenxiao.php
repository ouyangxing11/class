<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/14
 * Time: 20:41
 */
namespace myclass;
use think\Db;
use app\miaokeapp\model\UkyDatabase;

class Fenxiao
{

    public function money($trade_no){
        $db = new UkyDatabase();
        $order = $db->table("dt_live_order")->where("trade_no",$trade_no)->where("deleted=0")->find()->toArray();
        if(!$order) return 0;
        $datainfo = $db->table("dt_column")->where("id",$order["dataid"])->find()->toArray();
        $db->startTrans();
        if($order["type"]=="vip"){
            return $this->vip_back($order,$datainfo);
        }else{
            $percent_config = $this->get_percent($order,$datainfo);
            $ksb_prcent = $percent_config["ksb"];//ksb平台分成比例
            $neirong_percent = $percent_config["neirong"];//内容提供方分成比例
            $qudao_percent = $percent_config["qudao"];//渠道售卖方分成比例
            $res1 = $this->hs_money($order,$ksb_prcent);
            $promoter = $this->shop_model($order["memberid"],$order["type"],$order["dataid"],$order["userid"],$order["promoter_id"]);
            $res3 = $res2 = 1;
            $pro_ticket = "";
            $member_money = $pro_money = $promoter_id = 0;
            $shop_income = (int)($order["price"]*$qudao_percent)/100;
            if(!$promoter){//没有开启推广
                $member_money = $shop_income;
                $res2 = $this->shop_money($order,$qudao_percent,$shop_income);
            }else{
                $promoter_id = $promoter["tui"];
                $percent = $promoter["percent"];
                if($promoter_id>0){//有推广
                    $res3 = $this->promoter_money($order,$promoter_id,$percent);
                    $pro_money = (int)($order["price"]*$percent)/100;
                    if($pro_money>0){
                        //加密课代表可提现余额
                        $json['userid'] = $promoter_id;
                        $json['money'] = $pro_money;
                        $pro_ticket = think_encrypt(json_encode($json),'91renrenshi');
                    }
                    $res2 = $this->shop_money($order,$qudao_percent-$percent,$shop_income);
                    $member_money = (int)($order["price"]*$qudao_percent-$order["price"]*$percent)/100;
                }else{//无推广
                    $member_money = (int)($order["price"]*$datainfo["popularize"])/100;
                    $res2 = $this->shop_money($order,$qudao_percent,$shop_income);
                }
            }
            if($neirong_percent){//
                $res4 = $this->shop_part_money($order,$neirong_percent);
            }
            //加密机构余额
            $json['userid'] = $order["memberid"];
            $json['money'] = $member_money;//之前的加本次的收入
            $member_ticket = think_encrypt(json_encode($json),'91renrenshi');

            $t['orderid'] = intval($order['id']);
            $t['member_meet'] = floatval($member_money);
            $t['member_nhr'] = floatval(0);  //当前实付加密
            $t['promoter_meet'] = floatval($pro_money);
            $t['promoter_nhr'] = floatval(0);
            $ticket = think_encrypt(json_encode($t),'91renrenshi');
            $update_order = [
                "promoter_id"=>$promoter_id,
                "reality_price"=>$order["num"]*$order["one_price"],//订单总价
                "member_meet"=>$member_money,//机构应付
                "member_ticket"=>$member_ticket,//机构加密串
                "member_nhr"=>0,
                "promoter_meet"=>$pro_money,//课代表应付
                "promoter_ticket"=>$pro_ticket,//课代表加密串
                "promoter_nhr"=>0,
                "paystatus"=>1,
                "ticket"=>$ticket
            ];
            $db->table("dt_live_order")->where("id",$order["id"])->update($update_order);
//            $back = new \myclass\Payback();
//            $sus = $back->money($order["id"]);

            if($res1 && $res2 && $res3){
                $db->commit();
                return true;
            }else{
                $db->rollback();
                return 0;
            }
        }

        return true;
        $order = $db->table("dt_live_order")
            ->where("trade_no",$trade_no)
            ->where("deleted=0")
            ->find()->toArray();
        if(!$order) return 0;
        $datainfo = $db->table("dt_column")->where("id",$order["dataid"])->find()->toArray();
        $hsmoney = [
            "orderid"=>$order["id"],
            "userid"=>$order["userid"],
            "price"=>$order["price"],
            "income"=>(int)($order["price"]*(100-$datainfo["popularize"]))/100,
            "popularize"=>100-$datainfo["popularize"],
            "addtime"=>date("Y-m-d H:i:s"),
            "offline"=>1
        ];
        $shop_income = (int)($order["price"]*$datainfo["popularize"])/100;
        $pro_ticket = "";
        $cash_type=2;//线下团购
        $db->startTrans();
        $res1 =  $db->table("dt_shop_hsmoney")->insert($hsmoney);
        $promoter = $this->shop_model($order["memberid"],$order["type"],$order["dataid"],$order["userid"],$order["promoter_id"]);
        $res3 = $res2 = 1;
        $pro_money = $promoter_id = 0;
        if(!$promoter){//没有开启推广
            $shopmoeny[] = [
                "orderid"=>$order["id"],
                "userid"=>$order["userid"],
                "title"=>"收入",
                "memberid"=>$order["memberid"],
                "price"=>$order["price"],
                "income"=>(int)($order["price"]*$datainfo["popularize"])/100,
                "shop_income"=>$shop_income,
                "trade_no"=>"",
                "cash_price"=>(int)($order["price"]*$datainfo["popularize"])/100,
                "popularize"=>$datainfo["popularize"],
                "cash_type"=>$cash_type,
                "status"=>1,
                "type"=>1,
                "addtime"=>date("Y-m-d H:i:s")
            ];
            $shopmoeny[] = [
                "orderid"=>$order["id"],
                "userid"=>0,
                "title"=>"线下提现",
                "memberid"=>$order["memberid"],
                "price"=>(int)($order["price"]*$datainfo["popularize"])/100,
                "income"=>0-((int)($order["price"]*$datainfo["popularize"])/100),
                "shop_income"=>0,
                "trade_no"=>$trade_no,
                "cash_price"=>0,
                "popularize"=>0,
                "cash_type"=>4,//线下提现默认提了
                "status"=>1,//提现成功
                "type"=>2,
                "addtime"=>date("Y-m-d H:i:s")
            ];
            $member_money = (int)($order["price"]*$datainfo["popularize"])/100;
//            if($shopmoeny["income"]>0)
            $res2 =  $db->table("dt_shop_money")->insertAll($shopmoeny);
        }else{
            $promoter_id = $promoter["tui"];
            $percent = $promoter["percent"];
            if($promoter_id>0){//有推广
                $user_money = [
                    "orderid"=>$order["id"],
                    "userid"=>$order["userid"],
                    "promoter_id"=>$promoter_id,
                    "memberid"=>$order["memberid"],
                    "price"=>$order["price"],
                    "income"=>(int)($order["price"]*$percent)/100,
                    "cash_price"=>(int)($order["price"]*$percent)/100,
                    "popularize"=>$percent,
                    "cash_type"=>$cash_type,
                    "type"=>1,
                    "status"=>1,
                    "addtime"=>date("Y-m-d H:i:s")
                ];
                $pro_money = $user_money["income"];
                if($user_money["income"]*100>0){
                    //加密课代表可提现余额
                    $json['userid'] = $promoter_id;
                    $json['money'] = $pro_money;
                    $pro_ticket = think_encrypt(json_encode($json),'91renrenshi');
                    $res3 =  $db->table("dt_promoter_money")->insert($user_money);
                }
                $shopmoeny[] = [
                    "orderid"=>$order["id"],
                    "userid"=>$order["userid"],
                    "title"=>"收入",
                    "memberid"=>$order["memberid"],
                    "price"=>$order["price"],
                    "income"=>(int)($order["price"]*$datainfo["popularize"]-$order["price"]*$percent)/100,
                    "shop_income"=>$shop_income,
                    "cash_price"=>(int)($order["price"]*$datainfo["popularize"]-$order["price"]*$percent)/100,
                    "trade_no"=>"",
                    "popularize"=>$datainfo["popularize"]-$percent,
                    "cash_type"=>$cash_type,
                    "type"=>1,
                    "status"=>1,
                    "addtime"=>date("Y-m-d H:i:s")
                ];
                $shopmoeny[] = [
                    "orderid"=>$order["id"],
                    "userid"=>0,
                    "title"=>"线下提现",
                    "memberid"=>$order["memberid"],
                    "price"=>(int)($order["price"]*$datainfo["popularize"]-$order["price"]*$percent)/100,
                    "income"=>0-((int)($order["price"]*$datainfo["popularize"]-$order["price"]*$percent)/100),
                    "shop_income"=>0,
                    "cash_price"=>0,
                    "trade_no"=>$trade_no,
                    "popularize"=>0,
                    "cash_type"=>4,
                    "type"=>2,
                    "status"=>1,
                    "addtime"=>date("Y-m-d H:i:s")
                ];
                $member_money = (int)($order["price"]*$datainfo["popularize"]-$order["price"]*$percent)/100;
//                if($shopmoeny["income"]>0)
                $res2 = $db->table("dt_shop_money")->insertAll($shopmoeny);
            }else{//无推广
                $shopmoeny[] = [
                    "orderid"=>$order["id"],
                    "userid"=>$order["userid"],
                    "title"=>"收入",
                    "memberid"=>$order["memberid"],
                    "price"=>$order["price"],
                    "income"=>((int)($order["price"]*$datainfo["popularize"])/100),
                    "shop_income"=>$shop_income,
                    "cash_price"=>((int)($order["price"]*$datainfo["popularize"])/100),
                    "popularize"=>$datainfo["popularize"],
                    "cash_type"=>$cash_type,
                    "trade_no"=>"",
                    "type"=>1,
                    "status"=>1,
                    "addtime"=>date("Y-m-d H:i:s")
                ];
                $shopmoeny[] = [
                    "orderid"=>$order["id"],
                    "userid"=>0,
                    "title"=>"线下提现",
                    "memberid"=>$order["memberid"],
                    "price"=>$order["price"],
                    "income"=>0-((int)($order["price"]*$datainfo["popularize"])/100),
                    "shop_income"=>0,
                    "cash_price"=>0,
                    "popularize"=>0,
                    "cash_type"=>4,
                    "trade_no"=>$trade_no,
                    "type"=>2,//提现
                    "status"=>1,
                    "addtime"=>date("Y-m-d H:i:s")
                ];
                $member_money = (int)($order["price"]*$datainfo["popularize"])/100;
//                if($shopmoeny["income"]>0)
                $res2 = $db->table("dt_shop_money")->insertAll($shopmoeny);
            }
        }

        //加密机构余额
        $json['userid'] = $order["memberid"];
        $json['money'] = $member_money;//之前的加本次的收入
        $member_ticket = think_encrypt(json_encode($json),'91renrenshi');

        $t['orderid'] = intval($order['id']);
        $t['member_meet'] = floatval($member_money);
        $t['member_nhr'] = floatval(0);  //当前实付加密
        $t['promoter_meet'] = floatval($pro_money);
        $t['promoter_nhr'] = floatval(0);
        $ticket = think_encrypt(json_encode($t),'91renrenshi');
        $update_order = [
            "promoter_id"=>$promoter_id,
            "reality_price"=>$order["num"]*$order["one_price"],//订单总价
            "member_meet"=>$member_money,//机构应付
            "member_ticket"=>$member_ticket,//机构加密串
            "member_nhr"=>0,
            "promoter_meet"=>$pro_money,//课代表应付
            "promoter_ticket"=>$pro_ticket,//课代表加密串
            "promoter_nhr"=>0,
            "paystatus"=>1,
            "audit"=>2,
            "ticket"=>$ticket//审核通过

        ];
        $db->table("dt_live_order")->where("id",$order["id"])->update($update_order);
//        $back = new \myclass\Payback();
//        $sus = $back->money($order["id"]);
        if($res1 && $res2 && $res3){
            $db->commit();
            return true;
        }else{
            $db->rollback();
            return 0;
        }

    }




    //店铺模式
    public function shop_model($shopid,$type,$dataid,$userid,$promoteid){
        $db = new UkyDatabase();
        $shop =  $db->table("dt_promoter_config")->where("memberid",$shopid)->find();
        if($shop)$shop=$shop->toarray();
        $item =  $db->table("dt_promoter_goods")->where("memberid",$shopid)
            ->where("dataid",$dataid)->find();
        if($item)$item=$item->toArray();

        if(!$shop || (isset($shop["open_promoter"]) && $shop["open_promoter"]==0))return false;
        if(!$item || (isset($item["isopen"]) && $item["isopen"]==0))return false;
        $myself = $db->table("dt_promoter_user")->where("userid",$userid)->where("status",1)->find();
        if($myself)$myself=$myself->toarray();
        if($shop["open_self"] && $myself)$shop["retail_type"] =4;//自购分佣
        if(!$shop["open_self"] && $myself)$shop["retail_type"] =5;//自购不分佣
        switch ($shop["retail_type"]){
            case 1://竞争模式，谁是推广员就给谁分钱
                $tui = $promoteid;
                break;
            case 2://保护模式，只要有单就一直是客户
                $tui = $this->protect_model($shop["memberid"],$userid,$promoteid);
                break;
            case 3://自定义模式,有效期限内是客户，下单重置有效期
                $tui = $this->customize_model($shop,$userid,$promoteid);
                break;
            case 4://自购分佣
                $tui = $this->self_money($userid);
                break;
            case 5://自购不分佣
                $tui = 0;
                break;
            default:
                $tui = 0;
        }
        $percent = $item["popularize"];
        //购买者是否是推广员--自购分佣
//        $myself = $db->table("dt_promoter_user")->where("userid",$userid)->where("status",1)->find();
//        if($shop["open_promoter"] && $shop["open_self"]==1 && $myself &&$item["isopen"]==1) $tui= $userid;
//        if($shop["open_promoter"] && $shop["open_self"]==0 && $myself) $tui= 0;
        $user_shop = $db->table("dt_user")->where("id",$userid)->value("memberid");
        $tui_shop = $db->table("dt_user")->where("id",$tui)->value("memberid");
        if($user_shop!=$tui_shop) $tui=0;
        $is_promoter = $db->table("dt_promoter_user")->where("userid",$tui)->where("status",1)->find();
        if(!$is_promoter)$tui = 0;
        return ["tui"=>$tui,"percent"=>$percent];
    }


    public function self_money($userid){
        $db = new UkyDatabase();
        $myself = $db->table("dt_promoter_user")->where("userid",$userid)->where("status",1)->find();
        if($myself) {
            $tui= $userid;
        }else{
            $tui = 0;
        }
        return $tui;
    }

    //保护模式需要做的
    public function protect_model($shopid,$userid,$promoteid){
        $db = new UkyDatabase();
        $exist = $db->table("dt_promoter_bind")
            ->where("memberid",$shopid)
            ->where("userid",$userid)
            ->where("expire_time",">",date("Y-m-d H:i:s"))
            ->where("status",0)->find();
        if($exist){
            $exist = $exist->toArray();
            return $exist["promoter_id"];
        }else{
            $is_promoter = $db->table("dt_promoter_user")->where("userid",$promoteid)->where("status",1)
                ->find();
            if(!$is_promoter) return 0;
            if($promoteid<=0) return 0;
            $data = [
                "memberid"=>$shopid,
                "userid"=>$userid,
                "promoter_id"=>$promoteid,
                "bind_time"=>date("Y-m-d H:i:s"),
                "status"=>0,
                "expire_time"=>"2100-01-01 00:00:00",
                "expire_days"=>0
            ];
            $res = $db->table("dt_promoter_bind")->insert($data);
            return $promoteid;
        }
    }

    //自定义模式需要做的
    public function customize_model($shop,$userid,$promoteid){
        $db = new UkyDatabase();

        $exist = $db->table("dt_promoter_bind")->where("memberid",$shop["memberid"])
            ->where("userid",$userid)
            ->where("status",0)
            ->order("expire_time desc")
            ->find();
        $days = $shop["protect_day"];
        $time = date('Y-m-d H:i:s',strtotime("+$days day"));
        if($exist){
            $exist = $exist->toArray();
            if($exist["expire_time"]>date("Y-m-d H:i:s")){//未过期,之前是谁的客户还是谁的
                $res = $db->table("dt_promoter_bind")->where("id",$exist["id"])
                    ->update(["expire_time"=>$time,"expire_days"=>$days]);
                return $exist["promoter_id"];
            }else{//已过期,更新新的推广员id
                if($promoteid==$exist["promoter_id"]){//还是我推广的，更新过期时间
                    $res = $db->table("dt_promoter_bind")->where("id",$exist["id"])
                        ->update(["expire_time"=>$time,"expire_days"=>$days]);
                }else{//新的推广员
                    if($promoteid<=0) return 0;
                    $is_promoter = $db->table("dt_promoter_user")
                        ->where("userid",$promoteid)
                        ->where("status",1)
                        ->find();
                    if(!$is_promoter) return 0;
                    $data = [
                        "memberid"=>$shop["memberid"],
                        "userid"=>$userid,
                        "promoter_id"=>$promoteid,
                        "bind_time"=>date("Y-m-d H:i:s"),
                        "status"=>0,
                        "expire_time"=>$time,
                        "expire_days"=>$days
                    ];
                    $res = $db->table("dt_promoter_bind")->insert($data);
                }
                return $promoteid;
            }
        }else{
            $req = $db->table("dt_promoter_bind")->where("memberid",$shop["memberid"])
                ->where("userid",$userid)
                ->where("promoter_id",$promoteid)
                ->order("expire_time desc")
                ->find();
            if($req){
                $req = $req->toArray();
                $data = [
//                    "promoter_id"=>$promoteid,
                    "bind_time"=>date("Y-m-d H:i:s"),
                    "status"=>0,
                    "expire_time"=>$time,
                    "expire_days"=>$days
                ];
                $res = $db->table("dt_promoter_bind")->where("id",$req["id"])
                    ->update($data);
                return $promoteid;
            }else{
                if($promoteid<=0) return 0;
                $is_promoter = $db->table("dt_promoter_user")->where("userid",$promoteid)->where("status",1)->find();
                if(!$is_promoter) return 0;
                $data = [
                    "memberid"=>$shop["memberid"],
                    "userid"=>$userid,
                    "promoter_id"=>$promoteid,
                    "bind_time"=>date("Y-m-d H:i:s"),
                    "status"=>0,
                    "expire_time"=>$time,
                    "expire_days"=>$days
                ];
                $res = $db->table("dt_promoter_bind")->insert($data);
                return $promoteid;
            }
        }

    }







    //会员回调
    public function vip_back($order,$datainfo){
        //是否走推广
        $db = new UkyDatabase();
        $promoter = $this->shop_model($order["memberid"],$order["type"],$order["dataid"],$order["userid"],$order["promoter_id"]);
        $stock = $this->vip_stock($order);
//        $percent_config = $this->get_percent($order,$datainfo);
//        $ksb_prcent = $percent_config["ksg"];//ksb平台分成比例
//        $neirong_percent = $percent_config["neirong"];//内容提供方分成比例
//        $qudao_percent = $percent_config["qudao"];//渠道售卖方分成比例
        $promoter_id = $pro_money =0;
        $pro_ticket = "";
        $res1 = $res3 = 1;
        //有库存，华师不分钱
        if($stock){
            $shop_income = (int)($order["price"]*100)/100;
            if(!$promoter) {
                $res2 = $this->shop_money($order,100,$shop_income);
                $member_money = (int)($order["price"]*100)/100;
            }else{
                $promoter_id = $promoter["tui"];
                $percent = $promoter["percent"];
                if($promoter_id>0){
                    $res3 = $this->promoter_money($order,$promoter_id,$percent);
                    $res2 = $this->shop_money($order,100-$percent,$shop_income);
                    $member_money = (int)($order["price"]*(100-$percent))/100;
                    $pro_money = (int)($order["price"]*$percent)/100;
                    if($pro_money*100>0){
                        //加密课代表可提现余额
                        $json['userid'] = $promoter_id;
                        $json['money'] = $pro_money;
                        $pro_ticket = think_encrypt(json_encode($json),'91renrenshi');
                    }
                }else{
                    $res2 = $this->shop_money($order,100,$shop_income);
                    $member_money = (int)($order["price"]*100)/100;
                }
            }
        }else{
            $pre_pay = $db->table("dt_member_prepay")
                ->where("memberid",$order["memberid"])
                ->where("dataid",$order["dataid"])
                ->where("status",1)
                ->max("popularize");
            if(!$pre_pay) {
                $populize = $order["popularize"];//拿新的等级比例
            }else{
                $populize = $pre_pay;
                $this->auto_upgrade($order["memberid"],$order["level_type"],$order["dataid"]);//自动升级会员等级
            }
            $shop_income = (int)($order["price"]*$populize)/100;
            $res1 = $this->hs_money($order,100-$populize);
            if(!$promoter) {
                $res2 = $this->shop_money($order,$populize,$shop_income);
                $member_money = (int)($order["price"]*$populize)/100;
            }else{
                $promoter_id = $promoter["tui"];
                $percent = $promoter["percent"];
                if($promoter_id>0){
                    $res3 = $this->promoter_money($order,$promoter_id,$percent);
                    $res2 = $this->shop_money($order,$populize-$percent,$shop_income);
                    $member_money = (int)($order["price"]*($populize-$percent))/100;
                    $pro_money = (int)($order["price"]*$percent)/100;
                    if($pro_money*100>0){
                        //加密课代表可提现余额
                        $json['userid'] = $promoter_id;
                        $json['money'] = $pro_money;
                        $pro_ticket = think_encrypt(json_encode($json),'91renrenshi');
                    }
                }else{
                    $res2 = $this->shop_money($order,$populize,$shop_income);
                    $member_money = (int)($order["price"]*$populize)/100;
                }
            }
        }

        //加密机构余额
        $json['userid'] = $order["memberid"];
        $json['money'] = $member_money;//之前的加本次的收入
        $member_ticket = think_encrypt(json_encode($json),'91renrenshi');

        $t['orderid'] = intval($order['id']);
        $t['member_meet'] = floatval($member_money);
        $t['member_nhr'] = floatval(0);  //当前实付加密
        $t['promoter_meet'] = floatval($pro_money);
        $t['promoter_nhr'] = floatval(0);
        $ticket = think_encrypt(json_encode($t),'91renrenshi');
        $update_order = [
            "promoter_id"=>$promoter_id,
            "reality_price"=>$order["num"]*$order["one_price"],//订单总价
            "member_meet"=>$member_money,//机构应付
            "member_ticket"=>$member_ticket,//机构加密串
            "member_nhr"=>0,
            "promoter_meet"=>$pro_money,//课代表应付
            "promoter_ticket"=>$pro_ticket,//课代表加密串
            "promoter_nhr"=>0,
            "paystatus"=>1,
            "ticket"=>$ticket
        ];
        $db->table("dt_live_order")->where("id",$order["id"])->update($update_order);
//        $back = new \myclass\Payback();
//        $sus = $back->money($order["id"]);
        if($res1 && $res2 && $res3){
            $db->commit();
            return 1;
        }else{
            $db->rollback();
            return 0;
        }
    }


    //是否还有会员库存
    public function vip_stock($order){
        $db = new UkyDatabase();
        $level_type = $order["level_type"];
        $field = "level".$level_type."_rest";
        $stock = $db->table("dt_member_stock")->where("dataid",$order["dataid"])
            ->where("memberid",$order["memberid"])->where("$field >0")->find();
        if($stock)$stock = $stock->toArray();
        //有库存
        if($stock && isset($stock[$field]) && $stock[$field]>0){
            $db->table("dt_member_stock")->where("id",$stock["id"])->setDec($field);
            $log = [
                "groupid"=>$stock["groupid"],
                "memberid"=>$stock["memberid"],
                "dataid"=>$order["dataid"],
                "orderid"=>$order["id"],
                "send_num"=>$order["num"],
                "send_type"=>$level_type,
                "stockid"=>$stock["id"],
                "sendtime"=>$order["paytime"],
                "userid"=>$order["userid"],
                "is_history"=>0,
                "pay_back"=>0,
                "offline"=>1//线下
            ];
            $db->table("dt_send_log")->insert($log);
//            if($level_type==1)$this->add_purchase($order,$stock);
            return $stock["id"];
        }else{
            return 0;//无库存
        }
    }


    //平台分的钱
    public function hs_money($order,$percent){
        $db = new UkyDatabase();
        $hsmoney = [
            "orderid"=>$order["id"],
            "userid"=>$order["userid"],
            "price"=>$order["price"],
            "income"=>(int)($order["price"]*$percent)/100,
            "popularize"=>$percent,
            "addtime"=>date("Y-m-d H:i:s"),
            "offline"=>1
        ];
        $res1 = $db->table("dt_shop_hsmoney")->insert($hsmoney);
        return $res1;
    }


    //机构分的钱
    public function shop_money($order,$populize,$shop_income,$content_part=0){
        $db = new UkyDatabase();
        $shopmoeny[] = [
            "orderid"=>$order["id"],
            "userid"=>$order["userid"],
            "title"=>"收入",
            "memberid"=>$order["memberid"],
            "price"=>$order["price"],
            "income"=>(int)($order["price"]*$populize)/100,
            "shop_income"=>$shop_income,
            "trade_no"=>"",
            "cash_price"=>(int)($order["price"]*$populize)/100,
            "popularize"=>$populize,
            "cash_type"=>2,
            "status"=>1,
            "type"=>1,
            "addtime"=>date("Y-m-d H:i:s")
        ];
        $shopmoeny[] = [
            "orderid"=>$order["id"],
            "userid"=>0,
            "title"=>"线下提现",
            "memberid"=>$order["memberid"],
            "price"=>(int)($order["price"]*$populize)/100,
            "income"=>0-((int)($order["price"]*$populize)/100),
            "shop_income"=>0,
            "trade_no"=>$order["trade_no"],
            "cash_price"=>0,
            "popularize"=>0,
            "cash_type"=>4,//线下提现默认提了
            "status"=>1,//提现成功
            "type"=>2,
            "addtime"=>date("Y-m-d H:i:s")
        ];

        $res2 = $db->table("dt_shop_money")->insertAll($shopmoeny);
        return $res2;
    }


    //内容方分的钱
    public function shop_part_money($order,$populize){
        $db = new UkyDatabase();
        //加密机构余额
        $t['orderid'] = intval($order['id']);
        $t['member_meet'] = floatval((int)($order["price"]*$populize)/100);
        $t['member_nhr'] = floatval(0);  //当前实付加密
//        $t['promoter_meet'] = floatval($pro_money);
//        $t['promoter_nhr'] = floatval(0);
        $ticket = think_encrypt(json_encode($t),'91renrenshi');
        $shopmoeny = [
            "orderid"=>$order["id"],
            "userid"=>$order["userid"],
            "memberid"=>$order["item_memberid"],
            "price"=>$order["price"],
            "income"=>(int)($order["price"]*$populize)/100,
            "shop_income"=>(int)($order["price"]*$populize)/100,
            "cash_price"=>(int)($order["price"]*$populize)/100,
            "popularize"=>$populize,
            "cash_type"=>2,//线下收入
            "type"=>1,
            "ticket"=>$ticket,
            "member_meet"=>(int)($order["price"]*$populize)/100,
            "member_nhr"=>0,
            "addtime"=>date("Y-m-d H:i:s")
        ];
        $res2 = $db->table("dt_shop_part_money")->insert($shopmoeny);
        return $res2;
    }



    //课代表分的钱
    public function promoter_money($order,$promoter_id,$percent){
        $db = new UkyDatabase();
        $user_money = [
            "orderid"=>$order["id"],
            "userid"=>$order["userid"],
            "promoter_id"=>$promoter_id,
            "memberid"=>$order["memberid"],
            "price"=>$order["price"],
            "income"=>(int)($order["price"]*$percent)/100,
            "cash_price"=>(int)($order["price"]*$percent)/100,
            "popularize"=>$percent,
            "cash_type"=>2,//线下收入
            "type"=>1,
            "status"=>1,
            "addtime"=>date("Y-m-d H:i:s")
        ];
        $res3 = $db->table("dt_promoter_money")->insert($user_money);
        return $res3;
    }



    //获取比例
    public function get_percent($order,$datainfo){
        $ksb = $qudao = $neirong = 0;
        if(($order["memberid"]==$order["item_memberid"]) || $order["item_memberid"]==0){
            $qudao = $datainfo["popularize"];
            $ksb = 100 - $datainfo["popularize"];
        }
        if($order["item_memberid"]!=$order["memberid"] && $order["item_memberid"]>0){//售卖别的机构课程
            $ksb = $datainfo["pingtai"];
            $qudao = $datainfo["qudao"];
            $neirong = $datainfo["jigou"];
        }
        return ["ksb"=>$ksb,"qudao"=>$qudao,"neirong"=>$neirong];
    }



    //自动升级会员分销比例
    public function auto_upgrade($memberid,$level_type,$dataid){
        $db = new UkyDatabase();
        $pay_num = $db->table("dt_live_order o")
            ->join("dt_send_log l","o.id=l.orderid","left")
            ->where("o.memberid",$memberid)
            ->where("o.dataid",$dataid)
            ->where("o.level_type",$level_type)
            ->where("l.id is null")
            ->where("o.paystatus",1)
            ->count();
        $level_type = "level".$level_type;
        $config = [
            "level1"=>[
                "60"=>100,
                "70"=>400
            ],
            "level2"=>[
                "60"=>3,
                "70"=>8
            ],
            "level3"=>[
                "60"=>2,
                "70"=>5
            ]
        ];
        $exist = $db->table("dt_member_prepay")->where("memberid",$memberid)
            ->where("dataid",$dataid)
            ->where("status",1)
            ->order("level desc")
            ->find()->toArray();
        $num1 = $config[$level_type]["60"];
        $num2 = $config[$level_type]["70"];
        $field = $level_type."_num";
        if($exist){
            $new_level = $exist["level"];
            $popularize = $exist["popularize"];
            if($pay_num + 1 >$num1){
                $popularize = 60;
                $new_level = 2;
            }
            if($pay_num + 1 >$num2){
                $popularize = 70;
                $new_level = 3;
            }
            $db->table("dt_member_prepay")->where("id",$exist["id"])->update(["popularize"=>$popularize,"level"=>$new_level]);
        }

    }

    //库存使用相当于领取一次券，需扣减掉
    public function add_purchase($order,$stock){
        $total_num = $stock["level1_total"];
        $exist = Db::table("dt_member_prepay")->where("memberid",$order["memberid"])->find();
        if(!$exist)return false;
        $numberList = $table = Db::table('dt_group_purchase')->where('orderid',$exist["orderid"])->column('number');
        $bindNum = [];
        foreach($numberList as $val){
            $bindNum[] = $val;
        }
        for($i=1;$i<=$total_num;$i++){
            if(!in_array($i,$bindNum)){//没被使用过的券number
                break;
            }
        }
        $data = [
            'userid'=>$order["userid"],
            'orderid'=>$exist["orderid"],
            'memberid'=>$order["memberid"],
            'groupid'=>$order['userid'],
            'number'=>$i,
            'column_id'=>$order['dataid'],
            'type'=>$order['type'],
            'addtime'=>dateYmdHis()
        ];
        $re = Db::table('dt_group_purchase')->insert($data);
    }

    public function logger($log_content)
    {
        if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
            sae_set_display_errors(false);
            sae_debug($log_content);
            sae_set_display_errors(true);
        }else{ //LOCAL
            $max_size = 500000;
            $log_filename = "pay_error.html";
            if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
            file_put_contents($log_filename, date('Y-m-d H:i:s').$log_content."\r\n", FILE_APPEND);
        }
    }
}