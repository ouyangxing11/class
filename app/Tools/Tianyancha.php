<?php
namespace myclass;
use think\config;
use think\Request;
use think\Loader;
use think\Db;

use think\File;

class Tianyancha{
//    e0764aaf-cdc7-4fe1-95bc-9405ee0826bf
//    dd17c7ca-561b-4598-a92c-4a2a8b79c7d3
    public function __construct($token="e0764aaf-cdc7-4fe1-95bc-9405ee0826bf"){

        $this->token		= $token;
        $this->search_url	="http://open.api.tianyancha.com/services/v4/open/searchV2";
        $this->baseinfo_url	="https://open.api.tianyancha.com/services/v4/open/baseinfo";
        $this->validday=30;	//有效时间内从本地获取，超出时间从接口查询
    }

    private $company_arr = [
        1 => '公司',
        2 => '香港公司',
        3 => '社会组织',
        4 => '律所',
        5 => '事业单位',
        6 => '基金会'
    ];

    /**查询列表
     * @param string $word
     * @return mixed|\think\response\Json
     */
    public function search($word="")
    {
        if(empty($word)) return json(["errcode"=>1,"msg"=>"关键词不能为空"]);
        $param=[];
        $param["word"]=$word;

        $param_url="?".http_build_query($param);	//数组生成url

        $json=$this->vget($this->search_url.$param_url);

        $data=json_decode($json,true);
//        print_r($data);die;
//        $data = object_to_array($data);
        if ($data['reason'] == 'ok') {
            $this->save_number('天眼查列表');
            $this->savecache($data['result']['items'],'addAll','');
        }
        return $data;
    }


    /**
     * 保存查询次数
     */
    public function save_number($name)
    {
        //保存查询次数
        $find = Db::table("rrs_tianyancha_num")->where("name",$name)->find();
        if (empty($find)) {
            $res = Db::table('rrs_tianyancha_num')->insert(['name'=>$name,'count'=>1]);
        } else {
            $res = Db::table("rrs_tianyancha_num")->where("id",$find['id'])->setInc('count',1);
        }
        return $res;
    }


    /**
     * 调用接口获取基本信息，id优先匹配，把信息缓存到本地数据
     * @param string $name
     * @param string $id
     * @return \think\response\Json
     */
    public function baseinfo($name="",$id="")
    {
        $name=trim($name);
        $id=trim($id);
        $flag = 'add';
        $ccid = 0;
        if(empty($name) && empty($id)) return json(["errcode"=>1,"msg"=>"企业名称和企业id不能同时为空"]);

        $param=[];
        if(!empty($id)) {
            $param["id"]=$id;
            $data = $this->getcache($param);
            if ($data) {
                if (strtotime($data['addtimes']) + 30*24*3600 >= time()) {
                    return $data;
                } else {
                    $flag = 'edit';
                    $ccid = $data['ccid'];
                }
            }
        }
        if(!empty($name)) {
            $param["name"]=$name;
            $data = $this->getcache($param);
            if ($data) {
                if (strtotime($data['addtimes']) + 30*24*3600 >= time()) {
                    return $data;
                } else {
                    $flag = 'edit';
                    $ccid = $data['ccid'];
                }
            }
        }
        $param_url="?".http_build_query($param);	//数组生成url

        $json=$this->vget($this->baseinfo_url.$param_url);
        $data=json_decode($json,true);
        if ($data['reason'] == 'ok') {
            $this->save_number('天眼查详情');
        }
        $data = $data['error_code'] == 0 ? $data['result'] : [];
        $this->savecache($data,$flag,$ccid);
        return $data;
    }

    /**查看详情
     * @param $data
     */
    public function detail($data) {
        $find = $this->getcache("id = '{$data['id']}' or name='{$data['name']}'");
        if ($find) {
            $this->savecache($data,'edit',$find['ccid']);
        } else {
            $res = $this->savecache($data,'add','');
        }
        $data['companyorgtype'] = isset($data['companyorgtype']) ? $data['companyorgtype'] : isset($this->company_arr[$data['companytype']]) ? $this->company_arr[$data['companytype']] : '';
        $data['estiblishtime'] = isset ($data['estiblishtime']) && trim($data['estiblishtime']) != '-' ?substr($data['estiblishtime'],0,19): '';
        return $data;
    }


    /**
     * 将查询结果缓存到本地
     * @param unknown $table
     * @param unknown $data
     */
    public function savecache($data=[],$flag='',$ccid=''){
        $model = new \app\common\model\TianyanchaCorp();
        if (!empty($data)) {
            if ($flag == 'add') {
                //添加
                $res = $model->add($data);
            } else if ($flag == 'edit') {
                $res = $model->edit($data,$ccid);
            } else if ($flag == 'addAll') {
                $res = $model->addAll($data);
            }
            return $res||false;
        }
        return false;
    }

    /**
     * 根据获取时间取出有效时间段的数据
     * @param unknown $table
     * @param unknown $data
     */
    public function getcache($param){
        $res = Db::table("rrs_tianyancha_corp")->cache(true,30*24*3600)->where($param)->find();
        return $res;
    }


    private function vget($url){
        $heard=["Authorization:{$this->token}"];//token 写入 headers
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_USERAGENT, "abc"); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HTTPHEADER, $heard); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据
    }


}