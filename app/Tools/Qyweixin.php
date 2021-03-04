<?php
namespace myclass;

class Qyweixin
{
    /**
     * 微信公众号信息处理
     */
    //corpid
//    public $corpid = 'wxb33b78e908b8a88d';//华师兄弟
    public $corpid = 'wwa8fe0f40f11bc6cf';//华师经纪
    //sercret
//    public $corpsecret = '2lZpccQwP6ldzupQtqMAtf-1Ku_WQdMmzUo6Ld-vHu4';//华师兄弟测试--通讯录的
    public $corpsecret = 'CPw27q1WE622k_eb1PnQeliMrJVWtgUWVzKmRnfWKkc';//华师经纪--通讯录的
//    public $msgsecret = '9b8nwfubypfF-op8IAiOmvDmZQbvzalKTcwer7jlOOo';//华师兄弟发送消息的--服务处理中心
    public $msgsecret = 'X3K7-jq3e89whtw-jmph5FcRnqgl1HAz3mBN2eDLaoc';//华师经纪发送消息的--华公子

    //微信发消息api
    public $weixinSendApi = 'https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=';
//    public $weixinSendApi = 'https://qyapi.weixin.qq.com/cgi-bin/linkedcorp/message/send?access_token=';

    /**
     * 请求微信Api，获取AccessToken
     */
    public function getAccessToken($type)
    {
        $id = $type=="msg"?21:20;
        $secret = $type=="msg"?$this->msgsecret:$this->corpsecret;
        $db = new \app\common\model\Weixinconfig;
        $data = $db->get($id);
        if($data->access_token_time>time())
        {
            $this->token = $data->access_token;
            return $data->access_token;
        }else {
            //更新access_token
            $getAccessTokenApi = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid={$this->corpid}&corpsecret={$secret}";
            $jsonString = $this->curlGet($getAccessTokenApi);
            $jsonInfo = json_decode($jsonString,true);
            if(isset($jsonInfo['access_token'])) {
                $tokenInfo = $jsonInfo;
                $data->access_token_time = time() + 7100;
                $data->access_token = $tokenInfo['access_token'];
                $this->token = $tokenInfo['access_token'];
                $data->save();
            }
        }
        if(isset($tokenInfo['access_token']) && $tokenInfo['expires_in']>time()){
            return $tokenInfo['access_token'];
        } else {
            return FALSE;
        }
    }

    //获取部门列表
    public function get_dept_list(){
        $token = $this->getAccessToken("corp");
        $url = "https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token=".$token;
        $jsonString = $this->curlGet($url);
        $jsonInfo = json_decode($jsonString,true);
        print_r($jsonInfo);die;
        if(isset($jsonInfo["errcode"])&& $jsonInfo["errcode"]==0){
            return $jsonInfo["userlist"];
        }
        return [];
    }

    //获取部门下所有成员
    public function  get_dept_userlist(){
        $token = $this->getAccessToken("corp");
        $url = "https://qyapi.weixin.qq.com/cgi-bin/user/simplelist?access_token={$token}&department_id=1&fetch_child=1";
        $jsonString = $this->curlGet($url);
        $jsonInfo = json_decode($jsonString,true);
        if(isset($jsonInfo["errcode"])&& $jsonInfo["errcode"]==0){
            return $jsonInfo["userlist"];
        }
        return [];
  }


  //获取应用列表
  public function get_applist(){
      $token = $this->getAccessToken("corp");
      $url = "https://qyapi.weixin.qq.com/cgi-bin/agent/list?access_token={$token}";
      $jsonString = $this->curlGet($url);
      $jsonInfo = json_decode($jsonString,true);
      print_r($jsonInfo);
  }


    /**
     * 发信息接口
     *
     * @author wanghan
     * @param $content 发送内容
     * @param $touser 接收的用户 @all全部 多个用 | 隔开
     * @param $toparty 接收的群组 @all全部 多个用 | 隔开
     * @param $totag 标签组 @all全部 多个用 | 隔开
     * @param $agentid 应用id
     * @param $msgtype 信息类型 text=简单文本
     */
    public function send($content='测试',$touser='@all',$toparty='',$totag='',$agentid=8,$msgtype='text')
    {
        $api = $this->weixinSendApi.$this->getAccessToken("msg");
//        echo $api;die;
        $postData = array(
            'touser' => $touser,
            'toparty' => $toparty,
            'totag' => $totag,
            'msgtype' => $msgtype,
            'agentid' => $agentid,
            'text' => array(
                'content' => urlencode($content)
            )
        );

        $postString = urldecode(json_encode($postData));
        $ret = $this->curlPost($api,$postString);
        $retArr = json_decode($ret,TRUE);
        if(isset($retArr['errcode']) && $retArr['errcode'] == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Curl Post数据
     * @param string $url 接收数据的api
     * @param string $vars 提交的数据
     * @param int $second 要求程序必须在$second秒内完成,负责到$second秒后放到后台执行
     * @return string or boolean 成功且对方有返回值则返回
     */
    function curlPost($url, $vars, $second=30)
    {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//            'Content-Type: application/json; charset=utf-8',
//            'Content-Length: ' . strlen($vars))
//        );
        $data = curl_exec($ch);
        curl_close($ch);
        if($data)
            return $data;
        return false;
    }

    /**
     * CURL get方式提交数据
     * 通过curl的get方式提交获取api数据
     * @param string $url api地址
     * @param int $second 超时时间,单位为秒
     * @param string $log_path 日志存放路径,如果没有就不保存日志,还有存放路径要有读写权限
     * @return true or false
     */
    function curlGet($url,$second=30,$log_path='', $host='', $port='')
    {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        if(!empty($host)){
            curl_setopt($ch,CURLOPT_HTTPHEADER,$host);
        }
        if(!empty($port)){
            curl_setopt($ch,CURLOPT_PORT,$port);
        }
        $data = curl_exec($ch);
        $return_ch = curl_errno($ch);
        curl_close($ch);
        if($return_ch!=0)
        {
            if(!empty($log_path))
                file_put_contents($log_path,curl_error($ch)."\n\r\n\r",FILE_APPEND);
            return false;
        }
        else
        {
            return $data;
        }
    }

}