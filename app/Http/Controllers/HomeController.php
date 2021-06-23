<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use test\Test;
use function Psy\debug;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
//    public function __construct()
//    {
//        $this->middleware('auth');
//    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
echo date("w");die;
        $origin = isset($_SERVER['HTTP_ORIGIN'])? $_SERVER['HTTP_ORIGIN'] : '';
        echo $origin;die;
        $arr = [1,2,3,4,5];
        $max = max($arr);
        echo $max;die;
        $config = [
            "appId"=>"444",
            "apiVersion"=>"v2",
            "timestamp"=>time()
        ];
        $params = $config;
        ksort($params);
        $sign = "";
        foreach ($params as $k=>$v){
            $sign .= $k.$v;
        }
        echo $sign;die;
echo str_replace("=","",http_build_query($params));die;
        return md5(http_build_query($params));
        $arr = array(1,2,3,4,5,'a');
        foreach ($arr as $k=>$y){

            if($y == 3){
                continue;
            }
            echo $y. "\n";
        }
        die;
        date_default_timezone_set("PRC");
        $refund_time = date("Y-m-d H:i:s",strtotime("-72 hours"));//72小时前
        echo date_default_timezone_get();
        echo $refund_time;die;
        echo phpinfo();
//        return view('home');
    }


    public function composer_test(){
        $x = NULL;
//        if ('0xFF' == 255) {
            $x = (int)'0xFF';
//        }
        var_dump($x);
//        $class = new Test();
//        $class->test();
    }


    public function mongo()
    {
        $manager = new \MongoDB\Driver\Manager("mongodb://45.40.202.157:27017",[
            'username' => "admin",
            'password' => "123456",
            'db' => "db1"
        ]);

// 插入数据
        $bulk = new \MongoDB\Driver\BulkWrite;
//        $bulk->insert(['x' => 1, 'name'=>'菜鸟教程', 'url' => 'http://www.runoob.com']);
//        $bulk->insert(['x' => 2, 'name'=>'Google', 'url' => 'http://www.google.com']);
//        $bulk->insert(['x' => 3, 'name'=>'taobao', 'url' => 'http://www.taobao.com']);
//        $manager->executeBulkWrite('db1.table1', $bulk);
//
        $filter = ['x' => ['$gt' => 1]];
        $options = [
            'projection' => ['_id' => 0],
            'sort' => ['x' => -1],
        ];

// 查询数据
        $query = new \MongoDB\Driver\Query($filter, $options);
        $cursor = $manager->executeQuery('db1.table1', $query);

        foreach ($cursor as $document) {
            print_r($document);
        }

//更新
//        $bulk->update(
//            ['x' => 2],
//            ['$set' => ['name' => '菜鸟工具', 'url' => 'tool.runoob.com']],
//            ['multi' => false, 'upsert' => false]
//        );
        $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 1000);
//        $result = $manager->executeBulkWrite('db1.table1', $bulk, $writeConcern);

