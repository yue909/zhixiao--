<?php
namespace Home\Controller;
use Think\Controller;
class EmptyController extends Controller{
    public function _empty(){
        //根据当前控制器名来判断要执行那个城市的操作
       $this->display('Empty/404');
    }
  
}