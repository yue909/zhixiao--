<?php
// +----------------------------------------------------------------------
// | 单用户微信基础类
// +----------------------------------------------------------------------
namespace Home\Controller;

use App\QRcode;
use Think\Controller;

class WeixinController extends Controller{
    //全局相关
    public static $_set; //缓存全局配置
    public static $_shop; //缓存全局配置

    public static $_wx; //缓存微信对象
    public static $_ppvip; //缓存会员通信证模型
    public static $_ppvipmessage; //缓存会员消息模型
    public static $_fx; //缓存分销模型
    public static $_fxlog; //缓存分销新用户推广模型	qd(渠道)=1为朋友圈，2为渠道场景二微码
    public static $_token;
    public static $_location; //用户地理信息
    //信息接收相关
    public static $_revtype; //微信发来的信息类型
    public static $_revdata; //微信发来的信息内容
    //信息推送相关
    //public static $_url='http://shop.hylanca.com/';//推送地址前缀
    public static $_url;
    public static $_wecha_id;
    public static $_actopen;

    public static $WAP;//CMS全局静态变量
	public static $default_str;

    // 自动计算模型
    public static $_demployee;

    public function __construct($options){


        //获取微信配置信息
        self::$_set = M('wx_user')->find();
        self::$_token = self::$_set['w_token'];


        //检测token是否合法
        $tk = $_GET['token'];
        if ($tk != self::$_token) {
            die('token error');
        }
        $user = M('wx_user')->find();
        $options = array(
         			'token'=>self::$_set['w_token'], //填写你设定的key
         			'encodingaeskey'=>self::$_set['aeskey'], //填写加密用的EncodingAESKey
         			'appid'=>self::$_set['appid'], //填写高级调用功能的app id
         			'appsecret'=>self::$_set['appsecret'], //填写高级调用功能的密钥
        );

		self::$_wx = new \Util\Wx\Wechat($options);
        //缓存通行证数据模型
        self::$_ppvip = M('Users');

        self::$_fx = M('Users');

//		self::$default_str = '欢迎关注知小兵,知小兵致力打造项目信息查询平台,让企业轻松获取项目信息,使资助项目更透明！
//
//					第一步:点击“<a href="https://wx.zhixiaobing.com/index.php/Weixin">查资助</a>”,搜索你想要查看的公司;
//					第二步:进入公司详细页面后,点击公司名称下方的“未监控”,我们就可实时推送该企业最近可申请的政府资助项目.';
        self::$default_str = '欢迎关注知小兵，知小兵致力打造项目信息查询平台，让企业轻松获取项目信息';



        //判断验证模式
        if (IS_GET) {
            self::$_wx->valid();
        } else {
            if (!self::$_wx->valid(true)) {
                die('no access!!!');
            }
            //读取微信平台推送来的信息类型存全局
            self::$_revtype = self::$_wx->getRev()->getRevType();

            //读取微型平台推送来的信息存全局
            self::$_revdata = self::$_wx->getRevData();
            self::$_wecha_id = self::$_wx->getRevFrom();
            //读取用户地理信息
            //self::$_location=self::$_wx->getRevData();
            $str = "";

            foreach (self::$_revdata as $k => $v) {
                $str = $str . $k . "=>" . $v . '  ';
            }
            file_put_contents('./Data/app_rev.txt', '收到请求:' . date('Y-m-d H:i:s') . PHP_EOL . '通知信息:' . $str . PHP_EOL . PHP_EOL . PHP_EOL, FILE_APPEND);

        }

    }



    public function index(){
        file_put_contents('./Data/type.txt',self::$_revtype);
        $this->go();

    } //index类结束

