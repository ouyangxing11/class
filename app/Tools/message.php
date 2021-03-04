<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/9 0009
 * Time: 下午 3:15
 */

namespace myclass;
use \think\Db;
/**消息
 * Class message
 * @package myclass
 */
class message
{
    /**添加消息队列
     * @param $id
     */
    public function add_common_message($data)
    {
        //审核通过
        $add_data = [];
        $fields = ['title','txt','ifwx','ifsms','sender','toname','wxopenid','mobile','tablename','rowid','pagepath','alerttime'];
        foreach ($data as $k => $v) {
            $temp_keys = array_keys($v);
            $diff_keys = array_diff($fields,$temp_keys);
            if (!empty($diff_keys)) {
                return false;
            } else {
                $add_data[$k] = [
                    'title' => $v['title'],
                    'txt' => $v['txt'],
                    'ifwx' => $v['ifwx'],
                    'ifsms' => $v['ifsms'],
                    'sender' => $v['sender'],
                    'toname' => $v['toname'],
                    'wxopenid' => $v['wxopenid'],
                    'mobile' => $v['mobile'],
                    'tablename' => $v['tablename'],
                    'rowid' => $v['rowid'],
                    'addtime' => date("Y-m-d H:i:s"),
                    'deleted' => 0,
                    'pagepath' => $v['pagepath'],
                    'alerttime' => $v['alerttime']
                ];
            }
        }
        $result = Db::table("rrs_msg_center")->insertAll($add_data);
        return $result;
    }


    /**添加消息队列
     * @param $id
     */
    public function add_msg_center($id,$data)
    {
        $find = Db::table('rrs_meeting_item')->where('deleted','=',0)->find($id);
        //审核通过
        $add_data = [];
        foreach ($data as $k => $v) {
            $wx = $find['ifwx'] == 1 ? $v['wxopenid'] : '';
            $phone = $find['ifsms'] == 1 ? $v['phone'] : '';
            $add_data[$k] = [
                'title' => $find['title'],
                'txt' => $find['txt'],
                'ifwx' => $find['ifwx'],
                'ifsms' => $find['ifsms'],
                'sender' => $find['owner'],
                'toname' => $v['userid'],
                'wxopenid' => $wx,
                'mobile' => $phone,
                'tablename' => 'rrs_meeting_item',
                'rowid' => $id,
                'addtime' => date("Y-m-d H:i:s"),
                'deleted' => 0,
                'pagepath' => 'pages/my/my_date/my_date',
                'alerttime' => date("Y-m-d H:i:s",strtotime($find['begindate'])-$find['alert']*60)
            ];
        }
        $result = Db::table("rrs_msg_center")->insertAll($add_data);
        return $result;
    }
}