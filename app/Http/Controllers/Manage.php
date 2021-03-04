<?php
namespace app\admin\controller\user;
use app\common\model\User;
use think\facade\Request;
use think\Db;
use app\common\controller\Auth;
use app\miaokeapp\model\UkyDatabase;
use app\common\model\UkyRead;
/**
 * @title 人员增删改
 * @description 接口说明
 * @group 部门人员
 */
class Manage extends Auth{
    protected $model;
    public function initialize(){
        parent::initialize();
        $this->model = new \app\common\model\User();
    }

    /**
     * @title 用户列表
     * @description 接口说明
     * @author 开发者[lonnie]
     * @url /admin/user.manage/lst
     * @method POST
     *
     * @param name:deptid type:int require:0 default:0 other: desc:部门id
     * @param name:disable type:int require:0 default:0 other: desc:在职|离职
     * @param name:vague type:string require:0 default:0 other: desc:搜索词
     * @param name:page type:int require:0 default:0 other: desc:页码
     * @param name:limit type:int require:0 default:0 other: desc:每页数量
     *
     * @return_format count:总数
     * @return id:员工id
     * @return userid:员工id
     * @return deptid:部门名称
     * @return name:员工姓名
     * @return loginname:登陆名
     * @return dname:部门名称
     * @return duty:职位
     * @return stationid:岗位
     * @return rankid:职级
     * @return uppername:上级
     * @return phone:手机号
     * @return email:邮箱
     * @return poststatus:在职状态
     * @return manger:管理员列表@
     * @manger userid:管理员id name:管理员名称
     */
    public function lst(){
        $params = input();
        $deptid = !empty($params['deptid']) ? $params['deptid'] : '';
        $disable = isset($params['disable']) ? $params['disable'] : '';
        $vague = !empty($params['vague']) ? $params['vague'] : '';
        $page = !empty($params['page']) ? $params['page'] : 1;
        $length = !empty($params['limit']) ? $params['limit']: 10;
        $start = ($page - 1) * $length;
        $limit = $start.",".$length;

        if(is_numeric($disable)){
            $map[] = ['u.poststatus','eq',$disable];
        }else{
            $map[] = ['u.poststatus','eq',1];
        }
        if(!$deptid){
            return json(['code' => 0,'msg' => '参数错误，刷新重试']);
        }
        $dept = new \app\common\model\Dept();
        $dept_list = $dept->lst();
        foreach ($dept_list as $k => $v){
            if($v['deptid'] == $deptid){
                $manger_ids = $v['user_id'];
            }
        }
        $manger = [];
        if($manger_ids){
            $user_map[] = ['u.userid','in',$manger_ids];
            $user_map[] = ['u.deleted','eq',0];
            $user_map[] = ['u.poststatus','eq',1];
            $manger = $this->model->getList($user_map,'u.userid,u.loginname as name');
        }
        $subid = getSonNode($dept_list,$deptid,[]);
        $map[] = ['u.deptid','in',$subid];
        $map[] = ['u.deleted','eq',0];
        if(!empty($vague)){
            $map[] = ['u.name|u.loginname|u.phone','like',"%$vague%"];
        }
        $field = 'u.userid as id,u.userid,u.deptid,u.ksb_userid,u.name,u.loginname,d.name as dname,u.duty,u.stationid,u.rankid,uu.loginname as diff,u.phone,u.email,u.poststatus';
        $count = $this->model->getCount($map);
    	$data = $this->model->getList($map,$field,[],'deptid asc,userid desc',$limit);
    	$ksb_userids = array_column($data,"ksb_userid");
        $uky = new UkyRead();
        $ksb_user = $uky->table("dt_user")->where("id","in",$ksb_userids)->column("nickname,headimgurl","id");
        $rank = Db::table("rrs_drive_rule")->where("deleted",0)->column("id,rank_name as name");
        $model = $this->model;
        $station = $model::$station_list;
        foreach ($data as $k => $v){
            $data[$k]['rank'] = isset($rank[$v['rankid']]) ? $rank[$v['rankid']] : '';
            $data[$k]['station'] = isset($station[$v['stationid']]) ? $station[$v['stationid']] : '';
            $data[$k]['nickname'] = isset($ksb_user[$v["ksb_userid"]]) ? $ksb_user[$v["ksb_userid"]]["nickname"] : '';
            $data[$k]['headimgurl'] = isset($ksb_user[$v["ksb_userid"]]) ? $ksb_user[$v["ksb_userid"]]["headimgurl"]: '';
            unset($data[$k]['rankid'],$data[$k]['stationid']);
        }
    	return json(['code' => 0,'count' => $count,'data' => $data,'manger' => $manger]);
    }

