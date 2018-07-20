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
use Think\AjaxPage;
use Think\Page;
use Home\Controller\BaseController;
class ToolController extends BaseController {

	public function calculate($value='')
	{
//	    if(session('user_id')){
            if(is_mobile()){
                $this->assign('ismobile','ismobile');
            }
            $this->display();
//        }else{
//            $this->redirect(U('login/login'));
//        }
	}

    public function mcalculate($value='')
    {
//	    if(session('user_id')){
        if(is_mobile()){
            $this->assign('ismobile','ismobile');
        }
        $this->display();
//        }else{
//            $this->redirect(U('login/login'));
//        }
    }
    public function  objectform(){

	    $this->display();
    }

    // 计算器客服表单项目
    public function object(){
        header("Content-Type:text/html;charset=utf-8");
        // dump( M('board')->select());
        if (IS_POST) {
            $data['user_id']=session('user_id')?session('user_id'):'';
            $data['mobile'] =  I('post.lnktel');
            $data['username'] =  I('post.lnkper');
            $data['email'] = '';
            $data['company'] = I('post.lnkcorp');
            I('post.objectvalue')?I('post.objectvalue'):'';
            $data['content'] ='申报的项目：'.I('post.objectvalue').'<br/>留言内容：'.I('post.rmk1').'<br />来源：<a href='.I('post.url').'>'.I('post.url').'</a>';
            $data['add_time'] = time();
            $data['ip_address'] = get_client_ip();
            $data['status'] = '1';
            $code = I('post.code');
           
            // dump($data);
            // 12个小时内不能重复提交五次
            $validate_code = session('validate_code');
            $starttime = time()-12*60*60;
            // $this->ajaxReturn($starttime);
            $map['add_time'] = array(array('EGT',$starttime),array('ELT',time()),'AND');
            $count =M('board')->where($map)->where(array("ip_address"=>$data['ip_address']))->count();
            if ( $count >5) {
                $this->ajaxReturn(array('msg'=>'申请次数太多,请联系客服！',"status"=>'0'));
            }else{
                if($code ==  $validate_code['code']){
                    $board = M('board')->add($data);
                    if ($board) {

                        $this->ajaxReturn(array('msg'=>'提交成功',"status"=>'1'));
                    }else{
                        $this->ajaxReturn(array('msg'=>'提交失败，请咨询客服',"status"=>'0'));
                    }
                }else{
                    $this->ajaxReturn(array('msg'=>'验证码错误',"status"=>'2',$code,$validate_code.'111'));
                }

            }
        }


    }
    public function boards(){
	    $model=M('board');
        $count = $model->count();
        $Page  = new Page($count,15);
        $show = $Page->show();
//            $data = $model->where($map)->page($_GET['p'],10)->order("total_money desc")->select();
        $data = $model->order("add_time desc")->limit($Page->firstRow.','.$Page->listRows)->select();
	   
        $condition = []; 
       

        foreach ($data as $key => $value) {
            $data[$key]['condition'] = explode(',',$value['condition']);
        }
        // dump($condition);
        // dump($condition1);
        // dump($data);
         $this->assign('board',$data);
        $this->assign('page',$show);
	    $this->display();

        }


    public function changeTel($value = '')
    {
        if(IS_POST){

            $model=M('board');
            $map['mobile'] = I('post.mobile');
            $map['board_id'] = I('post.board_id');
            $data = $model->save($map);
            if ($data || empty($data)){
                $res =array('status'=>1);
            }else{
                $res =array('status'=>0);
            }
            $this->ajaxreturn ($res);

        }
    }

    public function status()
    {
        if(IS_POST){

            $model=M('board');

            $data = $model->save($_POST);
            if ($data || empty($data)){
                $res =array('status'=>1);
            }else{
                $res =array('status'=>0);
            }
            $this->ajaxreturn ($res);

        }

    }


    //注册用户
    public function users(){
	    $model=M('users');
	    $map['oauth']='';
	    $count = $model->where($map)->count();
        $page  = new Page($count,15);
	    $show = $page->show();
	    $users= $model->where($map)->order('user_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('users',$users);
        $this->assign('page',$show);
        $this->display();
        // dump(M('users')->where('mobile=18397423845')->find());
//       dump( M()->execute("desc tp_users"));
        // dump((time()));
    }

    public function test1(){
         // header("Content-Type:text/html;charset=utf-8");
	    M('board')->where("mobile='18397423845'")->delete();
    }
}