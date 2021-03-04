<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Queuetest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $name = rand(0,10)."testname";
        $pass = "543214sdfsd";
        $email = rand(100,999)."sfs@ejiayou.com";
        $remember_token = "dsfs444422";
        try{
            $res = DB::insert("insert into users(name,email,password,remember_token) values ('{$name}','{$email}','{$pass}','{$remember_token}')");
//            sleep(5);
//            echo $res;
//            return true;
            echo 1;
        }catch (\Exception $e){
//            return false;
            Log::info($e->getMessage());
        }
//        return true;

    }
}
