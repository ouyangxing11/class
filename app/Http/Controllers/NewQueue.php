<?php


namespace App\http\controllers;


class NewQueue
{

    private  $column= array();//存放列是否占有标记，0为占有
    private  $rup= array();//存放主对角线是否占有，0为占有
    private  $lup= array();//存放次对角线是否占有，0为占有
    private  $queen= array();//存放解中皇后的位置
    private  $num;  //解的编号
    private  $sum=0;
    function __construct() {
        for($i=1;$i<=8;$i++){
            $this->column[$i]=1;
        }
        for($i=1;$i<=(2*8);$i++){
            $this->rup[$i]=$this->lup[$i]=1;
        }
    }
    public function backtrack($i){//i从上往下
        if($i>8){
            $this->showAnswer();
        }else{
            for($j=1;$j<=8;$j++){
                //从左往右横向

                if(($this->column[$j]==1)&&($this->rup[$i+$j]==1)&&($this->lup[$i-$j+8]==1)){
                    $this->queen[$i]=$j;
                    //设定为占用
                    $this->column[$j]=$this->rup[$i+$j]=$this->lup[$i-$j+8]=0;
                    $this->backtrack($i+1);
                    $this->column[$j]=$this->rup[$i+$j]=$this->lup[$i-$j+8]=1;
                }
            }
        }
    }

    protected function showAnswer(){
        echo $this->sum;
        echo "<br>";
        $this->num++;
        print("解答");
        print($this->num);
        echo "<br>";
        for($y=1;$y<=8;$y++){
            for($x=1;$x<=8;$x++){
                if($this->queen[$y]==$x){
                    print("Q");
                }else{
                    print(" * ");
                }
            }
            print("<br>");
        }
        print("<br>");
    }
}