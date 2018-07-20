<?php
/**

 * ============================================================================
 * * 版权所有 2015-2027 深圳国品品牌策划有限公司，并保留所有权利。
 * 网站地址: http://www.gopin.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: Chris 2015-08-10 $
 */ 
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller {
    public $session_id;
    public $cateTrre = array();
    /*
     * 初始化操作
     */
    public  $action;
    public  $controller;

    public function _initialize() {
        $this->action = ACTION_NAME;
        $this->controller = CONTROLLER_NAME;
        unlink("./Application/Runtime/Html/Home_Index_index.html");

      if (session('user_id')) {
          $user=M('users')->where('user_id='.session('user_id'))->find();
          session('user',$user);
          $this->assign('user',$user);
      }

       $hash = 'guanwangrenzheng';  
       $this->assign('hash',$hash);
       $this->assign('name','value');
     
       
        $url =$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

        // if((is_mobile() && $action=='index' &&  $controller=='index') || (is_mobile() && $url=='zhixiaobing.com/') || (is_mobile() && $url=='zhixiaobing.com/index.php')){
        //     $this->redirect('index/index');
        // }
//         $this->public_assign();


      $this->assign('common_title','查资助-就上知小兵');
    }
    /**
     * 保存公告变量到 smarty中 比如 导航 
     */
    public function public_assign()
    {
        
       $tpshop_config = array();
       $tp_config = M('config')->cache(true,TPSHOP_CACHE_TIME)->select();       
       foreach($tp_config as $k => $v)
       {
       	  if($v['name'] == 'hot_keywords'){
       	  	 $tpshop_config['hot_keywords'] = explode('|', $v['value']);
       	  }       	  
          $tpshop_config[$v['inc_type'].'_'.$v['name']] = $v['value'];
       }                        
       
       $goods_category_tree = get_goods_category_tree();    
       $this->cateTrre = $goods_category_tree;
       $this->assign('goods_category_tree', $goods_category_tree);                     
       $brand_list = M('brand')->cache(true,TPSHOP_CACHE_TIME)->field('id,parent_cat_id,logo,is_hot')->where("parent_cat_id>0")->select();              
       $this->assign('brand_list', $brand_list);
       $this->assign('tpshop_config', $tpshop_config);          
    }
    
    // public function _empty($value='')
    // { 
    //   $this->display('Empty/empty');

    // }
  // 客服表单项目
     public function object()
     {
      header("Content-Type:text/html;charset=utf-8");
      // dump( M('board')->select());
      if (IS_POST) {

        $data['user_id']=session('user_id')?session('user_id'):'';
        $data['mobile'] =  I('post.lnktel')?I('post.lnktel'):I('post.mobile');
        $data['username'] =  I('post.lnkper')?I('post.lnkper'):I('post.username');
        $data['email'] = '';
        $data['company'] = I('post.lnkcorp')?I('post.lnkcorp'):I('post.company');
        $data['condition']=I('post.condition')?I('post.condition'):'';

        $data['condition'] =explode(',', $data['condition']);
        $data['result'] = I('post.result')?I('post.result'):'';
        I('post.objectvalue')?I('post.objectvalue'):'';
        I('post.rmk1')?I('post.rmk1'):I('post.content');
        $data['content'] ='申报的项目：'.I('post.objectvalue').'<br/>留言内容：'.I('post.rmk1').'<br />来源：<a href='.I('post.url').'>'.I('post.url').'</a>';
       
        $data['add_time'] = time();
        $data['ip_address'] = get_client_ip();
        $data['status'] = '1';
        // dump($data);
        // 12个小时内不能重复提交五次
        $starttime = time()-12*60*60;
        // $this->ajaxReturn($starttime);
        $map['add_time'] = array(array('EGT',$starttime),array('ELT',time()),'AND');
        $count =M('board')->where($map)->where(array("ip_address"=>$data['ip_address']))->count();
        if ( $count >10) {
          $this->ajaxReturn(array('msg'=>'申请太多,请联系客服！',"status"=>'0')); 
        }else{

           $board = M('board')->add($data);
           if ($board) {
             
                $this->ajaxReturn(array('msg'=>'申请成功',"status"=>'1'));
           }else{
                $this->ajaxReturn(array('msg'=>'申请失败',"status"=>'0')); 
           }
        }

      
      }


    }
    public function _empty($value='')
    {
      $this->display('Empty/404');
    }
}