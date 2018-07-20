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
use Home\Logic\ArticleLogic;
use QL\QueryList;
use Home\Controller\BaseController;
use Think\Page;
class ArticleController extends BaseController {


    public function searchPatentCount($name = '华为技术'){
        header("Content-type:text/html;Charset=utf-8");
        $name = '华为技术';
        $url = 'http://www.cha-tm.net/chatmbs/tmweb/webkh/baitusoft/modelnew03/sbcxListTJ.jsp?m=http://www.cha-tm.com/ruanjianyanshi/chaxunweb/&z=5f187c9c177d677cf67f9e4f8077a798&c=0&t=2&k='.$name.'&p=1&l=2&b=';


        $content = $this->httpGet($url);

        preg_match_all("/<a.+>(.+)<\/a>/U",$content,$result);
        print_r($result);
        $count = 0;
        if($result){
            foreach ($result[1] as $value){
                if(strpos($value,'有效') !== false){
                    $count = str_replace(array('有效','(',')'),array('','',''),$value);
                    break;
                }
            }
        }
        print_r($count) ;

    }


    function httpPost($url,$data,$header=null){ // 模拟提交数据函数

        $curl = curl_init(); // 启动一个CURL会话

        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在

        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转

        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer

        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包

        //curl_setopt($curl, CURLOPT_COOKIEFILE, $GLOBALS['cookie_file']); // 读取上面所储存的Cookie信息

        //curl_setopt($curl, CURLOPT_COOKIEFILE, 'CgEDFCK4WP7zHtf_mFw8TEOZnzlM6_tyCokA.server11'); // 读取上面所储存的Cookie信息

        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环

        curl_setopt($curl, CURLOPT_HEADER, $header); // 显示返回的Header区域内容

        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回

        $tmpInfo = curl_exec($curl); // 执行操作

        if (curl_errno($curl)) {

            echo 'Errno'.curl_error($curl);

        }



        curl_close($curl); // 关键CURL会话

        return $tmpInfo; // 返回数据

    }

    public function searchPatent(){
        header("Content-type:text/html;Charset=utf-8");
        $url = 'http://www.soopat.com/Home/Result?SearchWord=%E4%B8%AD%E5%85%B4%E9%80%9A%E8%AE%AF%E8%82%A1%E4%BB%BD%E6%9C%89%E9%99%90%E5%85%AC%E5%8F%B8&FMZL=Y&SYXX=Y&WGZL=Y&FMSQ=Y';

        $result = $this->httpGet($url);
        var_dump($result);
        exit();

//        $data = array(
//            'requestModule'=>'PatentSearch',
//            'userId'=>'',
//            'language'=>'cn',
//            'patentSearchConfig'=>array(
//                'Query'=>'华为技术',
//                'TreeQuery'=>'',
//                'Database'=>'wgzl,syxx,fmzl',
//                'Action'=>'Search',
//                'DBOnly'=>0,
//                'Page'=>1,
//                'PageSize'=>10,
//                'GUID'=>'30edce54a440499ba8937289dfd8b82d',
//                'Sortby'=>'-INNOJOY指数,-公开（公告）日,公开（公告）号',
//                'AddOnes'=>'',
//                'DelOnes'=>'',
//                'RemoveOnes'=>'',
//                'Verification'=>null,
//                'SmartSearch'=>'',
//                'TrsField'=>''
//            )
//        );

        $data = array(

            'sc'=>'',
            'q'=>'绿鹰科技',
            'sort'=>'',
            'sortField'=>'',
            'fq'=>'',
            'pageSize'=>10,
            'pageIndex'=>1,
            'type'=>'s',
            'merge'=>'no-merge'
        );

        $result = $this->httpPost($url,$data);
        var_dump($result);








        exit();

        $result = baitu_get_patent_by_name("中兴通讯股份有限公司",2,I('get.p') ? I('get.p') : 1);
        print_r($result);

        exit();
        $name = '华为技术';
        $url = 'http://www.cha-tm.net/chatmbs/tmweb/webkh/baitusoft/modelnew03/sbcxListLB.jsp?m=http://www.cha-tm.com/ruanjianyanshi/chaxunweb/&z=5f187c9c177d677cf67f9e4f8077a798&c=0&t=2&k='.urlencode($name).'&p='.$page.'&l=2&b=';
//        $html = httpGet($url);
//        //print_r($html);
//        $regex4="/<table.*?(class=\"border-all\")(.*?)>.*?<\/table>?/is";
//        $html = preg_match_all($regex4, $html, $table);
//
//        print_r($table);

        $data = QueryList::Query($url,
            array(
                "table" => array("table.border-all[onmouseover]","html"),
            )

        )->data;
        //print_r($data);
        if($data){
            $result = array();
            foreach($data as $key=>$value){
                preg_match_all("/<td.*?>.*?<\/td>/is",$value['table'],$td);
                if($td){

                    preg_match_all("/src=\"(.+?)\"/",$td[0][0],$img);
                    $result[$key]['img'] = $img[1][0];

                    $result[$key]['status'] = trim(strip_tags($td[0][2]));
                    $result[$key]['class'] = trim(strip_tags($td[0][4]));
                    $result[$key]['name'] = trim(strip_tags($td[0][6]));
                    $result[$key]['app_time'] = trim(strip_tags($td[0][8]));
                    $result[$key]['company_name'] = trim(strip_tags($td[0][10]));
                    $result[$key]['reg_time'] = trim(strip_tags($td[0][12]));
                }
            }
        }
        print_r($result);

    }