    /*微信访问判断主路由控制器by App
    return
     */
    public function go(){

        switch (self::$_revtype) {
            case \Util\Wx\Wechat::MSGTYPE_TEXT:
                $this->checkKeyword(self::$_revdata['Content']);
                //self::$_wx->text(self::$_revdata['Content'])->reply();
                break;
            case \Util\Wx\Wechat::MSGTYPE_EVENT:
                $this->checkEvent(self::$_revdata['Event']);
                break;
            case \Util\Wx\Wechat::MSGTYPE_IMAGE:
                //$this -> checkImg();
                self::$_wx->text('本系统暂不支持图片信息！')->reply();
                break;
            default:
                self::$_wx->text("本系统暂时无法识别您的指令！")->reply();
        }

    } //end go

    /*关键词指引
    return
     */
    public function checkKeyword($key){
        file_put_contents('./Data/content.txt',$key);
        //更新认证服务号的微信用户表信息（24小时内）
        $reUP = $this->updateUser(self::$_wecha_id);


        //App调试模式
        if (substr($key, 0, 5) == 'App-') {
            $this->toApp(substr($key, 5));
        }

        //强制关键词匹配
        //*********************************************************************
        if ($key == '操作指导') {
            $msg = '未配置';
            self::$_wx->text($msg)->reply();
        }
        if ($key == "推广二维码") {
            // 获取用户信息
            $map['openid'] = self::$_revdata['FromUserName'];
            $vip = self::$_ppvip->where($map)->find();
			$channel = M('Channel')->where(array('user_id'=>$vip['user_id']))->find();
            // 用户校正
            if (!$vip) {
                $msg = "用户信息缺失，请重新关注公众号";
                self::$_wx->text($msg)->reply();
                exit();
            } else if($channel['status'] == 0){
                $msg = "很遗憾,您还不是渠道商,请先进入渠道中心进行申请!";
                self::$_wx->text($msg)->reply();
                exit();
			}

            // 过滤连续请求-打开
            // if (F($vip['openid']) != null) {
            //     $msg = "推广二维码正在生成，请稍等！";
            //     self::$_wx->text($msg)->reply();
            //     exit();
            // } else {
            //     F($vip['openid'], $vip['openid']);
            // }

            // 生产二维码基本信息，存入本地文档，获取背景
            $background = $this->createQrcodeBg();
            $qrcode = $this->createQrcode($vip['user_id'], $vip['openid']);

            if (!$qrcode) {
                $msg = "专属二维码 生成失败";
                self::$_wx->text($msg)->reply();
                F($vip['openid'], null);
                exit();
            }
            // 生产二维码基本信息，存入本地文档，获取背景 结束

            // 获取头像信息
            $mark == false; // 是否需要写入将图片写入文件
            $headimg = $this->getRemoteHeadImage($vip['headimgurl']);
            if (!$headimg) {// 没有头像先从头像库查找，再没有就选择默认头像
                if (file_exists('./QRcode/headimg/' . $vip['openid'] . '.jpg')) { // 获取不到远程头像，但存在本地头像，需要更新
                    $headimg = file_get_contents('./QRcode/headimg/' . $vip['openid'] . '.jpg');
                } else {
                    $headimg = file_get_contents('./QRcode/headimg/' . 'default' . '.jpg');
                }
                $mark = true;
            }
            $headimg = imagecreatefromstring($headimg);
            // 获取头像信息 结束

            // 生成二维码推广图片=======================

            // Combine QRcode and background and HeadImg
            $b_width = imagesx($background);
            $b_height = imagesy($background);
            $q_width = imagesx($qrcode);
            $q_height = imagesy($qrcode);
            $h_width = imagesx($headimg);
            $h_height = imagesy($headimg);
			//imagecopyresampled($background, $qrcode, $b_width * 0.24, $b_height * 0.5, 0, 0, $q_width * 1.5, $q_height * 1.5, $q_width, $q_height);
            imagecopyresampled($background, $qrcode, 127,              545,            0, 0, 330,             330,            $q_width, $q_height);

            imagecopyresampled($background, $headimg, 10, 10, 0, 0, 120, 120, $h_width, $h_height);

            // Set Font Type And Color
            $fonttype = './Public/Common/fonts/wqy-microhei.ttc';
            $fontcolor = imagecolorallocate($background, 0x00, 0x00, 0x00);

            // Combine All And Text, Then store in local
           // imagettftext($background, 18, 0, 280, 100, $fontcolor, $fonttype, $vip['nickname']);
		   // imagettftext($background, 18, 0,180, 490, $fontcolor, $fonttype, $vip['nickname']);
		    imagettftext($background, 18, 0,150, 50, $fontcolor, $fonttype, $vip['nickname']);
            imagejpeg($background, './QRcode/promotion/' . $vip['openid'] . '.jpg');

            // 生成二维码推广图片 结束==================

            // 上传下载相应
            if (file_exists(getcwd() . "/QRcode/promotion/" . $vip['openid'] . '.jpg')) {
                $data = array('media' => '@' . getcwd() . "/QRcode/promotion/" . $vip['openid'] . '.jpg');
                $uploadresult = self::$_wx->uploadMedia($data, 'image');
                self::$_wx->image($uploadresult['media_id'])->reply();
            } else {
                $msg = "专属二维码生成失败";
                self::$_wx->text($msg)->reply();
            }

            // 上传下载相应 结束

            // 过滤连续请求-关闭
            F($vip['openid'], null);

            // 后续数据操作（写入头像到本地，更新个人信息）
            if ($mark) {
                $tempvip = $this->apiClient(self::$_revdata['FromUserName']);
                $vip['nickname'] = $tempvip['nickname'];
                $vip['headimgurl'] = $tempvip['headimgurl'];
            } else {
                // 将头像文件写入
                imagejpeg($headimg, './QRcode/headimg/' . $vip['openid'] . '.jpg');
            }

        }

        if ($key == "openid") {
            $map['openid'] = self::$_revdata['FromUserName'];
            $vip = self::$_ppvip->where($map)->find();

            self::$_wx->text($vip['openid'].'--'.$vip['nickname'])->reply();
        }
        //用户自定义关键词匹配
        //*********************************************************************
        $mapkey['keyword'] = $key;
        //用户自定义关键词
        $keyword = M('Wx_keyword');
        $ruser = $keyword->where($mapkey)->find();
        if ($ruser) {
            //进入用户自定义关键词回复
            $this->toKeyUser($ruser);

        }
        //*********************************************************************

        //系统自定义关键词数组
        //$osWgw=array('官网','首页','微官网','Home','home','Index','index');
        //if(in_array($key,$osWgw)){$this->toWgw('index',false);}

        //未知关键词匹配
        //*********************************************************************
        file_put_contents('./Data/zidingyi.txt','未知关键词匹配');
        $this->toKeyUnknow();
    }