    /**
     * @title 岗位|职级|客户池（添加|修改时需要）
     * @description 接口说明
     * @author 开发者[lonnie]
     * @url /admin/user.manage/getRelevantInfo
     * @method POST
     *
     * @return rank:职级列表@
     * @rank id:id name:名称
     * @return pools:客户池列表@
     * @pools id:id name:名称
     * @return station:岗位列表@
     * @station id:id name:名称
     */

    public function getRelevantInfo(){
        $model = new \app\common\model\User();
        $rank = Db::table("rrs_drive_rule")->where("deleted",0)->field("id,rank_name as name")->select();
        $pools = Db::table('rrs_pools')->where('deleted',0)->field("poolid as id,name")->select();
        $station_list = $model::$station_list;
        $station = [];
        foreach ($station_list as $k => $v){
            $station[] = [
                'id' => $k,
                'name' => $v,
            ];
        }
        //print_r($station_list);
        $data['rank'] = $rank;
        $data['pools'] = $pools;
        $data['station'] = $station;
        return json(['code'=>0,'data'=>$data]);
    }

    /**
     * @title 角色列表（添加||修改选择角色）
     * @description 接口说明
     * @author 开发者[lonnie]
     * @url /admin/user.manage/getAuthGroup
     * @method POST
     *
     * @return id:角色id
     * @return title:角色名称
     */
    public function getAuthGroup(){
        $model = new \app\common\model\Authgroup();
        $map[] = ['deleted','eq',0];
        $map[] = ['is_change','eq',1];
        $list = $model->getList($map,'id,title,sort,is_change','sort asc',30);
        return json(['code' => 0,'data' => $list]);
    }