    public function gaoxinAdd(){
        $gaoxin = M('HighAndNew')->where(array('rid'=>0))->limit(0,300)->order('id asc')->select();
        print_r($gaoxin);
        $model = M('Company');
//        foreach ($gaoxin as $value){
//            $add = "http://restapi.amap.com/v3/geocode/geo?key=051954d68c8eb529188afe22688bf9e2&address=".$value['address']."&city=深圳";
//            $res = (file_get_contents($add));
//            $res = json_decode($res,true);
//            if($res['count']>0){
//                $arr = explode(',',$res['geocodes'][0]['location']);
//                if($value['company_id']>0){
//                    $model->where('id='.$value['company_id'])->save(array('gaoxin'=>1,'lng'=>$arr[0],'lat'=>$arr[1]));
//                    print($arr[0].'-'.$arr[1]);
//                }
//
//                //$this->ajaxReturn(array('status'=>1,'info'=>'已经查找到地址.正在搜索财务,请稍等...','lng'=>$arr[0],'lat'=>$arr[1]));
//            }
//
//        }
    }


    function httpGet($url) {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_TIMEOUT, 500);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($curl, CURLOPT_URL, $url);



        $res = curl_exec($curl);

        curl_close($curl);

        return $res;

    }

    public function test2(){
        header("Content-type:text/html;charset=utf-8");






        $str = <<<str
<tr><tdclass="ma_bluebgma_left"width="18%">统一社会信用代码：</td><tdclass="ma_left">
914403001922038216
</td><tdclass="ma_bluebgma_left">纳税人识别号：</td><tdclass="ma_left">
914403001922038216
</td></tr><tr><tdclass="ma_bluebgma_left"width="15%">注册号：</td><tdclass="ma_left">
440301103097413
</td><tdclass="ma_bluebgma_left">组织机构代码：</td><tdclass="ma_left">
19220382-1
</td></tr><tr><tdclass="ma_bluebgma_left">
法定代表人：
</td><tdclass="ma_left"><aclass="text-primary"href="/people?name=%E5%AD%99%E4%BA%9A%E8%8A%B3&amp;companyname=%E5%8D%8E%E4%B8%BA%E6%8A%80%E6%9C%AF%E6%9C%89%E9%99%90%E5%85%AC%E5%8F%B8">孙亚芳</a><aclass="btnbtn-touzibtn-xsm-l"href="/people?name=%E5%AD%99%E4%BA%9A%E8%8A%B3&amp;companyname=%E5%8D%8E%E4%B8%BA%E6%8A%80%E6%9C%AF%E6%9C%89%E9%99%90%E5%85%AC%E5%8F%B8">他关联9家公司&gt;</a></td><tdclass="ma_bluebgma_left">注册资本：</td><tdclass="ma_left">
3990813.1820万元
</td></tr><tdclass="ma_bluebgma_left">经营状态：</td><tdclass="ma_left">
存续（在营、开业、在册）
</td><tdclass="ma_bluebgma_left">成立日期：</td><tdclass="ma_left">
1987-09-15
</td><tr><tdclass="ma_bluebgma_left">公司类型：</td><tdclass="ma_left">
有限责任公司（法人独资）
</td><tdclass="ma_bluebgma_left">人员规模：</td><tdclass="ma_left">
-</td></tr><tr><tdclass="ma_bluebgma_left">营业期限：</td><tdclass="ma_left">
1987-09-15至2040-04-09
</td><tdclass="ma_bluebgma_left">登记机关：</td><tdclass="ma_left"style="max-width:301px;">
深圳市市场监督管理局
</td></tr><tr><tdclass="ma_bluebgma_left">核准日期：</td><tdclass="ma_left">
2017-08-24
</td><tdclass="ma_bluebgma_left">英文名：</td><tdclass="ma_left"style="max-width:301px;">
ShenzhenHuaweiTechnologyCo.,Ltd.
</td></tr><tr><tdclass="ma_bluebgma_left">
所属地区
</td><tdclass="ma_left">
广东省
</td><tdclass="ma_bluebgma_left">
所属行业
</td><tdclass="ma_left">
制造业</td></tr><tr><tdclass="ma_bluebgma_left">企业地址：</td><tdclass="ma_left"colspan="3">
深圳市龙岗区坂田华为总部办公楼
<aclass="m-lc_a"data-target="#mapModal"data-toggle="modal"id="mapPreviewTwo">查看地图</a><aclass="m-lc_a"data-target="#loginModal"data-toggle="modal"onclick="getCaptcha()">附近公司</a></td></tr><tr><tdclass="ma_bluebgma_left">经营范围：</td><tdclass="ma_left"colspan="3">
程控交换机、传输设备、数据通信设备、宽带多媒体设备、电源、无线通信设备、微电子产品、软件、系统集成工程、计算机及配套设备、终端设备及相关通信信息产品、数据中心机房基础设施及配套产品（含供配电、空调制冷设备、智能管理监控等）的开发、生产、销售、技术服务、工程安装、维修、咨询、代理、租赁；信息系统设计、集成、运行维护；集成电路设计、研发；统一通信及协作类产品，服务器及配套软硬件产品，存储设备及相关软件的研发、生产、销售；无线数据产品（不含限制项目）的研发、生产、销售；通信站点机房基础设施及通信配套设备（含通信站点、通信机房、通信电源、机柜、天线、通信线缆、配电、智能管理监控、锂电及储能系统等）的研发、生产、销售；能源科学技术研究及能源相关产品的研发、生产、销售；大数据产品、物联网及通信相关领域产品的研发、生产、销售；通信设备租赁（不含限制项目）；培训服务；技术认证服务；信息咨询（不含限制项目）；企业管理咨询（不含限制项目）；进出口业务；国内商业、物资供销业业务（不含专营、专控、专卖商品）；对外经济技术合作业务；房屋租赁业务（持许可经营证）；以及其他法律法规不禁止的经营活动（依法须经批准的项目，经相关部门批准后方可开展经营活动）。
</td></tr>
str;


//        $str = str_replace(array(" ","\r\n","\r"),array('','',''),$str);
//
//        preg_match_all("/<tr>(.*)<\/tr>/U",$str,$arr);
//
//        var_dump($arr);
//
//
//
//        exit();
        $url = 'http://120.78.133.212:8000/details/?url=/firm_6b242b475738f45a4dd180564d029aa9.html';
        $content = $this->httpGet($url);
        $str = str_replace(array(" ","\r\n","\r","\n"),array('','','',''),$content);

        preg_match_all("/<tr>(.*)<\/tr>/U",$str,$arr);
        print_r($arr);

        foreach($arr[0] as $key=>$value){
            preg_match_all("/<tdclass=.+>(.+)<\/td>/U",$value,$arr2);

            if($arr2){
                $first = trim($arr2[1][0]);

                if($first == '法定代表人：'){
                    $details['RegistCapi'] =  $arr2[1][3];
                }elseif($first == '统一社会信用代码：'){
                    $updatesql['No'] = trim($arr2[1][1]) ;
                }elseif($first == '组织机构代码：'){
                    $updatesql['Status'] =trim($arr2[1][3]);
                }elseif($first == '公司类型：'){
                    $details['EconKind'] =  trim($arr2[1][1]);
                }elseif($first == '营业期限：'){
                    if(strpos(trim($arr2[1][1]),"至无固定期限") !==false){
                        $details['TeamEnd'] =  '无固定期限';
                        $details['EndDate'] =  '无固定期限';
                    }else{
                        $details['TeamEnd'] =  mb_substr(trim($arr2[1][1]),-10);
                        $details['EndDate'] =  mb_substr(trim($arr2[1][1]),-10);
                    }
                    $details['BelongOrg'] =  trim($arr2[1][3]);

                }elseif($first == '核准日期：'){
                    $details['CheckDate'] =  strtotime(trim($arr2[1][1]));

                }elseif($first == '所属地区'){
                    $details['Province'] =  trim($arr2[1][1]);
                }elseif($first == '经营范围：'){
                    $details['Scope'] =  trim($arr2[1][1]);
                }elseif($first == '企业地址：'){
                    //preg_match_all("/(.+)<adata-toggle(.+)a>/",trim($arr2[1][1]),$address);
                    $x = mb_strpos(trim($arr2[1][1]),"<");
                    $add = mb_substr(trim($arr2[1][1]),0,$x);

                    $details['Address'] = $details['Address2'] =  $add;
                }

            }
        }
        var_dump($details);



    }






    public function test(){
        header("Content-type:text/html;charset=utf-8");


        $company = python_select_company('腾讯');
        var_dump($company);
        exit();


//        $str = "['华为技术有限公司', '华为技术服务有限公司', '深圳市华为技术服务有限公司', '西安华为技术有限公司', '成都华为技术有限公司']['/firm_6b242b475738f45a4dd180564d029aa9.html', '/firm_f3920d6cd49511d33463285b651bc0e2.html', '/firm_0c19ee3d52f3bd74aed7c86734f2a612.html', '/firm_5a265ca51d4d03c2dea04a879f84469d.html', '/firm_8017e72136adbf9e9d5ef8c0b00beebe.html']['孙亚芳', '胡厚崑', '纪平', '徐文伟', '汪涛']['注册资本：3990813.1820万元', '注册资本：15000万人民币', '注册资本：2000万元', '注册资本：30000万', '注册资本：15000万人民币']['成立时间：1987-09-15', '成立时间：2007-07-02', '成立时间：1998-11-25', '成立时间：2008-02-01', '成立时间：2007-06-07']['\n 地址：深圳市龙岗区坂田华为总部办公楼\n ', '\n 地址：廊坊经济技术开发区望京大道西侧\n ', '\n 地址：深圳市龙岗区坂田华为基地B区2号楼\n ', '\n 地址：西安市高新区锦业路127号\n ', '\n 地址：成都高新西区西源大道1899号\n ']";
//
//
//        $arr = explode('[',$str);
//        foreach($arr as $key=>$value){
//            if(trim($value) == ''){
//                unset($arr[$key]);
//            }else{
//                $arr[$key] = str_replace(array("]","\r\n", "\n", "\r","'","\s"," "),array('','','','','',''),$value);
//                $arr[$key] =explode(',',$arr[$key]);
//            }
//
//
//        }
//
//
//        $newarray = array();
//
//        foreach($arr as $key=>$value){
//            foreach($value as $k=>$v){
//                $arr[$key][$k] = str_replace(array("注册资本：","成立时间：","地址：","\s"),array("","","",""),$v);
//            }
//
//        }
//        //print_r(($arr));
//        for($i=0;$i<5;$i++){
//            $newarray[] = array(
//                $arr[1][$i],
//                $arr[2][$i],
//                $arr[3][$i],
//                $arr[4][$i],
//                $arr[5][$i],
//                $arr[6][$i],
//            );
//        }
//
//        print_r(($newarray));




//        $newarray[] = array(
//            $arr[1][0],
//            $arr[2][0],
//            $arr[3][0],
//            $arr[4][0],
//            $arr[5][0],
//            $arr[6][0],
//        );
//
//        $newarray[] = array(
//            $arr[1][1],
//            $arr[2][0],
//            $arr[3][0],
//            $arr[4][0],
//            $arr[5][0],
//            $arr[6][0],
//        );






//        $keyword = I('get.keyword');
//
//        $url = 'http://120.78.133.212:8000/index/?name='.urlencode($keyword);
//
//
//        $content = $this->httpGet($url);
//        var_dump($content);






//        $model = M('Company');
//        $companys = M('Company')->field('id,Scope')->order('id asc')->limit(60000,20000)->select();
//
//        $ins = M('Industry')->select();
//        foreach ($companys as $key=>$value){
//            foreach ($ins as $v){
//                if(strpos($value['Scope'],'（') !== false){
//                    if(strpos($value['Scope'],'婴童用品') !== false || strpos($value['Scope'],'婴') !== false || strpos($value['Scope'],'童') !== false){
//                        $model->where(array('id'=>$value['id']))->save(array('industry'=>$v['id']));
//                        break;
//                    }
//                }
//
//                if($value['Scope'] && strpos($value['Scope'],$v['name']) !== false){
//                    $model->where(array('id'=>$value['id']))->save(array('industry'=>$v['id']));
//                    break;
//                }
//            }
//        }
    }


    // public function index(){     
    //     $article_id = I('article_id',38);
    //     $article = D('article')->where("article_id=$article_id")->find();
    //     $this->assign('article',$article);
    //     $this->display();
    // }
 
    /**
     * 文章内列表页
     */
    public function articleList(){
        // $article_cat = M('ArticleCat')->where("parent_id  = 0")->select();
        // $a = lock_url('zhixiaobing');
        $hash = unlock_url(I('get.hash'));

//        if ($hash!='guanwangrenzheng' || empty($hash) ){
//            $this->redirect('/index');
//        }else{
            $count =  M('Article')->count();
            $Page  = new Page($count,10);
            $show = $Page->show();
            $article = M('Article')->order('add_time desc')->page($_GET['p'],10)->select();
            foreach ($article as $key => $value) {
                $article[$key]['content']=strip_tags( $article[$key]['content']);
                $article[$key]['content'] = trim(mb_substr($article[$key]['content'],0,100,'utf-8'));

            }

            $mapa =array();
            $mapa['Name']=array('LIKE','%深圳%');
            $zizhu= M('Company')->where($mapa)->where('zizhu=1')->order('add_time desc')->limit(4)->select();
            $this->assign('zizhu',$zizhu); 

            $this->assign('title','文章列表');
            $this->assign('article',$article);        
            $this->assign('page',$show);

            if(is_mobile()){
                $action = ACTION_NAME ;
                $this->display("m".$action);
            }else{
                $this->display();
            }
          
           
//        }

    }    
    /**
     * 文章内容页
     */
    public function detail(){


        if(is_mobile()){
            $article_id = I('get.article_id');
            M('article')->where(array("article_id"=>$article_id))->setInc('click');
            $detail = M('article')->where(array("article_id"=>$article_id))->find();
            $this->assign('detail',$detail);
            echo "<div style='display: none'>";
            dump($detail);
            echo "</div>";
            $action = ACTION_NAME ;
            $this->display("m".$action);
        }else{

            $article_id = unlock_url(I('get.article_id'));

            if (!is_numeric($article_id) || empty($article_id)) {

                $this->redirect('articleList');

            }else {
                M('article')->where("article_id={$article_id}")->setInc('click');
                $detail = M('article')->where("article_id={$article_id}")->find();

                if ($detail) {

                    $parent = M('article_cat')->where("cat_id=" . $detail['cat_id'])->find();
                    $this->assign('cat_name', $parent['cat_name']);
                    $this->assign('detail', $detail);
                }

                $this->common();
                $zizhu = M('Company')->where($mapa)->order('total_money desc')->limit(4)->select();
                $title = '文章详情';
                $this->assign('title', $title);
                $this->assign('zizhu', $zizhu);
                $this->display();
            }
        }
        // dump($article);


    }
   public function common()
   {
       $article = M('Article')->order('add_time desc')->limit(50)->select();

      $this->assign('article', $article);
   }

