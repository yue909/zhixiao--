<?php
/**

 * ============================================================================
 * * 版权所有 2015-2027 深圳市知小兵科技有限公司，并保留所有权利。
 * 网站地址: http://www.gopin.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 */
namespace Home\Controller;
use Home\Logic\UsersLogic;
use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\SendBatchSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;

class LoginController extends BaseController {

    public function login(){

        if(!session('user_id')){
            if(is_mobile()){
                $this->display("mlogin");
            }else{
                $this->display();
            }
        }else {

            $this->redirect('/');

        }

    }


   public function userRegmobileCode()
   {
       if (IS_PSOT) {
            $sender = $mobile=I('post.phone');
            $type = I('type');

            $sms_time_out = tpCache('sms.sms_time_out');
            $sms_time_out = $sms_time_out ? $sms_time_out : 180;
            //获取上一次的发送时间
            $send = session('validate_code');
            if(!empty($send) && $send['time'] > time() && $send['sender'] == $sender){
                //在有效期范围内 相同号码不再发送
                $res = array('status'=>-1,'msg'=>'规定时间内,不要重复发送验证码');
            }
            
        
            if(!check_mobile($sender)){
                    $res = array('status'=>-1,'msg'=>'手机号码格式有误');
                    //$send = sendSMS($sender,'您好，你的验证码是：'.$code);
            }
            $code =  mt_rand(1000,9999);
            $send = sendSMS($sender,$code);
            if($send){
                $info['code'] = $code;
                $info['sender'] = $sender;
                $check['is_check'] = 0;
                $info['time'] = time() + $sms_time_out; //有效验证时间
                session('validate_code',$info);
                $res = array('status'=>1,'msg'=>'验证码已发送，请注意查收');
            }else{

                $res = array('status'=>-1,'msg'=>'次数太多,稍后在试!');
            }

            $this->ajaxReturn($res);

       }
   }

    // pC手机密码登录
    public function userLoginaction()
    {
            if (IS_POST) {
                $mobile = I('post.mobile');
                $password =  encrypt(I('post.password'));
                $code = I('post.code');
                $sendcode= session('validate_code');
                if($code != $sendcode['code']){
                    $res =array('msg'=>'验证码错误','status'=>2);
                }else{
                    $users = M('users')->where(array('mobile'=>$mobile,'password'=>$password))->find();
                    if ($users) {
                        $data['last_ip']=get_client_ip();
                        $data['last_login']=time();
                        $data['mobile'] = $mobile;
                        $data['user_id']=$users['user_id'];
                        $users1= M('users')->save($data);
                        session('user_id',$users['user_id']);
                        $res = array('msg'=>'登录成功','status'=>1);
                    }else{
                        $res = array('msg'=>'帐号或者密码错误!','status'=>0);
                    }
                }
                $this->ajaxReturn($res);
            }


    }
//注册
    public function register()
   {
       $this->display();
   }

// 检测手机号是不是正确
    public function userPhonecheck()

   {
       if (IS_POST) {
           $mobile = trim(I('post.phone'));
           $user = M('users')->where('mobile='.$mobile)->find();
           if ($user) {
               $this->ajaxReturn(array('msg'=>'用戶已注册','status'=>'1'));
           }else{
                $this->ajaxReturn(array('msg'=>'可以注册','status'=>'0'));
             
           }
       }
   }