    public function checkEvent($event){
        switch ($event) {
            //首次关注事件
            case 'subscribe':
			    //用户关注：判断是否已存在
                //检查用户是否已存在
                $old['openid'] = self::$_revdata['FromUserName'];
				//$old 是关注的用户

                $isold = self::$_ppvip->where($old)->find();
				//如果这个用户已经存在
                if ($isold) {
                    $data['subscribe'] = 1;
                    $re = self::$_ppvip->where($old)->setField('subscribe', 1);
                    self::$_wx->text(self::$default_str)->reply();
                    if($parent_user = self::$_ppvip->where(array('user_id' => $isold['parent_id']))->find()){
                        $msg = array();
                        $msg['touser'] = $parent_user['openid'];
                        $msg['msgtype'] = 'text';
                        $str = "老客户[".$isold['nickname']."]重新关注了知小兵!";
                        $msg['text'] = array('content' => $str);
                        $ree = self::$_wx->sendCustomMessage($msg);
                        //写入个人信息的通知
                        $data = array(
                            'title'=>"老客户[".$isold['nickname']."]重新关注了知小兵!",
                            'message'=>"恭喜,".date("Y-m-d H:i:s",time())."老客户[".$isold['nickname']."]重新关注了知小兵!",
                            'category'=>2,
                            'send_time'=>time(),
                            'status'=>0,
                            'uid'=>$parent_user['user_id']
                        );
                        add_message($data);
                    }




                } else {
                    $pid = 0;
                    $old = array();
                    if (!empty(self::$_revdata['Ticket'])) {
                        $ticket = self::$_revdata['Ticket'];
                        $old = self::$_ppvip->where(array("ticket" => $ticket))->find();
                        $pid = $old["user_id"];
                    }

                    $user = $this->apiClient(self::$_revdata['FromUserName']);


					//file_put_contents('./Data/userinfo.txt', '收到请求:' . date('Y-m-d H:i:s') . PHP_EOL . '通知信息:' . "新用户" . PHP_EOL . PHP_EOL . PHP_EOL, FILE_APPEND);

                    unset($user['groupid']);
                    if ($user) {
                        //追入首次时间和更新时间
                        $user['reg_time'] = $user['cctime'] = time();
                        //追入员工
                        if ($old['user_id']) {
                            $user['parent_id'] = $pid;
                        } else {
                            $user['parent_id'] = 0;
                        }

						$user['oauth'] = 'weixin';
                        $revip = self::$_ppvip->add($user);
						//file_put_contents('./Data/userinfo.txt', '收到请求:' . date('Y-m-d H:i:s') . PHP_EOL . '通知信息:' . "新用户ok" . PHP_EOL . PHP_EOL . PHP_EOL, FILE_APPEND);

						$fp = fopen('./Data/user.txt','w+');
						fwrite($fp,var_export($user,true));
						fclose($fp);
                        //处理父亲
                        $mvip = self::$_ppvip;
                        $parent_user = $mvip->where(array('user_id' => $pid))->find();
                        if ($parent_user) {
							$msg = array();
							$msg['touser'] = $parent_user['openid'];
							$msg['msgtype'] = 'text';
							$str = "恭喜,通过您的分享,[".$user['nickname']."]成为了您的客户!请继续加油";
							$msg['text'] = array('content' => $str);
							$ree = self::$_wx->sendCustomMessage($msg);

							//写入个人信息的通知
							$data = array(
							    'title'=>$user['nickname'].'通过您的分享成为您的客户',
							    'message'=>"恭喜,".date("Y-m-d H:i:s",time())."通过您的分享,[".$user['nickname']."]成为了您的客户!请继续加油",
							    'category'=>2,
							    'send_time'=>time(),
							    'status'=>0,
							    'uid'=>$parent_user['user_id']
							);
							add_message($data);
							//写入个人信息的通知
							$data = array(
							    'title'=>"联系我们",
							    'message'=>"客服电话:<a href='tel:4009661818'>400-966-1818</a>",
							    'category'=>0,
							    'send_time'=>time(),
							    'status'=>0,
							    'uid'=>$parent_user['user_id']
							);
							add_message($data);
							//写入个人信息的通知
							$data = array(
							    'title'=>'会员通知',
							    'message'=>"恭喜您成为知小兵会员,开启您的资助申领之旅!",
							    'category'=>0,
							    'send_time'=>time(),
							    'status'=>0,
							    'uid'=>$parent_user['user_id']
							);
							add_message($data);

                        }

                    } else {
                        self::$_wx->text(self::$default_str)->reply();
                    }

                }
                self::$_wx->text(self::$default_str)->reply();
                break;
            //取消关注事件
            case
            'unsubscribe':
                //更新库内的用户关注状态字段
                $map['openid'] = self::$_revdata['FromUserName'];
                $old = self::$_ppvip->where($map)->find();
                if ($old) {
                    $rold = self::$_ppvip->where($map)->setField('subscribe', 0);
					if($old['parent_id'] !=0){
							if($parent_user = self::$_ppvip->where(array('user_id'=>$old['parent_id']))->find()){
								$msg = array();
								$msg['touser'] = $parent_user['openid'];
								$msg['msgtype'] = 'text';
								$str = "很遗憾,您的客户[".$old['nickname']."]取消了知小兵的关注,赶紧跟进一下吧,加油!";
								$msg['text'] = array('content' => $str);
								$ree = self::$_wx->sendCustomMessage($msg);

								//写入个人信息的通知
								$data = array(
								    'title'=>"[".$old['nickname']."]取消了知小兵的关注!",
								    'message'=>"很遗憾,您的客户[".$old['nickname']."]取消了知小兵的关注,赶紧跟进一下吧,加油!",
								    'category'=>2,
								    'send_time'=>time(),
								    'status'=>0,
								    'uid'=>$parent_user['user_id']
								);
								add_message($data);

							}
					}
                }
                break;
            //自定义菜单点击事件
            case 'CLICK':
                $key = self::$_revdata['EventKey'];
                //self::$_wx->text('菜单点击拦截'.self::$_revdata['EventKey'].'!')->reply();
                switch ($key) {
                    case '#sy':
                        break;
                }
                //不存在拦截命令,走关键词流程
                $this->checkKeyword($key);

                break;

        }
    }

