<?php
namespace Home\Controller;
use Home\Controller\BaseController;
class IndexController extends BaseController {


    public function index(){
     	
     	if(!session('article')){
       		$this->article();
       	}
       	$article = session('article');

       	if(!session('zizhu')){
       		$this->zizhu();
       	}
       	$zizhu = session('zizhu');
        // dump($article);
        $this->assign('name','value');       
        $this->assign('article',$article);      	
        $this->assign('zizhu',$zizhu);      	

        $shuju =$this->shuju();
        $this->assign('shuju',$shuju);

        
        // 判断当前用户是否手机

        if(is_mobile()){
        	$map =array();
            $map['total_money']=array('EGT',1000);
            $company = M('company')->where($map)->limit(10)->select();
            foreach($company as $key=>$value){
                $company[$key]['Name'] = str_cut($company[$key]['Name'],18);
                if(M('ObjectFunding')->where(array('company_id'=>$value['id']))->find()){
                    $company[$key]['z'] = 1;
                    $money = 0;
                    $object = M('ObjectFunding')->where(array('company_id'=>$value['id']))->select();
                    foreach($object as $k=>$v){
                        $money += str_replace(',','',$v['money']);
                    }
                    $company[$key]['money'] = number_format($money,3);
//                    if(empty($company[$key]['total_money']) && $company[$key]['money']!='0.00' || ($company[$key]['total_money']!=$company[$key]['money'] && !empty($company[$key]['total_money']))){
//                        $sql = "update tp_company set total_money=".$company[$key]['money'] ." where id=".$value['id'];
//                        $res = M('company')->execute($sql);
//                    }
                }
            }
            $this->assign('company',$company);
            $this->display('mindex');
//            $this->mindex();
        }else{
        	 // 查询历史
        	$this->assign('log',M('Company')->where('update_time!=""')->order("id desc")->limit(8)->select());
//        	echo "<div style='display: none'>";
//        	dump(M('Company')->where()->order("id desc")->limit(8)->select());
//        	echo "</div>";
        	$count = M('board')->count();
	        $num = intval((time())/100000)+$count;
	        $this->assign('num',$num);
        	if (!session('obj')) {
            $this->obj(); 
	        }
	        $obj = session('obj');
	        $level =  $obj['level'];
	        $efforts =  $obj['efforts'];
	        $type =  $obj['type'];
	        $this->assign('level',$level);
	        $this->assign('efforts',$efforts);
	        $this->assign('type',$type);
            $this->display();
        }


    }

    public function  obj($value='')
    {
        $obj['level'] = M('ObjectLevel')->select();
        $obj['efforts'] = M('ObjectEfforts')->select();
        $obj['type'] = M('ObjectType')->select();
        session('obj', $obj);
    }

   public function mindex()
   {

       if(!session('article')){
           $this->article();
       }
       $article = session('article');
       $this->assign('article',$article);
       $map['total_money']=array('EGT',1000);
       $company = M('company')->where($map)->limit(10)->select();
       $this->assign('company',$company);
       $shuju =$this->shuju();
       $this->assign('shuju',$shuju);
            foreach($company as $key=>$value){
                $company[$key]['Name'] = str_cut($company[$key]['Name'],18);
                if(M('ObjectFunding')->where(array('company_id'=>$value['id']))->find()){
                    $company[$key]['z'] = 1;
                    $money = 0;
                    $object = M('ObjectFunding')->where(array('company_id'=>$value['id']))->select();
                    foreach($object as $k=>$v){
                        $money += str_replace(',','',$v['money']);
                    }
                    $company[$key]['money'] = number_format($money,3);
//                    if(empty($company[$key]['total_money']) && $company[$key]['money']!='0.00' || ($company[$key]['total_money']!=$company[$key]['money'] && !empty($company[$key]['total_money']))){
//                        $sql = "update tp_company set total_money=".$company[$key]['money'] ." where id=".$value['id'];
//                        $res = M('company')->execute($sql);
//                    }
                }
            }
       $this->assign('company',$company);
       $this->display();
   }

    public function ajaxGetArticle(){
        $model= M('Article');
        $article = $model->page($_POST['p'],10)->order("add_time desc")->select();
        foreach(  $article as $key=>$value){
            $article[$key]['add_time']=date('Y-m-d',$value['add_time']);
            if (empty($article[$key]['cat_name2'])){
                $article[$key]['cat_name2']='中小企业服务署';


            }
            $rand =  rand(1,16);
            $article[$key]['rand']= $rand;
        }
        $this->ajaxreturn($article);
    }

    public function shuju($value='')
    {

        $data = array();
        $data['count'] = M('Company')->where('zizhu=1')->count();
        $map['total_money']  = array('NEQ','0');
        $money = M('Company')->where($map)->field('total_money')->select();
        $a=0;
        foreach ($money as $key => $value) {
            // dump($value);
            $a += $value['total_money'];
        }
        $data['money']=$a;

        return $data;
    }
    public function zizhu($value='')
    {	
    	$map['Name']=array('LIKE','%深圳%');
       	$map['zizhu']="1";
       	$map['total_money']=array('gt',0);
    	$zizhu= M('Company')->where($map)->order('add_time desc')->limit(8)->select();
    	session('zizhu',$zizhu);
    }


    public function article($value='')
    {
    	  $article  = M('Article')->limit(8)->order('add_time desc')->select();
    	  session('article',  $article );
    }




    public function test()
    {
        header("content-type:text/html;charset=utf-8");

//         $action = CONTROLLER_NAME ;
//        if("./Application/Runtime/Html/Home_Index_index.html"){
//            unlink("./Application/Runtime/Html/Home_Index_index.html");
//        }
        include_once('./Application/Runtime/Html/Home_Index_index.html');
    }


}