// 关于我们
   public function about()
   {
        if(is_mobile()){

            $this->display('mabout');
        }else{

            $this->display();
        }
   }

   public function zhishi()
   {
       if(is_mobile()) {

           $this->display('mzhishi');
       }else{

           $this->display();
       }
   }

   public function rending($value='')
   {
       if(is_mobile()) {
           $this->display('mrending');
       }else{

               $this->display();
           }
   }

   public function butie($value='')
   {
       if(is_mobile()) {
           $this->display("mbutie");
       }else{

       $this->display();
    }
   }

    public function zizhu(){
        if(is_mobile()){
            $this->display('mzizhu');
        }else{
            $this->display();
        }
    }
   public function guogao()
   {
       $this->newzizhu();
        if(is_mobile()){
            $this->display('mguogao');
        }else{
            $this->display();
        }
   }



   public function test3()
   {
       $nav = M('navigation')->select();
       dump($nav);
       $this->assign('nav',$nav);
   }

// 文章
   public function newzizhu()
   {
        $mapa =array();
        $mapa['Name']=array('LIKE','%深圳%');
        $mapa['zizhu']='1';
        $zizhu= M('Company')->where($mapa)->order('add_time desc')->limit(10)->select();
        $this->assign('zizhu',$zizhu);
   }


   public function gaoxinad($value='')
   {
     $this->display();
   }


   public function gaoxinld()
   {
       $this->display();
   }
    public function gaoxinlds()
    {
        $this->display();
    }
    

   public function zizhuld()
   {
       $this->display();
   }
    public function zizhulds()
    {
        $this->display();
    }

   public function butield(){
       $this->display();
   }
    public function butields(){
        $this->display();
    }
   public function rendingld()
   {
       $this->display();
   }

    public function rendinglds()
    {
        $this->display();
    }

     public function rendingld1()
   {
       $this->display();
   }
    public function rendinglds1()
    {
        $this->display();
    }

    public function guanbiaold()
    {
       $this->display();
    }
    public function guanbiaolds()
    {
        $this->display();
    }
}