    /*高级调试模式 by App
    $type=调试命令
    $App-openid:获取用户openid
     */
    public function toApp($type)
    {
        $title = "App管理员模式：\n命令：" . $type . "\n结果：\n";

        switch ($type) {
            case 'dkf':
                $str = "人工客服接入！";
                self::$_wx->dkf($str)->reply();
                break;
            case 'openid':
                self::$_wx->text($title . self::$_revdata['FromUserName'])->reply();
                break;
            default:
                self::$_wx->text("App:未知命令")->reply();
        }

    }

    /*自定义关键词模式 by App
    $ruser=关键词记录
     */
    public function toKeyUser($ruser)
    {
        $type = $ruser['type'];
        switch ($type) {
            //文本
            case "1":
                self::$_wx->text($ruser['summary'])->reply();
                break;
            //单图文
            case "2":
                $news[0]['Title'] = $ruser['name'];
                $news[0]['Description'] = $ruser['summary'];
                $img = $this->getPic($ruser['pic']);
                $news[0]['PicUrl'] = $img['imgurl'];
                $news[0]['Url'] = $ruser['url'];
                self::$_wx->news($news)->reply();
                break;
            //多图文
            case "3":
                $pagelist = M('Wx_keyword_img')->where(array('kid' => $ruser['id']))->order('sorts desc')->select();
                $news = array();
                foreach ($pagelist as $k => $v) {
                    $news[$k]['Title'] = $v['name'];
                    $news[$k]['Description'] = $v['summary'];
                    $img = $this->getPic($v['pic']);
                    $news[$k]['PicUrl'] = $img['imgurl'];
                    $news[$k]['Url'] = $v['url'];
                }
                self::$_wx->news($news)->reply();
                break;
            default:
                self::$_wx->text("未知类型的关键词，请联系客服！")->reply();
                break;
        }
    }

