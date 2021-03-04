<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    private $status = 0;

    public function weibo($food){

        if($food->type==1 || $food->type==2){
            return "不可加热带皮或者带壳食物";
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

}