   public function userNameCheck()
   {
       if (IS_POST) {
           $nickname = trim(I('post.nickname'));
           $user = M('users')->where(array('nickname'=>$nickname))->find();
           if ($user) {
               $this->ajaxReturn(array('msg'=>'用戶已注册','status'=>'1'));
           }else{
                $this->ajaxReturn(array('msg'=>'可以注册','status'=>'0'));
             
           }
       }
   }

//   注册
   public function doRegister()
   {
    if (IS_POST) {
        $data['mobile']=I('post.mobile');
        $data['password']=encrypt(I('post.password'));
        $data['password1']=encrypt(I('post.password1'));
        $data['nickname']=trim(I('post.nickname'));
        $data['nickname']=trim(I('post.nickname'));
        $data['reg_time']=time();
        $users = M('users');
        $user= $users ->where(array('mobile'=>$data['mobile']))->find();
        $user1= $users ->where(array("nickname"=>$data['nickname']))->find();
        if($user) {
            $res = array('msg'=>'手机号已经注册','status'=>0);
        }elseif(!empty($user1)){

            $res = array('msg'=>'用户名已存在','status'=>0);

        }elseif($data['password1']!= $data['password']){

            $res = array('msg'=>'密码不一致','status'=>0);

        }elseif($users->add($data)){

            $res = array('msg'=>'注册成功','status'=>1);


        }else{

            $res = array('msg'=>'注册失败','status'=>0);
        }

        return $this->ajaxReturn($res);
    }
       
   }
// 退出登录 
   public function loginOut($value='')
   {
       session('user_id',null);
       if (!session('user_id')){
        $this->ajaxReturn(array('msg'=>'退出成功！','status'=>1));
           
       }else{
         $this->ajaxReturn(array('msg'=>'退出失败！','status'=>0));
           
       }
   }
// 找回密码
   public function forget($value='')
   {  

      $this->display();
   }

// 验证手机号是不是正确
// 发送验证码
  public function sendCode($value='')
  { 

    $sms_time_out = tpCache('sms.sms_time_out');
    $sms_time_out = $sms_time_out ? $sms_time_out : 180;
    if(IS_POST){
      $code =  mt_rand(1000,9999);
      $sender =I('post.phone');

      // session('user_id',$user_id);
      // $this->ajaxReturn($user_id);
       if($send = sendWxCode($sender,$code)){
            $info['code'] = $code;
            $info['sender'] = $sender;
            $check['is_check'] = 0;
            $info['time'] = time() + $sms_time_out; //有效验证时间
            session('validate_code',$info);
            // session('validate_code',$info,300);
            $res = array('status'=>1,'msg'=>'验证码已发送，请注意查收',  'code'=>session('validate_code'));
          }else{
            $res = array('status'=>-1,'msg'=>'验证码发送失败,请联系客服', 'code'=>session('validate_code'));
          }
        $this->ajaxReturn($res);

      }
  }
//检查验证码
  public function checkCode()
  {
    if (IS_POST) {
        $code = I('post.code');
        $sender = I('post.phone');
        $send = session('validate_code');
        $user =M('users')->where("mobile={$sender}")->find();
        if (!$user) {
            $res = array('status'=>2,'msg'=>'手机号不正确' );
        }
        if($code == $send['code']){

          $res = array('status'=>1,'msg'=>'验证码正确' );
       
        }else{
          $res = array('status'=>-1,'msg'=>'验证码错误');
        }
        $this->ajaxReturn($res);
        
    }
  }
  // 提交表忘记密码
  public function doForget($value='')
  {
    if (IS_POST) {

        $code = I('post.code');
        $password = encrypt(trim(I('post.password')));
        $password1 = encrypt(trim(I('post.password1')));
        $mobile = I('post.mobile');
        $send = session('validate_code');

        $data['password']=  $password;
        if ($code!=$send['code'] ){

           $res = array('status'=>3,'msg'=>'验证码错误');

        }else{
            $user = M('users')->where("mobile={$mobile}")->find();
             if ($user){
            
                $user_id = $user['user_id'];
                $result = M('users')->where("user_id=$user_id")->save($data);
                if ($result || $result==0) {

                   $res = array('status'=>1,'msg'=>'密码修改成功');
                }else{

                  $res = array('status'=>5,'msg'=>'修改密码失败，请联系网站管理员');
                }

             }else{

                 $res = array('status'=>0,'msg'=>'用户还没有注册,请注册');

              }

        }

        if ($password != $password1 ) {
          
           $res = array('status'=>2,'msg'=>'重复密码输入错误');
        }

        $user = M('users')->where("mobile={$mobile}")->find();

        $this->ajaxReturn($res);
     } 
  }
    //移动端验证码登录
    public function codelogin(){

        if(IS_POST){
            $send = session('validate_code');
            $code = I('post.code');
            $mobile = I('post.mobile');
            $model = M('users');
            $user =  $model->where("mobile='{$mobile}'")->find();
            if($code==$send['code'] ){
                if($user){
                    $data['last_ip']=get_client_ip();
                    $data['last_login']=time();
                    $data['mobile'] = $mobile;
                    $model->where($data)->save();
                    $res = array('status'=>1,'msg'=>'登录成功');
                    $users = $model->where("mobile='{$mobile}'")->find();
                    session('user_id',$users['user_id']);
                }else{
                    $data['mobile'] = $mobile;
                    $data['nickname']='zxb_'.generate_username();
                    $data['last_ip']=get_client_ip();
                    $data['last_login']=time();
                    $user1 = $model->where($data)->add();
                    if($user1){
                        $res = array('status'=>1,'msg'=>'登录成功');
                        $users = $model->where("mobile='{$mobile}'")->find();
                        session('user_id',$users['user_id']);
                    }else{
                        $res = array('status'=>0,'msg'=>'登录失败');
                    }

                }
            }else{
                $res = array('status'=>0,'msg'=>'验证码不正确');
            }

            $this->ajaxreturn($res);

        }

    }

