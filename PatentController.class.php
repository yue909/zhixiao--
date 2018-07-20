<?php
/**

 * ============================================================================
 * 版权所有 2015-2027 深圳国品品牌策划有限公司，并保留所有权利。
 * 网站地址: http://www.gopin.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: Chris
 * Date: 2015-09-09
 */
namespace Home\Controller;
use Think\Controller;
use Think\AjaxPage;
use Aliyun\Core\Config;

use Aliyun\Core\Profile\DefaultProfile;

use Aliyun\Core\DefaultAcsClient;

use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;

use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;
class PatentController extends Controller {

	//搜素客户
 	public function searchPatent(){
        if (I('get.info')) {
            //htmlspecialchars_decode  把预定义的 HTML 实体 "<"（小于）和 ">"（大于）转换为字符：
            $info = htmlspecialchars_decode(trim(I('get.info')));
            // 正则替换 font
            $info=preg_replace("/<(\/?font.*?)>/","",$info);
            $choice = I('get.choice');

            $data = lst($info,$choice);

            $this->assign('data',$data);
        }else{
            $info='';
        }


    	$this->display();
	}



	//检测所有客户的专利情况并发送通知


    public function checkPatent(){
        $sql = "SELECT c.Name ,u.mobile,u.openid,u.truename,u.nickname FROM tp_company as c,tp_order_company as o , tp_users as u WHERE c.id=o.company_id AND u.user_id=o.user_id limit 10";

        require_once  './Api/dysms/vendor/autoload.php';    //此处为你放置API的路径
        Config::load();             //加载区域结点配置
        $signName = '知小兵';
        $templateCode = 'SMS_114385853';   //短信模板ID 项目启动通知
        $accessKeyId = "LTAIY5hkpCbATF38";//参考本文档步骤2
        $accessKeySecret = "c8sDDJ1SYDCv9QDdG8YMrpF7YoopGe";//参考本文档步骤2
        //短信API产品名（短信产品名固定，无需修改）
        $product = "Dysmsapi";
        //短信API产品域名（接口地址固定，无需修改）
        $domain = "dysmsapi.aliyuncs.com";
        //暂时不支持多Region（目前仅支持cn-hangzhou请勿修改）
        $region = "cn-hangzhou";


        //初始化访问的acsCleint
        $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
        DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
        $acsClient= new DefaultAcsClient($profile);
        $request = new SendSmsRequest;
        $request->setSignName("知小兵");//必填-短信签名


        $wechat = get_wechat_obj();

        //发送短信通知,微信通知

        $date = date('Y-m-d',time());

        $res = M('')->query($sql);
        foreach($res as $key=>$value){
            $data = lst($value['Name'],1);
            foreach($data as $k=>$v){
                //还在申请日前
                if($v['diffday'] < 0 ){
                    //30天内的
                    if($v['diffday'] > -30){
                        $msg = ($value['Name'].'-'.$v['名称'].'距离超过缴费期还有'.(abs(ceil($v['diffday']))).'天');
                    }
                }else{
                    //30天内的
                    if($v['diffday'] <= 30){
                        $msg = ($value['Name'].'-'.$v['名称'].'已过缴费期'.(abs(ceil($v['diffday']))).'天');
                    }
                }

                //向客户发送短信通知

                $mobile = 18320970806;
                //必填-短信接收号码。支持以逗号分隔的形式进行批量调用，批量上限为1000个手机号码,批量调用相对于单条调用及时性稍有延迟,验证码类型的短信推荐使用单条调用的方式
                $request->setPhoneNumbers($mobile);
                //$request->setPhoneNumbers(17671303317);
                //必填-短信模板Code
                $request->setTemplateCode($templateCode);
                $company_name = $value['Name'];


                //选填-假如模板中存在变量需要替换则为必填(JSON格式),友情提示:如果JSON中需要带换行符,请参照标准的JSON协议对换行符的要求,比如短信内容中包含\r\n的情况在JSON中需要表示成\\r\\n,否则会导致JSON在服务端解析失败
                //$request->setTemplateParam("{'username':".$value['nickname']."}");
                //$request->setTemplateParam("{\"username\":\"".$company_name.",\"object\":\"".$b['goods_name']."\"}");

                $request->setTemplateParam('{"username":"'.$company_name.'","object":"'.$v['名称'].'","action":"'.$msg.'"}');

                //发起访问请求 发送短信
                if($mobile){
                    $request->setPhoneNumbers($mobile);
                    $acsResponse = $acsClient->getAcsResponse($request);
                }



/*                $super_admin = M('Users')->where($super_admin_where)->select();
                foreach($super_admin as $admin){
                    if($admin['mobile']){
                        $request->setPhoneNumbers($admin['mobile']);
                        //$acsResponse = $acsClient->getAcsResponse($request);
                    }
                }*/

                $openid = 'oIR4Vwgy0xN_HSpmq9ek3AB-Dhdg';

                //向客户发送微信模板消息 启动通知

                $url = 'https://wx.zhixiaobing.com/index.php/Weixin';
                $url = 'https://wx.zhixiaobing.com/index.php/Weixin';
                $tempData = array(
                    "touser"=>$openid,
                    "template_id"=>'tJYDXjj0hUCFo6VX4sb_3dOgl2NQmD-w63im4bBgwww',
                    "url"=>$url,
                    "topcolor"=>"#333",
                    "data"=>array(
                        "first"=>array("value"=>'专利到期通知','color'=>"#333"),
                        "keyword1"=>array("value"=>$v['名称']."缴费期提醒",'color'=>"#333"),
                        "keyword2"=>array("value"=>$date,'color'=>"#333"),
                        "remark"=>array("value"=>"尊敬的客户:".$msg.".点击查看详情","color"=>"#666"),
                    )
                );
                $wechat->sendTemplateMessage($tempData);




            }
        }

    }


}