    /**
     * @title 添加
     * @description 接口说明
     * @author 开发者[lonnie]
     * @url /admin/user.manage/add
     * @method POST
     *
     * @param name:name type:string require:0 default:0 other: desc:真实姓名
     * @param name:loginname type:string require:0 default:0 other: desc:登陆名
     * @param name:sex type:string require:0 default:0 other: desc:性别
     * @param name:deptid type:int require:0 default:0 other: desc:部门
     * @param name:duty type:string require:0 default:0 other: desc:职位
     * @param name:city type:int require:0 default:0 other: desc:城市
     * @param name:phone type:int require:0 default:0 other: desc:手机号
     * @param name:qq type:int require:0 default:0 other: desc:qq
     * @param name:email type:email require:0 default:0 other: desc:邮箱
     * @param name:poolsid type:int require:0 default:0 other: desc:客户池id
     * @param name:isteacher type:int require:0 default:0 other: desc:是否是老师
     * @param name:assistantid type:int require:0 default:0 other: desc:助理id
     * @param name:stationid type:int require:0 default:0 other: desc:岗位
     * @param name:rankid type:int require:0 default:0 other: desc:职级
     * @param name:upperid type:int require:0 default:0 other: desc:上级
     * @param name:password type:string require:0 default:0 other: desc:密码
     * @param name:qrpassword type:string require:0 default:0 other: desc:确认密码
     * @param name:poststatus type:int require:0 default:0 other: desc:是否在职
     * @param name:jointime type:date require:0 default:0 other: desc:时间
     * @param name:endtime type:date require:0 default:0 other: desc:离职时间
     */
    public function add(){
        if(request()->isPost()){
            Db::startTrans();
            try{
                $params = input('post.');
                $deptid = !empty($params['deptid']) ? $params['deptid'] : '';
                $group_ids = !empty($params['groupid']) ? $params['groupid'] : '';
                $cityid = !empty($params['city']) ? $params['city'] : '';
                $teacher = new \app\common\model\Teacher();
                $validate = validate("User");
                if (!$validate->scene('add')->check($params)) {
                    return json(['code' => 1,'msg' => $validate->getError()]);
                }
                $pass_res = $validate->check_password($params);
                if ($pass_res['code'] == 1) {
                    return json($pass_res);
                }
                unset($params['userid']);

                $params['password'] = md5($params['password']);
                $params['companyid'] = 1;
                $params['deleted'] = 0;
                if ($params['isteacher'] == 0) {
                    $params['client_upper_limit'] = 200;
                    $params['client_reality_limit'] = 200;
                }
                if($deptid){
                    $dept = new \app\common\model\Dept();
                    $pdeptid = $dept->alias('d')
                                    ->join('rrs_dept dd','d.pid=dd.deptid')
                                    ->where(['d.deptid' => $deptid,'d.groups' => 2,'dd.groups' => 1])
                                    ->value('dd.deptid');
                    $pdeptid && $params['pdeptid'] = $pdeptid;
                }

                $res = $this->model->createData($params);
                //$user_id = $rrs_user->getLastInsID();
                if(!$res){
                    return json(['code' => 1,'msg' => '添加失败']);
                }
                (new \app\common\model\AuthLog())->add(4, '创建账号', '用户名：'.$params['name'], USER_ID, $res, '用户管理', 1);//记录日志
                $auth_group_data = [];
                if($group_ids){
                    $group_ids = explode(',',$group_ids);
                    foreach ($group_ids as $v){
                        $auth_group_data[] = [
                            'uid' => $res,
                            'group_id' => $v
                        ];
                    }
                    Db::table('rrs_auth_group_access_new')->insertAll($auth_group_data);
                }else{
                    //默认添加销售员权限
                   /* if($params['isteacher'] == 0){
                        $auth_group_data = [
                            'uid' => $res,
                            'group_id' => 178
                        ];
                        Db::table('rrs_auth_group_access_new')->insert($auth_group_data);
                    }*/
                }

                if($params['isteacher']){
                    $info['web_name'] = $params['name'];
                    $info['userid'] = $res;
                    $info['web_name'] = $params['loginname'];
                    $info['name'] = $params['name'];
                    $info['phone'] = $params['phone'];
                    $info['deptid'] = $params['deptid'];
                    $info['cityid'] = $cityid;
                    $info['assistantid'] = $params['assistantid'];
                    $info['jointime'] = isset($params['jointime']) ? $params['jointime'] : date('Y-m-d');
                    $info['categoryid'] = 0;
                    $info['tid'] = insert_ID('rrs_teacher');
                    $status = $teacher->allowField(true)->save($info);
                    if(!$status){
                        //Db::commit();  return json(['code' => 0,'msg' => '添加成功']);
                        Db::rollback(); return json(['code' => 1,'msg' => '讲师添加失败']);
                    }
                }
                Db::commit();
                return json(['code' => 0,'msg' => '添加成功']);
            } catch (\Exception $e) {
                Db::rollback();
                return json(["code"=>1,"info"=>$e->getMessage()]);
            }
        }
    }

