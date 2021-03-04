<?php


namespace App\Http\Controllers;
use App\Jobs\Queue;
use App\Tools\Baidumap;
use Illuminate\Http\Request;
use App\Jobs\Queuetest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;


class IndexController extends Controller
{

    public function index(){
        echo app_path();die;
//        $sql = "select * from users";
//        $result = DB::select($sql);
//        $result = DB::table("users")->orderBy("id")->chunk(1,function ($users){
//            foreach ($users as $user){
//                print_r($user->name);
//                echo "===";
//            }
//        });
//        Db::beginTransaction();
//        $res = DB::table("users")->where("id",2)->lockForUpdate()->get();
        $res= User::find(1);
//        echo $res->name;
        print_r($res->toArray());die;
//        $res = DB::table("users")->whereColumn("id","name")->get();
//        $res =  DB::table('users')
//            ->oldest("updated_at")
//            ->first();
//
        return User::paginate();
        var_dump($res);
        die;
    }


    public function redis(){
//        $redis = Redis::connection();
//        $name = $redis->get("name");
////
//        echo $name;die;
        Redis::psubscribe(['*'], function ($message, $channel) {
            echo $message;
        });
//        echo $name;
//        $key = "hit".date("md").'01';
//        $res = $redis->sadd($key,"user:".'2012');
//        print_r($redis->smembers($key));
    }

    public function dbtest(Request $request){
        //->whereColumn('first_name', 'last_name') 验证两个字段是否相等，可传比较符，二维数组条件
//        $where[] = ["merchandise_id","<",60];
//        $where[] = ["merchandise_name","like","%50%"];
//        $where = [["create_time","between",["2016-10-12","2017-10-12"]],["user_id",1]];
        $where["state"] = 1;
//        print_r($where);die;
        $res = DB::table("merchandise")
            ->where($where)
//            ->whereDate("create_time","2016-06-21")
//            ->select("merchandise_id")
            ->pluck("merchandise_name","merchandise_id","merchandise_id");
//            ->addSelect("merchandise_id")
//                ->lockForUpdate()
//            ->forPage(0,2);
        print_r($res);die;
        $update = DB::table("merchandise")
            ->where('merchandise_id',1)
            ->update(['value'=>1]);
        echo $update;
//        echo $res->currentPage();die;
//        foreach ($res as $v){
////            $v->merchandise_id = $v->merchandise_id + 5;
//            $v->mark = "ss";
//        }
//        print_r($res);


    }


    public function queue(){
        Log::info("测网速队列");
        $num = rand(0,999);
//        Queue::dispatch()->delay(Carbon::now()->addSeconds(5));
        $call_back_job = (new Queue($num))->onQueue('rabbit')->delay(0);
        $this->dispatch($call_back_job);

    }

    public function test(Request $request){

        for($i=0;$i<10;$i++){
            for($j=10;$j<20;$j++){
                echo $i."--".$j;
                echo "<hr/>";
            }
        }
        die;
        $a = 1;
        $a = $a + $a + ($a = 2);

        $b = 1;
        $b = $b + ($b = 2);

        echo $a, '-', $b;die;
        $sc = $request->input("sc");
        echo $sc;die;
        for($i = 1;$i <= 10; $i++ ){
            for($j = 1;$j <= 10;$j++){
                $m = $i * $i + $j * $j;
                echo"$m \n<br/>";
                if($m < 90 || $m > 190) {
                    break 1;//跳出第一层
                }
            }
        }
    }


    public function seckill(){
        $user_ids = ",1,2,3,4,";
        $user_ids = ltrim($user_ids,",");
        echo $user_ids;die;
        $redis = Redis::connection();
        $redis->watch("mywatchkey");
        $mywatchkey = $redis->get("mywatchkey");
        $rob_total = 100;   //抢购数量
        if ($mywatchkey < $rob_total) {
            $redis->multi();
            //设置延迟，方便测试效果。
            sleep(1);
            //插入抢购数据
            $redis->hSet("mywatchlist", "user_id_" . mt_rand(1, 9999), time());
            $redis->set("mywatchkey", $mywatchkey + 1);
            $rob_result = $redis->exec();
            if ($rob_result) {
                $mywatchlist = $redis->hGetAll("mywatchlist");
                echo "抢购成功！<br/>";
                echo "剩余数量：" . ($rob_total - $mywatchkey - 1) . "<br/>";
                echo "用户列表：<pre>";
                var_dump($mywatchlist);
            } else {
                echo "手气不好，再抢购！";
                exit;
            }
        }else{
            echo "手气不好，再抢购！";
            $mywatchlist = $redis->hGetAll("mywatchlist");
            var_dump($mywatchlist);
        }

    }


    public function fib_recursive($n){
        if($n==0||$n==1){
            return 1;
        }else{
            return $this->fib_recursive($n-1)+$this->fib_recursive($n-2);
        }
    }


    //redis 管道
    public function redis_pipline(){

        $redis = Redis::connection("docker1");

        $info = $redis->info("replication");
        print_r($info);


        die;


        $data["total_income"] = bcadd(2776.08 , 0,2);//加上邀请收益

        echo $data["total_income"];die;

        $mir = microtime(true);


        //使用管道20秒
        Redis::pipeline(function ($pipe) {
            for ($i = 0; $i < 100000; $i++) {
                $pipe->set("key:$i", $i);
            }
        });

        //不使用管道超过40秒
        $redis = Redis::connection();
        for ($i=1000000;$i>980000;$i--){
            $redis->set("set:".$i,$i);
        }



        $mir2 = microtime(true);

        echo $mir2-$mir;
    }

    public function getDistance(Request $request){
        $start = $request->input("start","");
        $end = $request->input("end","");
        $baidu = new Baidumap();
        $dis = $baidu->line_time($start,$end);
        print_r($dis);
    }


    public function test_yield(){
        $result = self::createRange(10); // 这里调用上面我们创建的函数
        foreach($result as $value){
            sleep(1);
            echo $value.'<br />';
        }
    }

    public static function  createRange($number){
        for($i=0;$i<$number;$i++){
            yield time();
        }
    }

    public function array_merge(){
        $arr1 = ["16","2","3","6","9","17"];
        $arr2 = ["16","2","3","9","15","6"];
        $arr3 = ["16","2","3","6","8","17"];

        $arr = array($arr1,$arr2,$arr3);
        $result_array = call_user_func_array ('array_intersect', $arr);
        print_r($result_array);die;

        $a = [1,2,3];
        $b = [2,3,4];
        $c = array_intersect($a,$b);
        print_r($c);
    }

}