<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Queue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $num = "";
    public function __construct($num)
    {
        //
        $this->num = $num;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
//            $res = DB::insert("insert into users(name,email,password,remember_token) values ('{$name}','{$email}','{$pass}','{$remember_token}')");
//            sleep(5);
//            echo $res;
//            return true;
            echo $this->num;
        }catch (\Exception $e){
//            return false;
            Log::info($e->getMessage());
        }
    }
}