    /**
     * @title 查看
     * @description 接口说明
     * @author 开发者[lonnie]
     * @url /admin/user.manage/info
     * @method POST
     *
     * @param name:id type:int require:0 default:0 other: desc:用户id
     *
     */
    public function info(){
        $uky = new UkyDatabase();
        $id = input('id');
        if(empty($id) || !is_numeric($id)){
            return json(['msg'=>'参数错误','code'=>1]);
        }

        $map = ['u.userid' => $id,'u.deleted' => 0];
        $field = "u.userid,u.name,u.loginname,u.phone,u.duty,u.qq,u.email,u.deptid,u.poolsid,u.upperid,u.rankid,u.stationid,u.isteacher,u.poststatus,CONVERT(varchar(10), u.jointime, 120 ) as jointime,CONVERT(varchar(10), u.leavetime, 120 ) as endtime,u.assistantid,u.sex,u.city as cityid,u.ksb_userid";
        $res = $this->model->getInfo($map,$field);
        $res['jointime'] = $res['poststatus'] == 0 ? $res['endtime'] : $res['jointime'];
        $res["nickname"] = "";
        $res["headimgurl"] = "";
        if($res["ksb_userid"] && $res['ksb_userid'] > 0){
            $ksbinfo = $uky->table("dt_user")->where("id",$res["ksb_userid"])->field("nickname,headimgurl")->find()->toArray();
            $res["nickname"] = $ksbinfo["nickname"];
            $res["headimgurl"] = $ksbinfo["headimgurl"];
        }

        if($res){
            $res['provinceid'] = Db::table('rrs_city')->where(['cityid' => $res['cityid']])->value('provinceid');
            $group = Db::table('rrs_auth_group_access_new a')
                        ->join('rrs_auth_group_new g','a.group_id=g.id')
                        ->where(['uid' => $id])
                        ->where("g.deleted","=",0)
                        ->field('a.group_id,g.title')
                        ->select();
            $group_name = [];
            $group_ids = [];
            foreach ($group as $k => $v){
                $group_name[] = $v['title'];
                $group_ids[] = $v['group_id'];
            }
            $res['root_name'] = implode(",", $group_name);
            $res['root'] = $group_ids;
            $res["isteacher"] = intval($res["isteacher"]);
            return json(['code' => 0,'data' => $res]);
        }else{
            return json(['code' => 1,'msg' => '获取信息失败']);
        }
    }

