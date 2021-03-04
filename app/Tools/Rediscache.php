<?php
namespace myclass;
use think\config;
use think\Request;
use think\Loader;
use think\Db;
use think\Controller;
use think\Exception;
use think\cache\driver\Redis;

class Rediscache extends Controller
{

    public function __construct($host="106.52.69.140",$port=6379) {
        try{
            $this->redis = new Redis();
            $this->port		=  $port;
            $this->host 	=  $host;
            $this->_table	=	"";
            $this->_field	=	"";
            $this->_pageNum=0;
            $this->_pageSize=0;
            $this->_where="";
            $this->_pCount=0;
            $this->redis->connect($this->host, $this->port);
            $this->redis->auth('hsjj!@#$%ok');
            $this->redis->select(15);
            $this->redis->ping();
            $this->redis->setOption(\Redis::OPT_SCAN,\Redis::SCAN_RETRY);
        }catch(\Exception $e){
            echo $e->getMessage();
            die;
        }
    }


    public function __destruct() {
        //print_r($this);
    }


    public  function table($table){

        $this->_table=$table;
        return $this;
    }

    public function field($field){

        $this->_field=$field;
        return $this;
    }

    public function where($where){

        $this->_where=$where;
        return $this;
    }

    public function find($id){

        $field=$this->redis->SMEMBERS($this->_table."_field");

        $table_field=[];
        foreach($field as $fd=>$fv){
            $table_field[]=$id.":".$fv;
        }

        foreach($this->redis->hMGet($this->_table,$table_field) as $rk=>$rv){
            $rk=substr($rk,strlen($id)+1);
            $field_data[$rk]=$rv;
        }

        return $field_data;
    }

    public function add($data=[]){
        $rdata=[];
        $id=$data["id"];

        foreach($data as $k=>$v){

            if($k!="id"){
                $rdata[$id.":".$k]=$v;

                $temp=$this->redis->HGET($this->_table,$id.":".$k);
                $this->redis->zrem($this->_table."_search_".$k,$id.":".$temp);

                $this->redis->zadd($this->_table."_search_".$k,$id,$id.":".$v);
            }else{
                $rdata[$id.":id"]=$id;
            }

            $this->redis->SADD($this->_table."_field",$k);
        }

        $this->redis->zadd($this->_table."_zset",$id,$id);
        return $this->redis->hMset($this->_table,$rdata);
    }

    public function page($num=1,$size){
        //echo input("page",0)>0;
        $this->_pageNum=input("page",0)>0?input("page",0):$num;
        $this->_pageSize=$size;
        $this->_pCount=ceil($this->count()/$size);
        return $this;
    }


    public function select(){
        $data=[];
        $it=NULL;

        if($this->_where){
            foreach($this->_where as $key=>$val){

                $zscan_data=NULL;
                while($zscan_data=$this->redis->zscan($this->_table."_search_".$key, $it, "*{$val}*")){

                    //print_r($zscan_data);
                    //echo $this->_table."_search_".$key."*{$val}*<br>";

                    if(!empty($zscan_data))
                    {
                        foreach($zscan_data as $k=>$v)
                        {
                            $data[]=$this->find($v);
                        }
                    }

                }
            }


        }else{

            $page_keys=$this->redis->zrange($this->_table."_zset",($this->_pageNum-1)*$this->_pageSize,($this->_pageNum-1)*$this->_pageSize+$this->_pageSize-1);

            foreach($page_keys as $k=>$v){
                $data[]=$this->find($v);
            }

        }

        return $data;
    }


    public function count(){

        return $this->redis->ZCARD($this->_table."_zset");
    }

    public function value(){
        //print_r($this->_table);
        return $this;
    }




    /**
     * 为hash表多个字段设定值。
     * @param string $key
     * @param array $value
     * @return array|bool
     */
    public function hMset($key,$value)
    {
        if(!is_array($value))
            return false;
        return $this->redis->hMset($key,$value);
    }

    /**
     * 为hash表多个字段设定值。
     * @param string $key
     * @param array|string $value string以','号分隔字段
     * @return array|bool
     */
    public function hMget($key,$field)
    {
        if(!is_array($field))
            $field=explode(',', $field);
        return $this->redis->hMget($key,$field);
    }


    public function hGetAll($key,$search_key)
    {

        $keys_arr=$this->redis->keys($key);

        $return=[];
        foreach($keys_arr as $key=>$val){
            $data=$this->hGetOne($val);

            if(strpos($data["title"],$search_key)!==false){
                $return[$val]=$data;
            }

        }
        return $return;
    }


    public function hSearchAll($key,$search_key)
    {
        $keys_arr=$this->redis->keys($key);

        $return=[];
        foreach($keys_arr as $key=>$val){
            $data=$this->hGetOne($val);

            if(strpos($data["title"],$search_key)!==false){
                $return[$val]=$data;
            }

        }
        return $return;
    }


    public function sAdd($key,$value){
        return $this->redis->SADD($key,$value);
    }

    public function sscan($value,$key){
        return $this->redis->sScan($value,$value,"*{$key}*有了清晰的思*",10000);
    }

    public function hGet($tb,$field){
        return $this->redis->HGET($tb,$field);
    }


    public function hGetOne($key)
    {
        return $this->redis->HMGET($key,["title"]);
    }

    public function hMsetTable($db,$value)
    {

        if(!is_array($value))
            return false;

        $pk=$db->getpk();	//获取表主键
        $table=$db->getTable();

        foreach($value as $key=>$val){

            $this->redis->hset($table.":".$val[$pk],$val["title"],$val[$pk]);
        }
        // return $this->redis->hMset($key,$value);
    }

    public function sendmsg($value){
        $data=json_encode($value,true);
        $sec=$value["sec"]?$value["sec"]:1;
        $score=$value["score"];
        return $this->redis->psetex("temp_sms:".$score, $sec, $data)&$this->redis->zADD("perm_sms",$score,$data);
    }



    public function get_list(){

        return $this->redis->Lrange("mylist",0,100);

    }

    public function del_list(){
        return $this->redis->BLPOP("mylist",0,100);
    }

}