    public function notice(){
        if(is_mobile()){
            $this->assign('title','知小兵条款须知');
            $this->display();
        }
    }
    public function test(){
        header("content-type:text/html;charset=utf-8");
        dump(M('users')->limit(10)->order("user_id desc")->select());
        $data['nickname']=generate_username();
    }
   // public function send_validate_code($sender,$type){

   //      $sms_time_out = tpCache('sms.sms_time_out');

   //      $sms_time_out = $sms_time_out ? $sms_time_out : 180;

   //      //获取上一次的发送时间

   //      $send = session('validate_code');

   //      if(!empty($send) && $send['time'] > time() && $send['sender'] == $sender){

   //          //在有效期范围内 相同号码不再发送

   //          $res = array('status'=>-1,'msg'=>'规定时间内,不要重复发送验证码');

   //          $this->ajaxReturn($res);

   //      }

   //      $code =  mt_rand(1000,9999);

   //      if(!check_mobile($sender)){

   //          $res = array('status'=>-1,'msg'=>'手机号码格式有误');

   //          $this->ajaxReturn($res);

   //      }



   //      //$send = sendSMS($sender,'您好，你的验证码是：'.$code);

   //      if($send = sendWxCode($sender,$code)){

   //          $info['code'] = $code;

   //          $info['sender'] = $sender;

   //          $check['is_check'] = 0;

   //          $info['time'] = time() + $sms_time_out; //有效验证时间

   //          session('validate_code',$info);

   //          $res = array('status'=>1,'msg'=>'验证码已发送，请注意查收');

   //      }else{

   //          $res = array('status'=>-1,'msg'=>'验证码发送失败,请联系管理员');

   //      }

   //      $this->ajaxReturn($res);

   //      //exit(json_encode($res));

   //  }





    //  public function sms(){
    //    header("Content-Type:text/html;charset=utf-8");  

    //     ini_set("display_errors", "on");
    //    require_once  './Api/dysms/vendor/autoload.php';    //此处为你放置API的路径

    //    Config::load();             //加载区域结点配置

    //    $signName = '知小兵';



    //    $templateCode = 'SMS_105075010';   //短信模板ID



    //    $accessKeyId = "LTAIY5hkpCbATF38";//参考本文档步骤2

    //    $accessKeySecret = "c8sDDJ1SYDCv9QDdG8YMrpF7YoopGe";//参考本文档步骤2

    //    //短信API产品名（短信产品名固定，无需修改）

    //    $product = "Dysmsapi";

    //    //短信API产品域名（接口地址固定，无需修改）

    //    $domain = "dysmsapi.aliyuncs.com";

    //    //暂时不支持多Region（目前仅支持cn-hangzhou请勿修改）

    //    $region = "cn-hangzhou";





    //       $mobile ='17665328672';



 

    //        //初始化访问的acsCleint

    //        $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

    //        DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);

    //        $acsClient= new DefaultAcsClient($profile);

    //        $request = new SendSmsRequest;

    //        //必填-短信接收号码。支持以逗号分隔的形式进行批量调用，批量上限为1000个手机号码,批量调用相对于单条调用及时性稍有延迟,验证码类型的短信推荐使用单条调用的方式

    //        $request->setPhoneNumbers($mobile);



    //    //$request->setPhoneNumbers(17671303317);



    //        //必填-短信签名

    //        $request->setSignName("知小兵");

    //        //必填-短信模板Code

    //        $request->setTemplateCode($templateCode);

    //        //选填-假如模板中存在变量需要替换则为必填(JSON格式),友情提示:如果JSON中需要带换行符,请参照标准的JSON协议对换行符的要求,比如短信内容中包含\r\n的情况在JSON中需要表示成\\r\\n,否则会导致JSON在服务端解析失败

    //        //$request->setTemplateParam("{'username':".$value['nickname']."}");

    //     //    $request->setTemplateParam(json_encode(array(  // 短信模板中字段的值
    //     //     "code"=>'11',
    //     //     "product"=>"dsd"
    //     // ), JSON_UNESCAPED_UNICODE));


    //        // $acsResponse = static::getAcsClient()->getAcsResponse($request);
    //         $request->setTemplateParam("{\"username\":\"".$value['nickname']."\"}");

    //        //$request->setTemplateParam("{\"username\":\"王炫\"}");

    //        //选填-发送短信流水号

    //        //$request->setOutId("1234");

    //        //发起访问请求

    //        $acsResponse = $acsClient->getAcsResponse($request);

    //      dump( $acsResponse) ;

    // }


   
}