    /**
     * @title 修改
     * @description 接口说明
     * @author 开发者[lonnie]
     * @url /admin/user.manage/edit
     * @method POST
     *
     * @param name:userid type:string require:0 default:0 other: desc:修改的用户id
     * @param name:name type:string require:0 default:0 other: desc:真实姓名
     * @param name:loginname type:string require:0 default:0 other: desc:登陆名
     * @param name:sex type:string require:0 default:0 other: desc:性别
     * @param name:deptid type:int require:0 default:0 other: desc:部门
     * @param name:duty type:string require:0 default:0 other: desc:职位
     * @param name:city type:int require:0 default:0 other: desc:城市
     * @param name:phone type:int require:0 default:0 other: desc:手机号
     * @param name:qq type:int require:0 default:0 other: desc:qq
     * @param name:email type:email require:0 default:0 other: desc:邮箱
     * @param name:poolsid type:int require:0 default:0 other: desc:客户池id
     * @param name:isteacher type:int require:0 default:0 other: desc:是否是老师
     * @param name:assistantid type:int require:0 default:0 other: desc:助理id
     * @param name:stationid type:int require:0 default:0 other: desc:岗位
     * @param name:rankid type:int require:0 default:0 other: desc:职级
     * @param name:upperid type:int require:0 default:0 other: desc:上级
     * @param name:password type:string require:0 default:0 other: desc:密码
     * @param name:qrpassword type:string require:0 default:0 other: desc:确认密码
     * @param name:poststatus type:int require:0 default:0 other: desc:是否在职
     * @param name:jointime type:date require:0 default:0 other: desc:时间
     * @param name:endtime type:date require:0 default:0 other: desc:离职时间
     * @param name:ksb_userid type:date require:0 default:0 other: desc:课师宝userid
     */
    public function edit(){
        Db::startTrans();
        $teacher = new \app\common\model\Teacher();
        $mysql_teacher = new \app\common\mysqlmodel\Teacher();
        $uky = new UkyDatabase();
        $params = input('post.');
        $deptid = !empty($params['deptid']) ? $params['deptid'] : '';
        $group_ids = !empty($params['groupid']) ? $params['groupid'] : '';
        $cityid = !empty($params['city']) ? $params['city'] : '';
        $validate = validate("User");
        if (!$validate->scene('edit')->check($params)) {
            return json(['code'=>1,'msg'=>$validate->getError()]);
        }
        $userid = !empty($params['userid']) ? $params['userid'] : '';
        if(trim($params['password']) != ''){
            $pass_res = $validate->check_password($params);
            if ($pass_res['code'] == 1) {
                return json($pass_res);
            }
            $params['password'] = md5(trim($params['password']));
        } else {
            unset($params['password']);
        }

        try{
            $params['name'] = trim($params['name']);
            $params['loginname'] = trim($params['loginname']);
            $params["ksb_userid"] = isset($params['ksb_userid']) ? trim($params['ksb_userid']) : -1;
            if(!isset($params["ksb_userid"]) || $params["ksb_userid"]<=0) $ksb_userid = -888;
            $params["ksb_openid"] = $uky->table("dt_user")->where("id",$params["ksb_userid"])->value("openid");
            $teacher_exist = $uky->table("dt_teacher")->where("ksb_userid",$ksb_userid)->count();
            if($teacher_exist)return json(["code"=>1,"msg"=>"该微信已绑定过讲师，请先确认"]);
            $user_exist = Db::table("rrs_user")
                ->where("userid","<>",$params["userid"])
                ->where("ksb_userid",$ksb_userid)
                ->select();
            if($user_exist)return json(["code"=>1,"msg"=>"该微信已绑定过华师员工，请先确认"]);
            if($params['ksb_userid']<=0)unset($params["ksb_userid"]);//传负数就是没有绑定
            if($deptid){
                $dept = new \app\common\model\Dept();
                $pdeptid = $dept->alias('d')
                                ->join('rrs_dept dd','d.pid=dd.deptid')
                                ->where(['d.deptid' => $deptid,'d.groups' => 2,'dd.groups' => 1])
                                ->value('dd.deptid');
                $pdeptid && $params['pdeptid'] = $pdeptid;
            }

            $find_user = Db::table("rrs_user")->where("userid",$params['userid'])->field('poststatus')->find();

            if ($find_user['poststatus'] != $params['poststatus']) {
                if ($params['poststatus'] == 1) {
                    //设置为在职
                    (new \app\common\model\AuthLog())->add(4, '启用账号', '用户名：'.$params['name'], USER_ID, $params['userid'], '用户管理', 1);//记录日志
                } else {
                    //设置为离职
                    (new \app\common\model\AuthLog())->add(4, '禁用账号', '用户名：'.$params['name'], USER_ID, $params['userid'], '用户管理', 1);//记录日志
                }
            }
            if (isset($params['endtime'])) {
                $params['leavetime'] = $params['endtime'];
            }
            $res = $this->model->editData($params);

            if(!$res){
                return json(['code'=>1,'msg'=>'更新失败']);
            }

            $auth_group_data = [];
            if($group_ids){
                $group_ids = explode(',',$group_ids);
                foreach ($group_ids as $v){
                    $auth_group_data[] = [
                        'uid' => $userid,
                        'group_id' => $v
                    ];
                }
                $find_group = Db::table("rrs_auth_group_access_new")
                    ->where("uid",$userid)
                    ->field('group_id')
                    ->select();
                $find_group_id = array_column($find_group,'group_id');
                $get_log_content = (new \app\admin\controller\auth\Authgroup())->get_log_content($find_group_id,$group_ids,'rrs_auth_group_new','id','title');
                if (!empty($get_log_content)) {
                    (new \app\common\model\AuthLog())->add(2, '角色修改', $get_log_content, $userid, $userid, '用户管理', 1);//记录日志
                }

                $del = Db::table('rrs_auth_group_access_new')->where(['uid' => $userid])->delete();
                if($del === false){
                    Db::rollback();
                    return json(['code' => 1,'msg' => '更新失败']);
                }
                $add_access = Db::table('rrs_auth_group_access_new')->insertAll($auth_group_data);
                if($add_access === false){
                    Db::rollback();
                    return json(['code' => 1,'msg' => '更新失败']);
                }
            }

            $teacheruser = $teacher->where('userid',$userid)->find();
            if($teacheruser){
                if ($params['poststatus'] == 1 && $params['isteacher'] == 1) {
                    $info['deleted'] = 0;
                } else {
                    $info['deleted'] = time();
                }
                $info['web_name'] = $params['loginname'];
                $info['name'] = $params['name'];
                $info['phone'] = $params['phone'];
                $info['deptid'] = $params['deptid'];
                $cityid && $info['cityid'] = $cityid;
                $info['assistantid'] = $params['assistantid'];
                $info['jointime'] = isset($params['jointime']) ? $params['jointime'] : date('Y-m-d');
                $status = $teacher->where('userid',$userid)->update($info);
                //$mysql_teacher->where('userid',$userid)->update($info);
            }else{
                $info['web_name'] = $params['loginname'];
                $info['name'] = $params['name'];
                $info['phone'] = $params['phone'];
                $info['deptid'] = $params['deptid'];
                $cityid && $info['cityid'] = $cityid;
                $info['assistantid'] = $params['assistantid'];
                $info['jointime'] = isset($params['jointime']) ? $params['jointime'] : date('Y-m-d');
                $info['userid'] = $params['userid'];
                $info['categoryid'] = 0;
                $info['tid'] = insert_ID('rrs_teacher');
                $status = $teacher->allowField(true)->save($info);
                //$mysql_teacher->allowField(true)->save($info);
            }

            if($status !== false){
                $result = [];
                //推送数据
                if ($params['isteacher'] == 1 && $params['poststatus'] == 0 && class_exists ('Yar_client')) {
                    //$result = $this->model->del_rpc('teacher',$userid);
                }
                Db::commit();
                return json(['code' => 0,'msg' => '更新成功']);
            }else{
                Db::rollback();
                return json(['code' => 1,'msg' => '更新失败']);
            }
        } catch (\Exception $e) {
            Db::rollback();
            return json(['code' => 1,'msg' => $e->getMessage()]);
        }
    }

