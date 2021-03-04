<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/13
 * Time: 19:41
 */
namespace myclass;
use think\Db;

class miaokelog
{
    protected $url_arr = array(
        'teacher_list' => '秒课列表',
        'miao_teacher' => '秒课讲师详情',
        'catagory_teacher' => '师资列表',
        'teacher_detail' => '讲师详情',
        'course_detail'=>'课程详情',
        'project_index'=>'项目列表',
        'pro_detail'=>'项目详情',
        'activity_detail'=>'活动详情',
        'sales_card'=>"名片详情"
    );

    public function __construct($method,$id,$agentid,$agentype,$content="")
    {
        $this->url = $method;//接口名。
        $this->view_id = $id;//参数id
        $this->agentid = $agentid;//分享者id
        $this->agentype = $agentype;//分享者用户类型
        $this->content = $content;//查看的内容
    }

    public function log_page(){
        $view = new \app\miaokeapp\model\SecViewLog();
        $share = new \app\miaokeapp\model\ShareLog();
        $data['view_word']  = $this->url_arr[$this->url];
        $data['view_type']  = $this->url;
        $data['view_id']    = $this->view_id;
        $data['view_time']  = date("Y-m-d H:i:s",time());
        $data['stay_time']  = 0;
        $data['contactid']  = USER_ID;
        $data['clientid']  = CLIENT_ID;
        $data['url']        = $this->url;
        $data['user_type']  = USER_TYPE;
        $data['transform']  = 0;
        $data['content']  = $this->content;
        $data['agentid']  = $this->agentid;
        $data['agentype']  = $this->agentype;
        $data['view']  = 0;
        $data['sourceid'] = $sourceid =$this->get_staff($this->agentid,$this->agentype);
        $stay_time = $this->get_stay_time($this->agentid,$sourceid);
        $share->addShare($this->agentid,$this->agentype,$this->url_arr[$this->url],$this->view_id);
        if($this->agentid && $this->agentype)$this->log_card($data['sourceid'],$this->agentid,$this->agentype);

    $view->isUpdate(false)->allowField(true)->insert($data);

    }

    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 更新上一个接口记录的停留时间
     */
    public function get_stay_time($agentid,$sourceid){
        $view = new \app\miaokeapp\model\SecViewLog();
        $lastvisit = $view->where("contactid",USER_ID)
                        ->where("clientid",CLIENT_ID)
                        ->where("user_type",USER_TYPE)
                        ->order("view_time desc")
                        ->find();
        if($lastvisit) {
            $lastvisit = $lastvisit->toArray();
            $stay_time = time() - strtotime($lastvisit['view_time']);
            if ($lastvisit['sourceid'] != $sourceid) {
                $stay_time = $stay_time > 1200 ? $stay_time : 1200;
            }
            $income = $stay_time>=1200?1:0;
            $view->isUpdate(true)->save(["stay_time" => $stay_time,"income"=>$income], ["id" => $lastvisit['id']]);
        }
    }



    public function get_staff($shareid,$sharetype){
        $agentid =0;
        if(USER_TYPE==1) {
            $contact = Db::table("rrs_client_contact cc")
                ->join("rrs_client c","c.clientid=cc.clientid","left")
                ->where("cc.contactid",USER_ID)
                ->field("c.owner as realowner,cc.owner,cc.contactid")
                ->find();
            $owner = $contact['owner'] ? $contact['owner'] : $contact['realowner'];
            $agentid = $owner;
            if($contact && empty($contact['owner']) && $sharetype==3){
                Db::table("rrs_client_contact")->where("contactid",USER_ID)->update(['owner'=>$shareid]);
                $agentid = $shareid;
            }
//            if ($sharetype == 1) {
//                //分享者是客户看客户看过的名片
////                $agent = Db::table("rrs_card_log c")
////                    ->join("rrs_user u", "u.userid=c.staffid")
////                    ->where("u.deleted=0 and poststatus=1")
////                    ->where("c.userid", $shareid)
////                    ->where("c.usertype", $sharetype)
////                    ->where("staffid>0")
////                    ->order("view_time desc")
////                    ->field("staffid,c.id,view_time")
////                    ->find();
////                $agentid = isset($agent['staffid']) ? $agent['staffid'] : $owner;
//                $shareuser = Db::table("rrs_client_contact cc")
//                    ->join("rrs_client c","c.clientid=cc.clientid","left")
//                    ->where("cc.contactid",$shareid)
//                    ->field("c.owner as realowner,cc.owner")
//                    ->find();
//                $showner = $shareuser['owner'] ? $shareuser['owner'] : $shareuser['realowner'];
//                $agentid =$owner ? $owner:$showner;
//            } elseif ($sharetype == 2) {
//                $assid = Db::table("rrs_user")->where("userid", $shareid)->value("assistantid");
//                $agentid = $owner ? $owner : $assid;
//            }
            $agentid = $agentid ? $agentid : 708;
        }elseif(USER_TYPE==2){
            $assid = Db::table("rrs_user")->where("userid", USER_ID)->value("assistantid");
            $agentid = $assid ? $assid : 708;
        }elseif(USER_TYPE==3){
            $agentid = USER_ID;
        }else{
            $agentid = 708;
        }
        return $agentid;
    }



    /**
     * 记录浏览卡片
     */
    public function log_card($agentid,$shareid,$sharetype){
        $userid = USER_ID;
        $map[] = ['staffid','eq',$agentid];//查看的助理id
        $map[] = ['userid','eq',$userid]; //查看者id
        $map[] = ['usertype','eq',USER_TYPE]; //查看者类型
        $map[] = ['agentid','eq',$shareid];
        $map[] =['agentype','eq',$sharetype];
        $exist = Db::table("rrs_card_log")->where($map)->select();
        if(empty($exist)){
            $map[] =["view_time",'eq', date("Y-m-d H:i:s",time())];
            $map[] =["admire",'eq',0];
            $res = Db::table("rrs_card_log")->insert($map);
        }else{
            $res = Db::table("rrs_card_log")->where($map)->update(["view_time"=>date("Y-m-d H:i:s",time())]);
        }
    }
}