    /*未知关键词匹配 by App
     */
    public function toKeyUnknow(){
        //if (strstr($key, "您好") || strstr($key, "你好") || strstr($key, "在吗") || strstr($key, "有人吗")) {
            $str =  self::$_wx->transfer_customer_service()->reply();
            file_put_contents('./Data/test.txt', '收到请求:' . date('Y-m-d H:i:s') . PHP_EOL . '通知信息:' . $str . PHP_EOL . PHP_EOL . PHP_EOL, FILE_APPEND);

        //}
        //self::$_wx->text("未找到此关键词匹配！")->reply();
    }

    /*具体微管网推送方式 by App
    $type=对应应用的类型
    $imglist=true/false 是否以多条返回/最多10条
     */
    public function toWgw($type, $imglist)
    {
        $wgw = F(self::$_uid . "/config/wgw_set"); //微官网设置缓存
        switch ($type) {
            case 'index':
                //准备各项参数
                $title = $wgw['title'] ? $wgw['title'] : '欢迎访问' . self::$_userinfo['wxname'];
                $summary = $wgw['summary'];
                $picid = $wgw['pic'];
                $picurl = $picid ? $this->getPic($picid) : false;
                //封装图文信息
                $news[0]['Title'] = $title;
                $news[0]['Description'] = $summary;
                $news[0]['PicUrl'] = $picurl['imgurl'] ? $picurl['imgurl'] : '#';
                $news[0]['Url'] = self::$_url . '/App/Wgw/Index/uid/' . self::$_uid;
                //推送图文信息
                self::$_wx->news($news)->reply();
                break;
        }
    }