//删除
        $bulk->delete(['x' => 1], ['limit' => 1]);   // limit 为 1 时，删除第一条匹配数据
        $bulk->delete(['x' => 2], ['limit' => 0]);   // limit 为 0 时，删除所有匹配数据

        $result = $manager->executeBulkWrite('db1.table1', $bulk, $writeConcern);
    }

    private $status = 0;

    public function weibo($food){

        if($food->type==1 || $food->type==2){
            return "不可加热带皮或者带壳ss食物";
        }

        if($this->status==0){
            $this->hot($food);
        }else{
            return "正在加热中，不可打开";
        }
        return "热好了";
    }

    public function hot($food){
        $this->status = 0;

        sleep(10);

        $this->status = 1;
    }


    public function fib($n=20){
        //该方法时间复杂度 O(n),性能大大提升
        if($n<1) return 0;
        $arr = [];//已查询过的数字，维护在数组中，下次直接取

        echo $this->helps($arr, $n) ;
    }

    public function helps(&$arr,$n){

        if($n==1 || $n==2) return 1;

        if (isset($arr[$n]) && $arr[$n]!=0) return $arr[$n];
        $arr[$n] = $this->helps($arr, $n-1) + $this->helps($arr, $n-2);
        return $arr[$n];
    }



    public function old_fib($n=5){
        //该方法时间复杂度 O(2^n)
        if($n==1 || $n==2) return 1;

        return  ($this->old_fib($n-1)) + ($this->old_fib($n-2));
    }

    //根据斐波那契数列 的状态转移⽅程，当前状态只和之前的两个状态有关，其实并不需要那么⻓ 的⼀个 DP table 来存储所有的状态，
    //只要想办法存储之前的两个状态就⾏ 了。所以，可以进⼀步优化，把空间复杂度降为 O(1)：
    public function new_fib($n=5){
        //该方法时间复杂度 O(1)
        if($n==1 || $n==2) return 1;

        $prev = $curr = 1;

        for($i=3;$i<=$n;$i++){
            $sum = $prev + $curr;
            $prev = $curr;
            $curr = $sum;
        }
        return  $curr;

    }

    public $values = [1, 5, 10, 50];
    public $array;


    public function dp($amount)
    {
        $values = [1, 5, 10, 50];
        krsort($values);
        $amount = intval($amount);
        for ($i = 0; $i < $amount + 1; $i++) {
            $this->array[$i] = -1;
        }

        $return = $amount;
        if ($amount < 1) {
            $return = 0;
        } elseif ($amount == 0) {
            $return = 0;
        } elseif ($this->array[$amount] != -1) {
            $return = $this->array[$amount];
        } elseif (in_array($amount, $values)) {
            $return = 1;
        } else {
            foreach ($values as $v) {
                //能够凑的面值则凑
                if ($v < $amount) {
                    $return_temp = 1 + $this->dp($amount - $v);
                    //如果当前凑个数更少则返回
                    if ($return_temp < $return) {
                        $return = $return_temp;
                    }
                }
            }
        }
        $this->array[$amount] = $return;
//        print_r($this->array);die;
        return $return;
    }


   public function  fallegg1(){
       $arr[] = [];
       $a = $this->fallegg(2,200,$arr);
//       print_r($arr);
       echo $a;
   }


   public function fallegg($k,$n,&$arr){
//        $n = 2;
//        $k = 20;

        if($k==1) return $n;
        if($n==1) return 0;


        if (isset($arr[$n."-".$k])) {
            return $arr[$n."-".$k];
        }

	    $res = 200;
        for ($i=1;$i<=$n;$i++){
            $res = min($res,max($this->fallegg($k-1,$i-1,$arr),$this->fallegg($k,$n-$i,$arr))+1);
        }
        $arr[$n."-".$k] = $res;
        return  $res;
    }


    public function choose_monkey_king(){

        //11只猴子
        //思路 没有叫到的删除掉原来的位置，加到后面
        //原理：循环链表
        $monkeys = [1,2,3,4,5,6,7,8,9,10];
        $king = $this->chooseMonkeyKing($monkeys,3);

        //求交集取出来原始位置
        $king_pos = array_intersect($monkeys, $king);
        echo '<pre>';
        print_r($king_pos);

    }

    public function  chooseMonkeyKing($arr,$callNum){
        if(count($arr) == 0 || $callNum <= 0)
        {
            exit("玩勺子把去吧!");
        }

        //定义一个循环的全局变量
        $i = 1 ;
        while(count($arr) > 1)
        {
            //如果取余为0 ，说明该猴子应该被T除。
            if( $i % $callNum == 0)
            {
                unset($arr[$i-1]);
            }
            else
            {
                //把当前的猴子加入到数组队尾，同时删除该位置的猴子
                array_push($arr,$arr[$i-1]);
                unset($arr[$i-1]);
            }

            $i++;
        }

        return $arr;
    }

    //检测内存
    public function collect(){

        $a = "new string";
        $c = $b = $a;
        debug_zval_dump('a');
        unset($b, $c);
        echo "<br/>";
        debug_zval_dump('a');

    }


    //array_filter用法。用回调函数过滤数组中的单元
    //array_walk 用法
    //1、循环数组，回调处理（并不修改数组元素的值，而是跳出去做其他的事情[回调的定义]）
    public function filter(){

        $array1 = array("a"=>1, "b"=>2, "c"=>3, "d"=>4, "e"=>5);
        $array2 = array(6, 7, 8, 9, 10, 11, 12);

        echo "Odd :\n";
        print_r(array_filter($array1, "odd"));
        echo "Even:\n";
        print_r(array_filter($array2, "even"));


        $arr = [
            ['name' => 'A', 'age' => 18],
            ['name' => 'B', 'age' => 11],
        ];
        array_walk($arr, function ($value) {
            //做其他的逻辑处理，不对数组元素进行处理
            echo  "name:" . $value['name'] . ', age:' . $value['age'] . "\n";
        });
    }
}
