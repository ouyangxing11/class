<?php

namespace App\http\controllers;


use App\Tools\BloomFilterRedis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;


class FilteRepeatedComments extends BloomFilterRedis
{

    /**
     * 表示判断重复内容的过滤器
     * @var string
     */
    protected $bucket = 'rptc';

    protected $hashFunction = array('BKDRHash', 'SDBMHash', 'JSHash');


    public function add_test(Request $request){
        $name = $request->input("name");
        $res = $this->add(200);
        var_dump($res);
    }

    public function exists_test(Request $request){
        $name = $request->input("name");
        $res = $this->exists(200);
        var_dump($res);
    }
}