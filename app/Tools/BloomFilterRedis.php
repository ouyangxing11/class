<?php
namespace App\Tools;
use App\http\controllers\BloomFilterHash;
use Illuminate\Support\Facades\Redis;
abstract class BloomFilterRedis
{
    /**
     * 需要使用一个方法来定义bucket的名字
     */
    protected $bucket;

    protected $hashFunction;

    public function __construct()
    {
        if (!$this->bucket || !$this->hashFunction) {
            throw new \Exception("需要定义bucket和hashFunction", 1);
        }
        $this->Hash = new BloomFilterHash();
        $this->Redis =  Redis::connection(); //假设这里你已经连接好了
    }

    /**
     * 添加到集合中
     */
    public function add($string)
    {
        $pipe = $this->Redis->multi();
        foreach ($this->hashFunction as $function) {
            $hash = $this->Hash->$function($string);
            $pipe = $this->Redis->setbit($this->bucket, $hash, 1);
        }
        return $this->Redis->exec();
    }

    /**
     * 查询是否存在, 如果曾经写入过，必定回true，如果没写入过，有一定几率会误判为存在
     */
    public function exists($string)
    {
        $pipe = $this->Redis->multi();
        $len = strlen($string);
        foreach ($this->hashFunction as $function) {
            $hash = $this->Hash->$function($string, $len);
            $pipe = $this->Redis->getbit($this->bucket, $hash);
        }
        $res = $this->Redis->exec();
        foreach ($res as $bit) {
            if ($bit == 0) {
                return false;
            }
        }
        return true;
    }

}