    /*将图文信息封装为二维数组 by App
    $array(Title,Description,PicUrl,Url),$return=false
    Return:新闻数组/或直接推送
     */
    public function makeNews($array, $return = false)
    {
        if (!$array) {
            die('no items!');
        }
        $news[0]['Title'] = $array[0];
        $news[0]['Description'] = $array[1];
        $news[0]['PicUrl'] = $array[2];
        $news[0]['Url'] = $array[3];
        if ($return) {
            return $news;
        } else {
            self::$_wx->news($news)->reply();
        }
    }

    /*获取单张图片 by App
    return
     */
    public function getPic($id)
    {
        $m = M('Upload_img');
        $map['id'] = $id;
        $list = $m->where($map)->find();
        $list['imgurl'] = self::$_url . "/Upload/" . $list['savepath'] . $list['savename'];
        return $list ? $list : false;
    }
    //根据微信接口获取用户信息
    //return array/false 用户信息/未获取。
    public function apiClient($openid)
    {
        $user = self::$_wx->getUserInfo($openid);
        return $user ? $user : FALSE;
    }

    /*认证服务号微信用户资料更新 by App
    return
     */
    public function updateUser($openid){
        // $user = self::$_ppvip->where(array('openid'=>'oIR4Vwgy0xN_HSpmq9ek3AB-Dhdg'))->find();
        // file_put_contents("./Data/user.txt",$user);


        $old = self::$_ppvip->where(array('openid'=>$openid))->find();
        //file_put_contents("./Data/old.txt",$old);

        if ($old) {
            if ((time() - $old['cctime']) > 3600*24) {
                $user = self::$_wx->getUserInfo($openid);
                file_put_contents('./Data/user.txt',$user);
                //当成功拉去数据后
                if ($user) {
                    $user['cctime'] = time();
                    $re = self::$_ppvip->where(array('user_id' => $old['user_id']))->save($user);
                } else {
                    $str = '更新用户资料失败，用户为：' . $openid;
                    file_put_contents('./Data/app_fail.txt', '微信接口失败:' . date('Y-m-d H:i:s') . PHP_EOL . '通知信息:' . $str . PHP_EOL . PHP_EOL . PHP_EOL, FILE_APPEND);
                }
            } else {
                //1天内，直接保存最后的交互时间
                $old['cctime'] = time();
                $re = self::$_ppvip->save($old);
            }
        }
        return true;

    }

    ///////////////////增值方法//////////////////////////
    public function getlevel($exp)
    {
        $data = M('Vip_level')->order('exp')->select();
        if ($data) {
            $level = array();
            foreach ($data as $k => $v) {
                if ($k + 1 == count($data)) {
                    if ($exp >= $data[$k]['exp']) {
                        $level['levelid'] = $data[$k]['id'];
                        $level['levelname'] = $data[$k]['name'];
                    }
                } else {
                    if ($exp >= $data[$k]['exp'] && $exp < $data[$k + 1]['exp']) {
                        $level['levelid'] = $data[$k]['id'];
                        $level['levelname'] = $data[$k]['name'];
                    }
                }
            }
        } else {
            return false;
        }
        return $level;
    }

    public function getCardNoPwd()
    {
        $dict_no = "0123456789";
        $length_no = 10;
        $dict_pwd = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $length_pwd = 10;
        $card['no'] = "";
        $card['pwd'] = "";
        for ($i = 0; $i < $length_no; $i++) {
            $card['no'] .= $dict_no[rand(0, (strlen($dict_no) - 1))];
        }
        for ($i = 0; $i < $length_pwd; $i++) {
            $card['pwd'] .= $dict_pwd[rand(0, (strlen($dict_pwd) - 1))];
        }
        return $card;
    }

