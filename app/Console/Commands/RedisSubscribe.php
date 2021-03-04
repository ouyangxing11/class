<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisSubscribe extends Command
{
    /**
     * 控制台命令的名称和签名。
     *
     * @var string
     */
    protected $signature = 'redis:subscribe';

    /**
     * 控制台命令说明。
     *
     * @var string
     */
    protected $description = 'Subscribe to a Redis channel';

    /**
     * 执行控制台命令。
     *
     * @return mixed
     */
    public function handle()
    {
//        Redis::subscribe(['test-channel'], function ($message) {
//            echo $message."99999";
//        });
        Redis::psubscribe(['*'], function ($message, $channel) {
            echo $message."88888";
        });
    }

}