    /**
     * @title 删除
     * @description 接口说明
     * @author 开发者[lonnie]
     * @url /admin/user.manage/remove
     * @method POST
     *
     * @param name:id type:int require:0 default:0 other: desc:用户id
     */
    public function remove(){
        $param = input();
        $id = $param['id'];
        $type = isset($param['type']) ? $param['type'] : 0;
        //$poststatus = isset($param['status']) ? $param['status'] : 1;
        if(empty($param['id']) || !is_numeric($id)) return json(['code'=>1,'msg'=>'参数错误']);
        $map['userid'] = $id;
        $map['deleted'] = 0;
        $item = $this->model->getInfo($map,'userid,isteacher');
        if(!$item){
            return json(['code'=>1,'msg'=>'找不到数据，刷新重试']);
        }
        $find = Db::table('rrs_user')->where("userid",$id)->field('name')->find();
        (new \app\common\model\AuthLog())->add(4, '禁用账号', '用户名：'.$find['name'], USER_ID, $id, '用户管理', 1);//记录日志
        $res = [];
        //推送数据
        if ($item['isteacher'] == 1 && class_exists ('Yar_client')) {
            //$res =  $this->model->del_rpc('teacher',$id);
        }
        if($type == 1){
            $data['deleted'] = time();
        }else{
            $data['poststatus'] = 0;
        }
        $data['leavetime'] = date('Y-m-d H:i:s');
        $status =  $this->model->where('userid',$id)->update($data);
        if($status !== false){
            $model = new \app\common\model\Teacher();
            $res = $model->where('userid',$id)->update(['deleted' => time()]);
            return json(['code' => 0,'msg'=>'更新成功']);
        }
        return json(['code'=>1,'msg'=>'更新失败']);
    }



    /**
     * @title 基本资料
     * @description 接口说明
     * @author 开发者[lonnie]
     * @url /admin/user.manage/personInfo
     * @method POST
     *
     * @param name:userid type:string require:0 default:0 other: desc:修改的用户id
     * @param name:avatar type:string require:0 default:0 other: desc:修改的用户id
     * @param name:name type:string require:0 default:0 other: desc:修改的用户id
     * @param name:loginname type:string require:0 default:0 other: desc:修改的用户id
     * @param name:phone type:string require:0 default:0 other: desc:修改的用户id
     * @param name:card_phone type:string require:0 default:0 other: desc:修改的用户id
     * @param name:duty type:string require:0 default:0 other: desc:修改的用户id
     * @param name:sex type:string require:0 default:0 other: desc:修改的用户id
     * @param name:email type:string require:0 default:0 other: desc:修改的用户id
     * @param name:qq type:string require:0 default:0 other: desc:修改的用户id
     * @param name:birthday type:string require:0 default:0 other: desc:修改的用户id
     * @param name:city type:string require:0 default:0 other: desc:修改的用户id
     */
    public function personInfo(){
        $field = 'userid,avatar,name,loginname,phone,card_phone,duty,sex,email,qq,birthday,city as cityid';
        $item = $this->model->getInfo(['userid' => USER_ID],$field);
        if($item['cityid']){
            $item['provinceid'] = Db::table('rrs_city')->where(['cityid' => $item['cityid']])->value('provinceid');
        }
        return json(['code' => 0,'data' => $item]);
    }