    // 获取头像函数
    function getRemoteHeadImage($headimgurl)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_URL, $headimgurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        $headimg = curl_exec($ch);
        curl_close($ch);
        return $headimg;
    }

    public function getQRCode($id, $openid)
    {
        $ticket = self::$_wx->getQRCode($id, 1);

        self::$_ppvip->where(array("user_id" => $id))->save(array("ticket" => $ticket["ticket"]));
        $qrUrl = self::$_wx->getQRUrl($ticket["ticket"]);

        $data = file_get_contents($qrUrl);
        //file_put_contents('./QRcode/qrcode/' . $openid . '.png', $data);
    }

    // 创建二维码
    function createQrcode($id, $openid)
    {
        if ($id == 0 || $openid == '') {
            return false;
        }
        if (!file_exists('./QRcode/qrcode/' . $openid . '.png')) {
            //二维码进入系统
//            $url = 'http://' . $_SERVER['HTTP_HOST'] . __ROOT__ . '/App/Shop/index/ppid/' . $id;
//            \Util\QRcode::png($url, './QRcode/qrcode/' . $openid . '.png', 'L', 6, 2);

            //二维码进入公众号
            $this->getQRCode($id, $openid);
        }
        $qrcode = imagecreatefromstring(file_get_contents('./QRcode/qrcode/' . $openid . '.png'));
        return $qrcode;
    }

    // 创建二维码
    function createEmployeeQrcode($id, $openid)
    {
        if ($id == 0 || $openid == '') {
            return false;
        }
        if (!file_exists('./QRcode/qrcode/' . $id . "employee" . $openid . '.png')) {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . __ROOT__ . '/App/Shop/index/employee/' . $id;
            \Util\QRcode::png($url, './QRcode/qrcode/' . $id . "employee" . $openid . '.png', 'L', 6, 2);
        }
        $qrcode = imagecreatefromstring(file_get_contents('./QRcode/qrcode/' . $id . "employee" . $openid . '.png'));
        return $qrcode;
    }

    // 创建背景
    function createQrcodeBg()
    {
        $autoset['qrcode_background'] = 'QRcode/background/zhixiaobing.jpg';
        if (!file_exists('./' . $autoset['qrcode_background'])) {
            $background = imagecreatefromstring(file_get_contents('./QRcode/background/default.jpg'));
        } else {
            $background = imagecreatefromstring(file_get_contents('./' . $autoset['qrcode_background']));
        }
        return $background;
    }

    // 创建背景
    function createQrcodeBgEmp()
    {
        $autoset['qrcode_emp_background'] = 'QRcode/background/default.jpg';;
        if (!file_exists('./' . $autoset['qrcode_emp_background'])) {
            $background = imagecreatefromstring(file_get_contents('./QRcode/background/default.jpg'));
        } else {
            $background = imagecreatefromstring(file_get_contents('./' . $autoset['qrcode_emp_background']));
        }
        return $background;
    }

    // 关注时返回信息
    function subscribeReturn($msg)
    {

		self::$_wx->text($msg)->reply();

//        $temp = getcwd() . $this->getSubscribePic(self::$_set['wxpicture']);
//        $switchs = file_exists($temp);
//        if (self::$_set['wxswitch'] == '0' || !$switchs) {
//            self::$_wx->text($msg)->reply();
//        } else {
//            $data = array('media' => '@' . $temp);
//            $uploadresult = self::$_wx->uploadMedia($data, 'image');
//            self::$_wx->image($uploadresult['media_id'])->reply();
//        }
    }

    // 获取单张图片
    function getSubscribePic($id)
    {
        $m = M('UploadImg');
        $temparr = split(',', $id);
        foreach ($temparr as $v) {
            if ($v != '') {
                $map['id'] = $v;
                break;
            }
        }
        if ($map) {
            $list = $m->where($map)->find();
            if ($list) {
                $list['imgurl'] = "/Upload/" . $list['savepath'] . $list['savename'];
                $temp = str_replace('/', '/', $list['imgurl']);
            }
        }
        return $temp ? $temp : '';
    }

} //API类结束