<?php


namespace App\Http\Controllers;


class TestController extends Controller
{
    private $name = "";
    private $age = 0;
    private $sex = "";

    public function __construct($name,$age,$sex)
    {

        $this->name = $name;
        $this->age = $age;
        $this->sex = $sex;
    }


    public function __clone(){
        echo __METHOD__."你正在克隆对象";
    }

}

$person = new TestController("小明");
$person2 = clone $person;


var_dump("person1".$person);
echo "<br/>";
var_dump("person2".$person2);