    /**
     * @title 修改基本资料
     * @description 接口说明
     * @author 开发者[lonnie]
     * @url /admin/user.manage/setInfo
     * @method POST
     *
     * @param name:userid type:int require:0 default:0 other: desc:用户id---其他字段参考基本信息字段
     */
    public function setInfo(){
        $params = input('post.');
        $params['userid'] = USER_ID;
        $field = 'avatar,duty,email,card_phone,city,sex,birthday,qq';
        $res = $this->model->editData($params,$field);
        if($res !== false){
            return json(['code' => 0,'msg' => '操作成功']);
        }else{
            return json(['code' => 1,'msg' => '操作失败']);
        }
    }


    /**
     * @title 修改密码
     * @description 接口说明
     * @author 开发者[lonnie]
     * @url /admin/user.manage/setPassword
     * @method POST
     *
     * @param name:oldpassword type:password require:0 default:0 other: desc:旧密码
     * @param name:password type:password require:0 default:0 other: desc:新密码
     * @param name:qrpassword type:password require:0 default:0 other: desc:确认密码
     */
    public function setPassword(){
        $params = input();
        if(empty(trim($params['oldpassword']))) return json(['code'=>1,'msg'=>'请填写旧密码' ]);
        if(empty(trim($params['password']))) return json(['code'=>1,'msg'=>'新密码不能为空' ]);

        if(!(trim($params['password']) === trim($params['qrpassword']))){
            return json(['code'=>1,'msg'=>'两次密码不一致' ]);
        }

        $map[] = ['userid','eq',USER_ID];
        $map[] = ['password','eq',md5(trim($params['oldpassword']))];
        $count = $this->model->getCount($map);
        if(!$count){
            return json(['code' => 1,'msg' => '旧密码错误' ]);
        }

        $res['password'] = md5(trim($params['password']));
        $res = $this->model->where('userid',USER_ID)->update($res);
        if($res !== false){
            return json(['code' => 0,'msg' => '密码修改成功' ]);
        }else{
            return json(['code' => 1,'msg' => '密码修改失败' ]);
        }
        return json(['code'=>1,'msg'=>'验证失败' ]);
    }


    /**
     * @title 更新用户企业微信名--用于企业微信提醒
     * @description 接口说明
     * @author 开发者[Oyx]
     * @url /admin/user.manage/update_qy_name
     * @method POST
     *
     */
    public function update_qy_name(){
        $user = Db::table("rrs_user")->where("deleted",0)->field("userid,name,loginname")->where("loginname is not null")->select();
        $class = new \myclass\Qyweixin();
        $list = $class->get_dept_userlist();
        $new = [];
        foreach ($user as $key=>$val) {
            foreach ($list as $k => $v) {
                if($val["loginname"]){
                    if(strpos($v['name'],$val["loginname"]) !== false){
                        $new[] = [
                            "userid"=>$val["userid"],
                            "qywx_name"=>$v["userid"]
                        ];
                    }
                }
                if($val["name"]){
                    if(strpos($v['name'],$val["name"])!== false){
                        $new[] = [
                            "userid"=>$val["userid"],
                            "qywx_name"=>$v["userid"]
                        ];
                    }
                }

            }
        }
        $model = new \app\common\model\User();
        $res = $model->saveAll($new);

        $json["code"] = 0;
        $json["data"] = $res;
        return json($json);
    }

}
