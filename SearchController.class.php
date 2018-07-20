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
class SearchController extends BaseController {
    public $ApiKey = '9d4a807ff4154ae7af97c10d43b250f9';
    public $qqMapKey = 'PJKBZ-PE2K2-MHWUK-CSNUZ-MMKY2-HKBHS' ;
    public $companyModel ;
    public $patentModel ;
    public $trademarkModel ;
    public $qqMapName=222;
    public function _initialize() {
        $key = I(get.keyword);
        $create_time=time();
//        M('search_record')->where(array('keyword'=>$key,'create_time'=> $create_time,'uid'=>0))->save();
        $this->assign('action_name',ACTION_NAME);
        $this->assign('this',CONTROLLER_NAME."-".ACTION_NAME);
        $this->companyModel = M('Company');
        $this->patentModel = M('Patent');
        $this->trademarkModel = M('Trademark');
        // $this->companyRelationModel = D('CompanyRelation');
        // $this->objectRelationModel = D('objectRelation');
        parent::_initialize();
    }
    public function selectpatent($Name){
        $url = 'http://i.yjapi.com/PatentV4/SearchPatents?key='.$this->ApiKey.'&pageSize=50&searchKey='.$Name;
        $result =  json_decode(file_get_contents($url),true);
        return $result;
    }
    
    //查询首页
    public function index(){
       
        header("Content-Type:text/html;charset=utf-8");
        $data = cc_check('Search/back');
        $a = ACTION_NAME;
        if ($data) {
           $this->redirect($data,"a=$a");
        }

        $this->assign('title','公司查询');
        $this->assign('backurl',U('Index/index'));
        
        //$cookie_company = cookie('company');
        $keyword = I('get.keyword');
        $time=time();
        $this->assign('time', $time);
        $hash = unlock_url($_GET['hash']);

        if($keyword != '' && $hash=='guanwangrenzheng'){
            // $this->add_record($keyword,1);
            // $tags = $this->scws($keyword);

            // $sql = "select * from tp_company where ";
            // foreach ($tags as $value){
            //     $sql.= "Name like '%".$value."%' and ";
            // }
            // $sql = substr($sql,0,-4);
            // $sql .= " limit 10";
//            $result = M('')->query($sql);
            
            // $sql = "select * from tp_company where Name like '%".$keyword."%' limit 10";

            $map['Name'] = array('like','%'.$keyword.'%');
            // $result = M('company')->query($sql);
            $count = M('Company')->where($map)->count();
            $Page  = new Page($count,10);
            $show = $Page->show();
            $result = M('Company')->field('id,Name,OperName,StartDate,Status,Address,Address2,EconKind,RegistCapi,Scope,total_money')->where($map)->order("total_money desc")->limit($Page->firstRow.','.$Page->listRows)->select();

            if(count($result) < 1){
                $result = array();
                $companyList = getCompanyListByKeyword($keyword);
                if($companyList['Status'] == 200){
                    foreach($companyList['Result'] as $key => $value){
                        $value['StartDate'] = strtotime($value['StartDate']);
                        $map = array('Name'=>$value['Name']);
                        if(!($company = $this->companyModel->where($map)->find())){
                            $value['add_time'] = time();
                            $value['update_time'] = time();
                            $id = $this->companyModel->add($value);
                            $value['id'] = $id;
                        }else{
                            $value['id'] = $company['id'];
                        }
                        $result[] = $value;
                    }
                }
            }


            //var_dump($result);

            // usort($result,$this->paixu($keyword));
            //var_dump($result);

//      usort($result,function($a,$b){
//              $x = strpos($a['Name'],'深圳');
//              $y = strpos($b['Name'],'深圳');
//              if(strcmp ( $x,  $y) == 0){
//                  return 0;
//              }
//              return strcmp ( $x,  $y) > 0 ? -1 : 1;
//          }
//      );
            // dump($result);
            foreach($result as $key=>$value){
                $result[$key]['Name'] = str_cut($result[$key]['Name'],18);
                $result[$key]['Name'] = str_replace($keyword, '<span style="color:#1baf8d;font-size:16px;">'.$keyword.'</span>', $result[$key]['Name']);
                if(M('ObjectFunding')->where(array('company_id'=>$value['id']))->find()){
                    $result[$key]['z'] = 1;
                    $money = 0;
                    $object = M('ObjectFunding')->where(array('company_id'=>$value['id']))->select();
                    foreach($object as $k=>$v){
                        $money += str_replace(',','',$v['money']);
                    }
                    $result[$key]['money'] = number_format($money,2);
                }

            }
            // if (session('user_id')) {
            // $this->assign('record',$this->unset_keyword(M('SearchRecord')->order(array('create_time'=>'desc'))->where(array('uid'=>session('user_id'),'type'=>1))->limit(10)->select()));
            // }
            $this->assign('result',$result);
            $this->assign('keyword',$keyword);
            $this->assign('page',$show);
        }
        $this->article();
        // dump($result);
        if(is_mobile()){
            $action = ACTION_NAME;
            $this->display('m'.$action);
//            $this->mindex();
        }else{
            $this->display();
        }

    }

    public function mindex()
    {
        header("Content-Type:text/html;charset=utf-8");
        $data = cc_check('Search/back');
        $a = ACTION_NAME;
        if ($data) {
            $this->redirect($data,"a=$a");
        }

        // dump($_GET);die;


        $this->assign('title','公司查询');
        $this->assign('backurl',U('Index/index'));

        //$cookie_company = cookie('company');
        $keyword = I('get.keyword');
        $time=time();
        $this->assign('time', $time);
        $hash = unlock_url($_GET['hash']);
        if($keyword != '' && $hash=='guanwangrenzheng'){
            // $this->add_record($keyword,1);
            // $tags = $this->scws($keyword);

            // $sql = "select * from tp_company where ";
            // foreach ($tags as $value){
            //     $sql.= "Name like '%".$value."%' and ";
            // }
            // $sql = substr($sql,0,-4);
            // $sql .= " limit 10";
//            $result = M('')->query($sql);

            // $sql = "select * from tp_company where Name like '%".$keyword."%' limit 10";

            $map['Name'] = array('like','%'.$keyword.'%');
            // $result = M('company')->query($sql);
            $count = M('Company')->where($map)->count();
            $Page  = new Page($count,10);
            $show = $Page->show();
            $result = M('Company')->where($map)->order("total_money desc")->limit($Page->firstRow.','.$Page->listRows)->select();

            if(count($result) < 1){
                $result = array();
                $companyList = getCompanyListByKeyword($keyword);
                if($companyList['Status'] == 200){
                    foreach($companyList['Result'] as $key => $value){
                        $value['StartDate'] = strtotime($value['StartDate']);
                        $map = array('Name'=>$value['Name']);
                        if(!($company = $this->companyModel->where($map)->find())){
                            $value['add_time'] = time();
                            $value['update_time'] = time();
                            $id = $this->companyModel->add($value);
                            $value['id'] = $id;
                        }else{
                            $value['id'] = $company['id'];
                        }
                        $result[] = $value;
                    }
                }
            }


            //var_dump($result);

            // usort($result,$this->paixu($keyword));
            //var_dump($result);

//      usort($result,function($a,$b){
//              $x = strpos($a['Name'],'深圳');
//              $y = strpos($b['Name'],'深圳');
//              if(strcmp ( $x,  $y) == 0){
//                  return 0;
//              }
//              return strcmp ( $x,  $y) > 0 ? -1 : 1;
//          }
//      );
            // dump($result);
            foreach($result as $key=>$value){
                $result[$key]['Name'] = str_cut($result[$key]['Name'],18);
                $result[$key]['Name'] = str_replace($keyword, '<span style="color:#1baf8d;font-size:16px;">'.$keyword.'</span>', $result[$key]['Name']);
                if(M('ObjectFunding')->where(array('company_id'=>$value['id']))->find()){
                    $result[$key]['z'] = 1;
                    $money = 0;
                    $object = M('ObjectFunding')->where(array('company_id'=>$value['id']))->select();
                    foreach($object as $k=>$v){
                        $money += str_replace(',','',$v['money']);
                    }
                    $result[$key]['money'] = number_format($money,2);
                }

            }
            // if (session('user_id')) {
            // $this->assign('record',$this->unset_keyword(M('SearchRecord')->order(array('create_time'=>'desc'))->where(array('uid'=>session('user_id'),'type'=>1))->limit(10)->select()));
            // }
            $this->assign('result',$result);
            $this->assign('keyword',$keyword);
            $this->assign('page',$show);
        }
        $this->article();
        $this->display();
    }

    public function companyDetails(){

        header("Content-type:text/html;charset=utf-8");
        $id = unlock_url($_GET['id']);

        // dump($id);
        if ( $id ) {

            if(I('get.update')){
                $map['id']=$id;
                $map['update_time']=time();
                M('company')->where($map)->save();
            }
            $company = M('Company')
            ->field('id,Name,OperName,StartDate,Status,Address,Address2,EconKind,RegistCapi,Scope,total_money,logo,No,BelongOrg,update_time,click')
            ->find($id);
            // dump($company);
            //公司基本信息
            if(empty($company['RegistCapi']) || $company['Address'] == null || I('get.update')){
                $company = updateCompanyByName($company);
            }
        
            M('Company')->where(array('id'=>$company['id']))->setInc('click');
            $timediff = timediff($company['StartDate'],time());
            $company['life'] = $timediff['year'];
            $company['patent'] = $company['patent_count'];


            $focus = M('Focus')->where(array('company_id'=>I('get.id')))->find();
            $this->assign('focus',$focus ? 'focus-on' : 'focus-off');
            $praise = M('CompanyPraise')->where(array('cid'=>I('get.id')))->find();
            $this->assign('praise',$praise ? 1 : 0);
            $company['praise_count'] = M('CompanyPraise')->where(array('cid'=>I('get.id')))->count();

      

            $this->assign('qqMapKey',$this->qqMapKey);
            $this->assign('qqMapName',$this->qqMapName);
            $this->assign('company',$company);


            $count =  M('ObjectFunding')->where(array('company_id'=>$company['id']))->count();
            $Page  = new Page($count,10);
            $show = $Page->show();
            $object = M('ObjectFunding')->where(array('company_id'=>$company['id']))->select();
           
            $money = 0;
            $sortarr = array();
            foreach($object as $key => $value){
                $object[$key]['money2'] = intval(floatval($value['money']) * 100);
                $o = M('Object')->find($value['oid']);
                $money += (float)str_replace(',','',$value['money']);
                $object[$key]['object'] = $o['title'];
                $object[$key]['objectid'] = $o['id'];
            }
            //var_dump($object);
            foreach($object as $key => $value){
                foreach($value as $k=>$v){
                    $sortarr[$k][$key] = $v;
                }
            }
            array_multisort($sortarr['money2'],SORT_DESC, $object);

              //商标 /专利 /软著

            $shangbiao =M('trademark')->where(array('company_id'=>$company['id']))->limit(15)->select();
            $patent =M('patent2')->where("applicant='{$company['Name']}'")->limit(15)->select();
            $ruanzhu =M('copyright')->where(array('company_id'=>$company['id']))->limit(15)->select();
            $ruanzhunum =M('copyright')->where(array('company_id'=>$company['id']))->count();
            // dump($ruanzhu);
             foreach ($shangbiao as $key => $value) {
                    $array=explode('@', $value['ImageUrl']);
                    // dump( $array);
                    $shangbiao[$key]['ImageUrl']=$array[0];
            }
            
            $this->assign('shangbiao',$shangbiao);
            $this->assign('patent',$patent);
            $this->assign('patentnum',count($patent));
            $this->assign('ruanzhu',$ruanzhu);
            $this->assign('ruanzhunum',$ruanzhunum);
            $this->assign('num',count($object));
            $this->assign('object',$object);
            $this->assign('money',$money);
            $this->assign('title',$company['Name']);

            // 商标数量 
            $this->assign('trademark_count',M('trademark')->where("company_id=".$id)->count());
            if(is_mobile()){
                $cates=getcatesbypid(0);
                $data=array();
                foreach($cates as $key=>$value){
                    if(empty($value['shop'])){

                        $data[$value['name']]=M('goods')->where($map)->where("cat_id={$value['id']} and is_show=1 and is_on_sale=1 and support!=''")->order('goods_id desc')->select();
                    }else{
                        foreach($value['shop'] as $k=>$v){
                            if(empty($v)){
                                $data[$value['name']]=M('goods')->where($map)->where("cat_id={$value['id']} and is_show=1 and is_on_sale=1 and and support!=''")->order('goods_id desc')->select();
                            }
                        }

                    }
                }
            $this->assign("data",$data);
            $focus = M('focus')->where('company_id='.$id)->find();
            $this->assign('focus',$focus);

            $this->display('mcompanyDetails');
            }else{
                $this->display();
            }

            }else{

            $this->redirect('Search/index');
        }
            // dump($patent);

    }
    public function mcompanyDetails(){

        header("Content-type:text/html;charset=utf-8");
        $id = unlock_url($_GET['id']);

        // dump($id);
        if ( $id ) {
            if(I('get.update')){
                $map['id']=$id;
                $map['update_time']=time();
                M('company')->where($map)->save();
            }
            $company = M('Company')->find($id);
            // dump($company);
            //公司基本信息
            if(empty($company['RegistCapi']) || $company['Address'] == null || I('get.update')){
                $company = updateCompanyByName($company);
            }

            M('Company')->where(array('id'=>$company['id']))->setInc('click');
            $timediff = timediff($company['StartDate'],time());
            $company['life'] = $timediff['year'];
            $company['patent'] = $company['patent_count'];


            $focus = M('Focus')->where(array('company_id'=>I('get.id')))->find();
            $this->assign('focus',$focus ? 'focus-on' : 'focus-off');
            $praise = M('CompanyPraise')->where(array('cid'=>I('get.id')))->find();
            $this->assign('praise',$praise ? 1 : 0);
            $company['praise_count'] = M('CompanyPraise')->where(array('cid'=>I('get.id')))->count();



            $this->assign('qqMapKey',$this->qqMapKey);
            $this->assign('qqMapName',$this->qqMapName);
            $this->assign('company',$company);


            $count =  M('ObjectFunding')->where(array('company_id'=>$company['id']))->count();
            $Page  = new Page($count,10);
            $show = $Page->show();
            $object = M('ObjectFunding')->where(array('company_id'=>$company['id']))->select();

            $money = 0;
            $sortarr = array();
            foreach($object as $key => $value){
                $object[$key]['money2'] = intval(floatval($value['money']) * 100);
                $o = M('Object')->find($value['oid']);
                $money += (float)str_replace(',','',$value['money']);
                $object[$key]['object'] = $o['title'];
                $object[$key]['objectid'] = $o['id'];
            }
            //var_dump($object);
            foreach($object as $key => $value){
                foreach($value as $k=>$v){
                    $sortarr[$k][$key] = $v;
                }
            }
            array_multisort($sortarr['money2'],SORT_DESC, $object);

            //商标 /专利 /软著

            $shangbiao =M('trademark')->where(array('company_id'=>$company['id']))->limit(15)->select();
            $patent =M('patent2')->where("applicant='{$company['Name']}'")->limit(15)->select();
            $ruanzhu =M('copyright')->where(array('company_id'=>$company['id']))->limit(15)->select();
            $ruanzhunum =M('copyright')->where(array('company_id'=>$company['id']))->count();
            // dump($ruanzhu);
            foreach ($shangbiao as $key => $value) {
                $array=explode('@', $value['ImageUrl']);
                // dump( $array);
                $shangbiao[$key]['ImageUrl']=$array[0];
            }

            $this->assign('shangbiao',$shangbiao);
            $this->assign('patent',$patent);
            $this->assign('ruanzhu',$ruanzhu);
            $this->assign('ruanzhunum',$ruanzhunum);
            $this->assign('num',count($object));
            $this->assign('object',$object);
            $this->assign('money',$money);
            $this->assign('title',$company['Name']);

            // 商标数量
            $this->assign('trademark_count',M('trademark')->where("company_id=".$id)->count());

                $this->display();

        }else{

            $this->redirect('Search/index');
        }
        // dump($patent);

    }
    //   //分词
  // public function scws($title){
  //       Vendor('scws.pscws4');
  //       $pscws = new \PSCWS4();
  //       $pscws->set_dict(VENDOR_PATH.'scws/lib/dict.utf8.xdb');
  //       $pscws->set_rule(VENDOR_PATH.'scws/lib/rules.utf8.ini');
  //       $title = $title ? $title : '中文分词';
  //       $pscws->set_ignore(true);
  //       $pscws->send_text($title);
  //       $words = $pscws->get_tops(5);
  //       $tags = array();
  //       foreach ($words as $val) {
  //           $tags[] = $val['word'];
  //       }
  //       $pscws->close();
  //       return $tags;
  //   }
    //用接口回去公司详情
    public function get_company_details($Name){
        $url = 'http://i.yjapi.com/ECISimple/GetDetailsByName?key='.$this->ApiKey.'&keyword='.$Name;
        //$url = 'http://i.yjapi.com/ECISimple/GetDetails?key='.$this->ApiKey.'&keyNo='.$keyNo;
        $res =  json_decode(file_get_contents($url),true);
        return ($res);
    }



 

    //项目列表
    public function objectList(){
        // $user = M('Users')->find(session('user.user_id'));
        // if(!$user['post_id']){
            // header("Location:".U('Index/index'));
        // }else{
        $this->assign('title','资助申报');
        if (IS_GET) {
            $model = M('Object');
            $map = array();
            $data = I('get.');
           // if($data['key']){
           //      $map['title'] = array('like', '%'.$data['key'].'%');
           //  }
            // if($data['status']=='1'){
            //     $map['status'] = $data['status'];
            // }
            // if ($data['status']=='0') {
            //     $map['status'] = $data['status'];
            // }
            if($data['level']){
                $map['level_id'] = $data['level'];
            }

            if($data['efforts']){
                $map['efforts_id'] = $data['efforts'];
            }
            if($data['type']){
                $map['type_id'] = $data['type'];
            }
            // if($data['way']){
            //     $map['wap_id'] = $data['way'];
            // }
            
            if (!session('user_id')) {
                $count = $model->where($map)->count();
                $Page  = new AjaxPage($count,10);
                $show = $Page->show();
               if($count>100){
                   $count=100;
                   $Page->totalRows="100+";
               }
                $data = $model->where($map)->order("id desc")->page($_GET['p'],10)->select();
                $pinggu = 1;
                $this->assign($pinggu);
            }else{
                $count = $model->where($map)->count();
                $Page  = new AjaxPage($count,10);
                $show = $Page->show();
                $data = $model->where($map)->order("id desc")->page($_GET['p'],10)->select();
            }
            // $data = $model->where($map)->order("id desc")->limit($Page->firstRow.','.$Page->listRows)->select();
            // dump($data);
            $this->assign('result',$data);
            $this->assign('page',$show);// 赋值分页输出
            // $this->display("ajaxObjectList");
            }
            //查找项目分类
            $this->assign('level',M('ObjectLevel')->select());
            $this->assign('efforts',M('ObjectEfforts')->select());
            $this->assign('type',M('ObjectType')->select());
            $this->assign('way',M('ObjectWay')->select());

            $title_id = I('get.title');
            $status_id = I('get.status');
            $level_id = I('get.level');
            $efforts_id = I('get.efforts');
            $type_id = I('get.type');
            $way_id = I('get.way');      
            $this->article();
            // $this->assign('statusid',$status_id);
            $this->assign('levelid',$level_id);
            $this->assign('effortsid',$efforts_id);
            $this->assign('typeid',$type_id);
            $this->assign('wayid',$type_id);
            if(is_mobile()){
                $this->display("mobjectList");
            }else{

                $this->display();
            }


        // }


    }
    //ajax获取项目列表
    public function ajaxObjectList(){

        if(IS_POST){
            $model = M('Object');
            $map = array();
            $data = I('post.');

            if($data['key']){
                $map['title'] = array('like', '%'.$data['key'].'%');
            }
            if($data['status']=='1'){
                $map['status'] = $data['status'];
            }
            if ($data['status']=='0') {
                $map['status'] = $data['status'];
            }
            if($data['level']){
                $map['level_id'] = $data['level'];
            }

            if($data['efforts']){
                $map['efforts_id'] = $data['efforts'];
            }
            if($data['type']){
                $map['type_id'] = $data['type'];
            }
            if($data['way']){
                $map['wap_id'] = $data['way'];
            }

            
            if (!session('user_id')) {
                $count = $model->where($map)->count();
                if($count>100){
                    $count=100;
                }
                $Page  = new AjaxPage($count,10);
                $show = $Page->show();
                $Page->totalRows='100+';
                $data = $model->where($map)->order("id desc")->limit($Page->firstRow.','.$Page->listRows)->select();
                $pinggu = 1;
                $this->assign($pinggu);
            }else{

                $count = $model->where($map)->count();
                $Page  = new AjaxPage($count,10);
                $show = $Page->show();
                $data = $model->where($map)->order("id desc")->page($_GET['p'],10)->select();
            }
            // $data = $model->where($map)->order("id desc")->limit($Page->firstRow.','.$Page->listRows)->select();

            $this->assign('result',$data);
            $this->assign('page',$show);// 赋值分页输出
            if(is_mobile()){

                $this->display('majaxObjectList');
            }else{

                $this->display();
            }



        }

       
    }

    public function ajaxObjectDetails(){
        //if(IS_POST){

        $id = I('post.id')?I('post.id') : I('get.id');

        $object = M()->field('o.*,ot.name type_name,ol.name level_name,ow.name way_name')->table('tp_object as o,tp_object_type as ot,tp_object_level as ol,tp_object_way as ow')->where('o.id='.$id.' and o.type_id=ot.id and o.level_id=ol.id and o.wap_id=ow.id')->find();

        if($object['nitice_ids']){
            $where['id'] = array('in',$object['nitice_ids']);
            $object['notice'] = M('Notice')->field('id,title,time')->where($where)->select();
        }
        //if($object['publicity_ids']){
            //$map['id'] = array('in',$object['publicity_ids']);

            $map['oid'] = $object['id'];
            $object['publicity'] = M('Publicity')->field('id,title')->where($map)->select();
        //}

        if($object['publicity']){
            foreach($object['publicity'] as $key => $value){
                $object['publicity'][$key]['count'] = M()->field('c.Name,of.*')->order('c.id asc')->table('tp_company as c,tp_object_funding as of')->where('of.lid='.$value['id'].' and of.company_id=c.id')->count();
                $object['publicity'][$key]['funding'] = M()->field('c.Name,of.*')->order('c.id asc')->table('tp_company as c,tp_object_funding as of')->where('of.lid='.$value['id'].' and of.company_id=c.id')->limit(10)->select();
            }
        }

        // unset($object['content']);

        $this->assign('object',$object);

        $this->article();
        $this->assign('title','资助详情');
        if(is_mobile()){

            $company_id = I('post.company_id')? I('post.company_id'):I('get.company_id');
            $this->assign( 'company_id',$company_id);
//            dump($company_id);
            $action = ACTION_NAME;
            $this->display('m'.$action);
        }else{
            $this->display();
        }



       
    }


    public function loadMoreFunding(){
        if(IS_POST){
            $lid = I('post.lid');
            $limit = I('post.limit');
            $result = M()->field('c.Name,of.*')->order('c.id asc')->table('tp_company as c,tp_object_funding as of')->where('of.lid='.$lid.' and of.company_id=c.id')->limit(($limit-1).",10")->select();
            $sql = "select c.Name,of.* from tp_company as c left JOIN tp_object_funding as of where of.lid and of.company_id=c.id order by c.id asc limit ".($limit+1).",10";
            //echo $sql;
            $this->ajaxReturn($result);
        }

    }



    public function ajaxCompanyDetails(){
        $company = $this->companyRelationModel->getCompany();
        //公司基本信息
        if(empty($company['CompanyDetails'])){
            $res = $this->get_company_details($company['Name']);
            if($res['Status'] == 200){
                $array = array(
                    'company_id'=>$company['id'],
                    'BelongOrg'=>$res['Result']['BelongOrg'],
                    'Province'=>$res['Result']['Province'],
                    'UpdatedDate'=>strtotime($res['Result']['UpdatedDate']),
                    'RegistCapi'=>$res['Result']['RegistCapi'],
                    'EconKind'=>$res['Result']['EconKind'],
                    'Province'=>$res['Result']['Province'],
                    'Address'=>$res['Result']['Address'],
                    'Scope'=>$res['Result']['Scope'],
                    'EndDate'=>strtotime($res['Result']['EndDate']),
                    'TermStart'=>strtotime($res['Result']['TermStart']),
                    'TeamEnd'=>strtotime($res['Result']['TeamEnd']),

                );
                M('CompanyDetails')->add($array);
            }
            $company['CompanyDetails'] = $array;
        }

        $this->assign('company',$company);
        $this->display();
    }

   
    public function companyDetails2(){
        header("Content-type:text/html;charset=utf-8");
        //$this->assign('rongyu',M('ConditionList')->where(array('cid'=>9))->select());

        $company = $this->companyRelationModel->getCompany();
        //公司基本信息
        if(empty($company['RegistCapi']) || $company['Address'] == null || I('get.update') || $company['StartDate'] < 2018){
            if($company['qichacha_url']){
                if($company_details = python_company_details($company['qichacha_url'])){
                    $company_details['TeamEnd'] = strtotime($company_details['TeamEnd']);
                    $company_details['EndDate'] = strtotime($company_details['EndDate']);
                    $company_details['details_source'] = 'python';
                    M('Company')->where(array('id'=>$company['id']))->save($company_details);
                    $company = array_merge($company,$company_details);
                }else{
                    $company = updateCompanyByName($company);
                }

            }else{
                $company = updateCompanyByName($company);
            }

        }
        if(!$company){
            $this->error('没有找到相关的企业');
        }
        M('Company')->where(array('id'=>$company['id']))->setInc('click');
        $timediff = timediff($company['StartDate'],time());
        $company['life'] = $timediff['year'];
        $company['patent'] = $company['patent_count'];

        //print_r(I('id'));
        //print_r(session('user.user_id'));
        if(session('user.user_id')){
            if($company2 = M('CompanyData')->where(array('cid'=>I('id')))->find()){
                $company2['rongyu'] = explode(',',$company2['rongyu']);
                foreach($company2 as $key => $value){
                    if($value == 0){
                        $company2[$key] = '';
                    }
                }
                $company = array_merge($company,$company2);
            }
        }

        //根据规则更新公司专利信息
        if((time()-$company['patent_update']) >  24*3600*7 || $company['click']%3 == 0 || I('get.update')){
            $count = array();
            $url = 'http://120.78.133.212:8000/get_patent/?keyword='.urlencode($company['Name']).'&t=2&p=1';
            $res = json_decode(file_get_contents($url),true);
            $company['patent_count'] = $res['cubePatentSearchResponse']['total_hits'];
            M('Company')->where(array('id'=>I('get.id')))->save(array('patent_count'=>$company['patent_count']));

        }
        //更新公司软著
        if((time()-$company['copyright_update']) >  24*3600*7 || $company['click']%3 == 0 || I('get.update')){

            $model = M('Copyright');

            $copyright = getCopyRightListByKeyword($company['Name']);
            if($copyright['Status'] == 200){
                foreach($copyright['Result'] as $key=>$value){
                    $temp = array(
                        'company_id'=>$company['id'],
                        'Owner'=>$value['Owner'],
                        'Category'=>$value['Category'],
                        'Name'=>$value['Name'],
                        'ShortName'=>$value['ShortName'],
                        'RegisterNo'=>$value['RegisterNo'],

                        'PublishDate'=>strtotime($value['PublishDate']),
                        'FinishDate'=>strtotime($value['FinishDate']),

                        'VersionNo'=>$value['VersionNo'],
                        'RegisterAperDate'=>strtotime($value['VersionNo']),
                    );
                    if(!M('Copyright')->where(array('RegisterNo'=>$value['RegisterNo'],'company_id'=>$company['id']))->find()){
                        $copyrightid = $model->add($temp);
                        $temp['id'] = $copyrightid;
                    }
                    $result['Copyright'][]=$temp;
                }
                //更新著作数量和更新日期
                M('Company')->where(array('id'=>$company['id']))->save(array('copyright_count'=>$copyright['Paging']['TotalRecords'],'copyright_update'=>time()));
                $company['copyright_count'] = $copyright['Paging']['TotalRecords'];
            }else{
                M('Company')->where(array('id'=>$company['id']))->save(array('copyright_update'=>time()));
            }
        }

        if(time()-$company['trademark_update'] > 24*3600*7 || $company['click']%3 ==0 || I('update')){
            $trademark_count = baitu_get_parent_count_by_name($company['Name']);

            M('Company')->where(array('id'=>I('get.id')))->save(
                array(
                    'is_select'=>1,
                    'trademark_update'=>time(),
                    'trademark_count'=>$trademark_count,
                )
            );
            $company['trademark_count'] = $trademark_count;


            /*          $trademark = selectTrademarkByApplicant($company['Name']);
                        if($trademark['Status'] == 200){
                            foreach($trademark['Result'] as $key => $value){
                                $temp = array();
                                $temp['company_id'] = $company['id'];
                                $temp['ID'] = $value['ID'];
                                $temp['RegNo'] = $value['RegNo'];
                                //$temp['IntCls'] = $value['IntCls'];
                                $temp['Name'] = $value['Name'];
                                $temp['AppDate'] = strtotime($value['AppDate']);
                                $temp['ApplicantCn'] = $value['ApplicantCn'];
                                $temp['ApplicantEn'] = $value['ApplicantEn'];
                                $temp['PatentImage'] = $value['PatentImage'];
                                $temp['Agent'] = $value['Agent'];
                                $temp['Status'] = $value['Status'];
                                $temp['FlowStatus'] = ($value['FlowStatus']);
                                $temp['FlowStatusDesc'] = $value['FlowStatusDesc'];
                                $temp['HasImage'] = $value['HasImage'];
                                $temp['ImageUrl'] = $value['ImageUrl'];

                                if(!$this->trademarkModel->where(array('ID'=>$value['ID'],'company_id'=>$company['id']))->find()){
                                    $this->trademarkModel->add($temp);
                                }
                            }
                            //更新公司信息
                            M('Company')->where(array('id'=>I('get.id')))->save(
                                array(
                                    'is_select'=>1,
                                    'trademark_update'=>time(),
                                    'trademark_count'=>$trademark['Paging']['TotalRecords'],
                                )
                                );
                            $company['trademark_count'] = $trademark['Paging']['TotalRecords'];

                        }else{
                            M('Company')->where(array('id'=>I('get.id')))->save(array('brand_update'=>time()));

                        }*/

        }

        $focus = M('Focus')->where(array('company_id'=>I('get.id'),'user_id'=>session('user.user_id')))->find();
        $this->assign('focus',$focus ? 'focus-on' : 'focus-off');
        $praise = M('CompanyPraise')->where(array('cid'=>I('get.id'),'uid'=>session('user.user_id')))->find();
        $this->assign('praise',$praise ? 1 : 0);
        $company['praise_count'] = M('CompanyPraise')->where(array('cid'=>I('get.id')))->count();


        $this->assign('qqMapKey',$this->qqMapKey);
        $this->assign('qqMapName',$this->qqMapName);
        $this->assign('company',$company);
//      $ConditionCategoryModel = M('ConditionCategory');
//      $ConditionRoleModel = M('ConditionRole');
//      $ConditionListModel = M('ConditionList');
        $goods = M('Goods')->where(array('is_recommend'=>1))->select();
//      $money2 = 0;
//      foreach($goods as $key=>$value){
//          unset($goods[$key]['goods_content']);
//          unset($goods[$key]['goods_content2']);
//          unset($goods[$key]['goods_content3']);
//          unset($goods[$key]['goods_content4']);
//          $conditionIds = $ConditionRoleModel->where(array('gid'=>$value['goods_id']))->select();
//          foreach($conditionIds as $k=>$v){
//              $category = $ConditionCategoryModel->find($v['cid']);
//              $conditionIds[$k]['name'] = $category['name'];
//              $conditionIds[$k]['value'] = $ConditionListModel->find($v['ccid']);
//          }

//             $goods[$key]['condition'] = $conditionIds;
//             $flag = 0;
//             $ok = 0;
//             $time = timediff(time(),$company['StartDate']);

//             foreach($goods[$key]['condition'] as $k=>$v){
//                 //成立时间
//                 if($v['cid'] == 1){


//                     if($v['value']['type'] == 1){
//                         if($time['year'] > $v['value']['value2']){
//                             if($v['value']['is_one']){
//                                 //$strTable .= "<li class='error'>成立时间超过规定年限".($time['year']-$v['value']['value2'])."年,不能做</li>";
//                                 $flag++;
//                             }
//                         }else{
//                             $ok++;
//                         }

//                     }else if($v['value']['type'] == 2){
//                         if($v['value']['value'] > $time['year']){
//                             if($v['value']['is_one']){
//                                 $flag++;
//                             }
//                         }else{
//                             $ok++;
//                         }
//                     }
//                     //专利数量
//                 }else if($v['cid'] == 2){
//                     if($v['value']['type'] == 2){
//                         if($v['value']['value'] > $company['patent_count']){
//                             if($v['value']['is_one']){
//                                 $flag++;
//                             }
//                         }else{
//                             $ok++;
//                         }
//                     }
//                 }

//             }
//             if($flag){
//                 $goods[$key]['display'] = 'none';
//             }else{
//                 $goods[$key]['display'] = 'block';
//             }
//             //按照80%计算
//             if($ok == 2){
//                 if(!empty($goods[$key]['grants']) || $goods[$key]['grants'] != 0){
//                     if($time['year'] >=5) $time['year'] = 5;
//                     if(strpos($goods[$key]['grants'],'-')){
//                         $goods[$key]['zizhu'] = explode('-',$goods[$key]['grants']);

//                         $goods[$key]['zizhu'] = number_format($goods[$key]['zizhu'][1] * (80+$time['year']+($time['month']/100))/100,2);
//                     }else{
//                         $goods[$key]['zizhu'] = $goods[$key]['grants'];
//                         //$goods[$key]['zizhu'] = number_format(((float)$goods[$key]['grants']) * (80+$time['year']+($time['month']/100))/100,2);
//                     }
//                 }
//             //按照60%计算
//             }elseif($ok == 1){
//                 if(!empty($goods[$key]['grants'])){
//                      if($time['year'] >=5) $time['year'] = 5;

//                      if(strpos($goods[$key]['grants'],'-')){
//                         $goods[$key]['zizhu'] = explode('-',$goods[$key]['grants']);
//                         $goods[$key]['zizhu'] = number_format($goods[$key]['zizhu'][1] * (60+$time['year']+($time['month']/100))/100,2);

//                         //var_dump($goods[$key]['zizhu'][1]."*(60+".$time['year']."+(".$time['month']."/10"+"))/100");

//                     }else{
//                         $goods[$key]['zizhu'] = $goods[$key]['grants'];
//                        // $goods[$key]['zizhu'] = number_format(((float)$goods[$key]['grants']) * (60+$time['year']+($time['month']/100))/100,2);

//                     }
//                 }
//             //按照40%计算
//             }else{
//                 if(!empty($goods[$key]['grants'])){
//                     if($time['year'] >=5) $time['year'] = 5;
//                     if(strpos($goods[$key]['grants'],'-')){
//                         $goods[$key]['zizhu'] = explode('-',$goods[$key]['grants']);
//                         $goods[$key]['zizhu'] = number_format($goods[$key]['zizhu'][1] * (40+$time['year']+($time['month']/100))/100,2);
//                         if($goods[$key]['zizhu'] < $goods[$key]['zizhu'][0]){
//                             $goods[$key]['zizhu'] = $goods[$key]['zizhu'][0] + (number_format($goods[$key]['zizhu'][1] * ($time['year']+($time['month']/100))/100,2));
//                         }

//                     }else{
//                         $goods[$key]['zizhu'] = $goods[$key]['grants'];


// //                         $goods[$key]['zizhu'] = number_format(((float)$goods[$key]['grants']) * (40+$time['year']+($time['month']/100))/100,2);
// //                         if($goods[$key]['zizhu'] < $goods[$key]['grants']){
// //                             $goods[$key]['zizhu'] = $goods[$key]['grants'] + (number_format($goods[$key]['zizhu'][1] * ($time['year']+($time['month']/100))/100,2));
// //                         }
//                     }


//                 }
//             }
//             if($goods[$key]['zizhu'] > 0){
//                 $money2 += $goods[$key]['zizhu'];
//             }
//          if($value['check_function']){
//              $function = 'check_'.$value['check_function'];
//              if(in_array($function,get_class_methods($this))){
//                  $goods[$key]['support'] = $this->$function($company['Name']);
//              }
//          }
//      }

//     $this->assign('money2',$money2);



        $goodscategory = M('GoodsCategory')->select();
        foreach($goodscategory as $key=>$value){
            foreach($goods as $k=>$v){
                if($v['cat_id'] == $value['id']){
                    $goodscategory[$key]['goodslist'][] = $v;
                }
            }
        }
        $this->assign('goodscategory',$goodscategory);



        //查询这个企业的资助

        //$object = M()->field('o.*,ot.name type_name,ol.name level_name,ow.name way_name')->table('tp_object as o,tp_object_type as ot,tp_object_level as ol,tp_object_way as ow')->where('o.id='.$id.' and o.type_id=ot.id and o.level_id=ol.id and o.wap_id=ow.id')->find();


        $object = M('ObjectFunding')->where(array('company_id'=>$company['id']))->select();

        $money = 0;
        $sortarr = array();
        foreach($object as $key => $value){
            $object[$key]['money2'] = intval(floatval($value['money']) * 100);
            $o = M('Object')->find($value['oid']);
            $money += (float)str_replace(',','',$value['money']);
            $object[$key]['object'] = $o['title'];
        }
        //var_dump($object);
        foreach($object as $key => $value){
            foreach($value as $k=>$v){
                $sortarr[$k][$key] = $v;
            }
        }

        array_multisort($sortarr['money2'],SORT_DESC, $object);


        $this->assign('object',$object);
        $this->assign('money',$money);

        $this->assign('title',$company['Name']);

        $this->display();
    }

    public function selecttrademark($Name){
        $url = 'http://i.yjapi.com/tm/Search?keyword='.$Name.'&key='.$this->ApiKey.'&pageSize=50';
        $result =  json_decode(file_get_contents($url),true);
        return $result;
    }

    public function add_record($keyword,$type){
        // if (!session('user.user_id')) {
        //     // session('user.user_id')='0';
        // }

        if($res = M('SearchRecord')->where(array('keyword'=>$keyword,'uid'=>session('user.user_id'),'type'=>$type))->find()){
            if(!($res['create_time'] > strtotime(date('Y-m-d',time())) && $res['create_time'] < strtotime(date('Y-m-d',time()))+3600*24)){
                M('SearchRecord')->add(array('keyword'=>$keyword,'create_time'=>time(),'uid'=>session('user.user_id'),'type'=>$type));
            }
        }else{
            M('SearchRecord')->add(array('keyword'=>$keyword,'create_time'=>time(),'uid'=>session('user.user_id'),'type'=>$type));
        }
    }
    public function remmberKeyWord(){
        if(IS_POST){
            $keyword = I('post.company');
            if($keyword){
                $this->add_record($keyword,7);
            }
        }

    }


    public function getCompanyDetails(){
        if($name = I('get.name')){
            $company = M('Company')->where(['Name'=>$name])->find();

            $company['StartDate'] = date("Y-m-d",$company['StartDate']);

            $this->ajaxReturn($company);
        }
    }

    public function productList(){
        header("Content-Type:text/html;charset=utf-8");

        $this->assign('title','同行查询');
        $this->assign('backurl',U('User/index')); 
        //$cookie_company = cookie('company');
        $keyword =  trim(I('get.keyword'));
        $time =  I('get.time')? I('get.time'):time();
        if (intval((time()-$time))/60>10) {

           $this->display('Public/links');

        }else{

            if($keyword){
                $map['Scope'] = array('like','%'.$keyword.'%');
                $map['Province'] = array('like','%'.$keyword.'%');
                $map['_logic'] = 'OR';
                $count = M('Company')->where($map)->count();
                $page = new Page($count,10);

                //$sql = "SELECT * FROM tp_company WHERE Scope like '%".$keyword."%' ORDER BY MID(total_money,0) +1 desc limit ".$page->firstRow.",".$page->listRows;

                $result = M('Company')->order('zizhu desc')->where($map)->limit($page->firstRow,$page->listRows)->select();



                foreach($result as $key=>$value){
                    $result[$key]['Name'] = str_cut($result[$key]['Name'],18);
                    $result[$key]['Scope'] = str_replace($keyword, '<span style="color:#1baf8d;font-size:12px;float:none;">'.$keyword.'</span>', $result[$key]['Scope']);
                    if(M('ObjectFunding')->where(array('company_id'=>$value['id']))->find()){
                        $result[$key]['z'] = 1;
                        $total_money = number_format(M('ObjectFunding')->where(array('company_id'=>$value['id']))->sum('money'),2);
                        $result[$key]['money'] = $total_money;
                    }
                }



                // dump($result);


    //            $sortarr = array();
    //            foreach($result as $key => $value){
    //                $result[$key]['money2'] = intval(floatval($value['money']) * 100);
    //            }
    //            foreach($result as $key => $value){
    //                foreach($value as $k=>$v){
    //                    $sortarr[$k][$key] = $v;
    //                }
    //            }

                //array_multisort($sortarr['money2'],SORT_DESC, $result);

                //$result = array_slice($result,$page->firstRow,$page->listRows);


    //            $sortarr = array();
    //            foreach($result as $key => $value){
    //                $result[$key]['money2'] = intval(intval($value['money']) * 100);
    //            }
    //            foreach($result as $key => $value){
    //                foreach($value as $k=>$v){
    //                    $sortarr[$k][$key] = $v;
    //                }
    //            }
    //            array_multisort($sortarr['money2'],SORT_DESC, $result);



                $this->assign('page',$page->show());
                $this->assign('result',$result);
                // dump($result);
                // dump($result);
                $this->assign('keyword',$keyword);
            }
            if(is_mobile()){
                $this->display("mproductList");
            }else{
                $this->article();
                $this->display();
            }

        }
    }


    public function mproductList(){
        header("Content-Type:text/html;charset=utf-8");

        $this->assign('title','同行查询');
        $this->assign('backurl',U('User/index'));
        //$cookie_company = cookie('company');
        $keyword =  trim(I('get.keyword'));
        $time =  I('get.time')? I('get.time'):time();
        if (intval((time()-$time))/60>10) {

            $this->display('Public/links');

        }else{

            if($keyword){
                $map['Scope'] = array('like','%'.$keyword.'%');
                $map['Province'] = array('like','%'.$keyword.'%');
                $map['_logic'] = 'OR';
                $count = M('Company')->where($map)->count();
                $page = new Page($count,10);

                //$sql = "SELECT * FROM tp_company WHERE Scope like '%".$keyword."%' ORDER BY MID(total_money,0) +1 desc limit ".$page->firstRow.",".$page->listRows;

                $result = M('Company')->order('zizhu desc')->where($map)->limit($page->firstRow,$page->listRows)->select();



                foreach($result as $key=>$value){
                    $result[$key]['Name'] = str_cut($result[$key]['Name'],18);
                    $result[$key]['Scope'] = str_replace($keyword, '<span style="color:#1baf8d;font-size:12px;float:none;">'.$keyword.'</span>', $result[$key]['Scope']);
                    if(M('ObjectFunding')->where(array('company_id'=>$value['id']))->find()){
                        $result[$key]['z'] = 1;
                        $total_money = number_format(M('ObjectFunding')->where(array('company_id'=>$value['id']))->sum('money'),2);
                        $result[$key]['money'] = $total_money;
                    }
                }



                $this->assign('page',$page->show());
                $this->assign('result',$result);
                // dump($result);
                // dump($result);
                $this->assign('keyword',$keyword);
            }
//                dump($result);
                $this->display();


        }
    }
    public function companyList(){
        header("Content-Type:text/html;charset=utf-8");
        
        $this->assign('title','公司查询');
        $this->assign('backurl',U('Index/index'));
        
        //$cookie_company = cookie('company');
        $keyword = I('post.key');
        
        if($keyword != ''){
            $this->add_record($keyword,1);
            // $tags = $this->scws($keyword);

            // $sql = "select * from tp_company where ";
            // foreach ($tags as $value){
            //     $sql.= "Name like '%".$value."%' and ";
            // }
            // $sql = substr($sql,0,-4);
            // $sql .= " limit 10";
//            $result = M('')->query($sql);
//
            $sql = "select * from tp_company where Name like '%".$keyword."%' limit 10";

            //$map['Name'] = array('like','%'.$keyword.'%');
            $result = M('company')->query($sql);

            if(count($result) < 1){
                $result = array();
                $companyList = getCompanyListByKeyword($keyword);
                if($companyList['Status'] == 200){
                    foreach($companyList['Result'] as $key => $value){
                        $value['StartDate'] = strtotime($value['StartDate']);
                        $map = array('Name'=>$value['Name']);
                        if(!($company = $this->companyModel->where($map)->find())){
                            $value['add_time'] = time();
                            $value['update_time'] = time();
                            $id = $this->companyModel->add($value);
                            $value['id'] = $id;
                        }else{
                            $value['id'] = $company['id'];
                        }
                        $result[] = $value;
                    }
                }
            }


            //var_dump($result);

            usort($result,$this->paixu($keyword));
            //var_dump($result);

//      usort($result,function($a,$b){
//              $x = strpos($a['Name'],'深圳');
//              $y = strpos($b['Name'],'深圳');
//              if(strcmp ( $x,  $y) == 0){
//                  return 0;
//              }
//              return strcmp ( $x,  $y) > 0 ? -1 : 1;
//          }
//      );
            //var_dump($result);
            foreach($result as $key=>$value){
                $result[$key]['Name'] = str_cut($result[$key]['Name'],18);
                $result[$key]['Name'] = str_replace($keyword, '<span style="color:#1baf8d;font-size:14px;">'.$keyword.'</span>', $result[$key]['Name']);
                if(M('ObjectFunding')->where(array('company_id'=>$value['id']))->find()){
                    $result[$key]['z'] = 1;
                    $money = 0;
                    $object = M('ObjectFunding')->where(array('company_id'=>$value['id']))->select();
                    foreach($object as $k=>$v){
                        $money += str_replace(',','',$v['money']);
                    }
                    $result[$key]['money'] = number_format($money,2);
                }

            }
            $this->assign('record',$this->unset_keyword(M('SearchRecord')->order(array('create_time'=>'desc'))->where(array('uid'=>session('user.user_id'),'type'=>1))->limit(10)->select()));
            $this->assign('result',$result);
            $this->assign('keyword',$keyword);
        }

        $this->display();
    }

    public function companyList2(){
        header("Content-Type:text/html;charset=utf-8");

        $this->assign('title','公司查询');
        $this->assign('backurl',U('Index/index'));

        //$cookie_company = cookie('company');
        $keyword =  I('post.keyword') ? I('post.keyword') : I('post.key');
        if($keyword == ''){
            $this->error('企业名称不能为空.');
        }else{
            $this->add_record($keyword,1);
        }
        $map['Name'] = array('like','%'.$keyword.'%');
        $result = M('Company')->where($map)->limit(10)->select();


        if(!$result || count($result) < 1){
            $result = array();

            if($companyList = python_select_company($keyword)){
                print_r($companyList);
                foreach($companyList as $key => $value){
                    $temp_company = array(
                        'Name'=>$value[0],
                        'add_time'=>time(),
                        'update_time'=>time(),
                        'qichacha_url'=>$value[1],
                        'OperName'=>$value[2],
                        'RegistCapi'=>$value[3],
                        'StartDate'=>strtotime($value[4]),
                        'CheckDate'=>strtotime($value[4]),
                        'Status'=>$value[6],
                        'source'=>'python'
                    );

                    $map = array('Name'=>$value[0]);
                    if(!($company = $this->companyModel->where($map)->find())){
                        $temp_company['id'] = $this->companyModel->add($temp_company);
                    }else{
                        $temp_company['id'] = $company['id'];
                    }
                    $result[] = $temp_company;
                }
            }else{

            }

        }


        //var_dump($result);

        usort($result,$this->paixu($keyword));
        //var_dump($result);

        usort($result,function($a,$b){
                $x = strpos($a['Name'],'深圳');
                $y = strpos($b['Name'],'深圳');
                if(strcmp ( $x,  $y) == 0){
                    return 0;
                }
                return strcmp ( $x,  $y) > 0 ? -1 : 1;
            }
        );
        //var_dump($result);
        foreach($result as $key=>$value){
            $result[$key]['Name'] = str_cut($result[$key]['Name'],18);
            $result[$key]['Name'] = str_replace($keyword, '<span style="color:#1baf8d;font-size:14px;">'.$keyword.'</span>', $result[$key]['Name']);
            if(M('ObjectFunding')->where(array('company_id'=>$value['id']))->find()){
                $result[$key]['z'] = 1;
                $money = 0;
                $object = M('ObjectFunding')->where(array('company_id'=>$value['id']))->select();
                foreach($object as $k=>$v){
                    $money += str_replace(',','',$v['money']);
                }
                $result[$key]['money'] = number_format($money,2);
            }

        }
        $this->assign('record',$this->unset_keyword(M('SearchRecord')->order(array('create_time'=>'desc'))->where(array('uid'=>session('user.user_id'),'type'=>1))->limit(10)->select()));
        $this->assign('result',$result);
        $this->assign('keyword',$keyword);
        $this->display();
    }

    public function paixu($keyword){
        return function($a,$b) use ($keyword){
            $res = strcmp(strpos($a['Name'],'深圳'),strpos($b['Name'],'深圳'));
            if( $res == 0  ){
                similar_text($a['Name'],$keyword,$percent1);
                similar_text($b['Name'],$keyword,$percent2);
                if($percent1 == $percent2){
                    return 0;
                }else if($percent1 < $percent2){
                    return 1;
                }else{
                    return -1;
                }
            }else if( $res > 0 ){
                return -1;
            }else{
                return 1;
            }



        };
    }


    /**
     * 分类列表显示
     */
    public function categoryList(){
        $this->display();
    }

    /**
     * 商品列表页
     */


    public function goodsList(){
        $filter_param = array(); // 帅选数组
        $id = I('get.id',1); // 当前分类id
        $brand_id = I('brand_id',0);
        $spec = I('spec',0); // 规格
        $attr = I('attr',''); // 属性
        $sort = I('sort','goods_id'); // 排序
        $sort_asc = I('sort_asc','asc'); // 排序
        $price = I('price',''); // 价钱
        $start_price = trim(I('start_price','0')); // 输入框价钱
        $end_price = trim(I('end_price','0')); // 输入框价钱
        if($start_price && $end_price) $price = $start_price.'-'.$end_price; // 如果输入框有价钱 则使用输入框的价钱
        $filter_param['id'] = $id; //加入帅选条件中
        $brand_id  && ($filter_param['brand_id'] = $brand_id); //加入帅选条件中
        $spec  && ($filter_param['spec'] = $spec); //加入帅选条件中
        $attr  && ($filter_param['attr'] = $attr); //加入帅选条件中
        $price  && ($filter_param['price'] = $price); //加入帅选条件中

        $goodsLogic = new \Home\Logic\GoodsLogic(); // 前台商品操作逻辑类
        // 分类菜单显示
        $goodsCate = M('GoodsCategory')->where("id = $id")->find();// 当前分类
        //($goodsCate['level'] == 1) && header('Location:'.U('Home/Channel/index',array('cat_id'=>$id))); //一级分类跳转至大分类馆
        $cateArr = $goodsLogic->get_goods_cate($goodsCate);
        // 帅选 品牌 规格 属性 价格
        $cat_id_arr = getCatGrandson ($id);
        $filter_goods_id = M('goods')->where("is_on_sale=1 and cat_id in(".  implode(',', $cat_id_arr).") ")->cache(true)->getField("goods_id",true);

        // 过滤帅选的结果集里面找商品
        if($brand_id || $price)// 品牌或者价格
        {
            $goods_id_1 = $goodsLogic->getGoodsIdByBrandPrice($brand_id,$price); // 根据 品牌 或者 价格范围 查找所有商品id
            $filter_goods_id = array_intersect($filter_goods_id,$goods_id_1); // 获取多个帅选条件的结果 的交集
        }
        if($spec)// 规格
        {
            $goods_id_2 = $goodsLogic->getGoodsIdBySpec($spec); // 根据 规格 查找当所有商品id
            $filter_goods_id = array_intersect($filter_goods_id,$goods_id_2); // 获取多个帅选条件的结果 的交集
        }
        if($attr)// 属性
        {
            $goods_id_3 = $goodsLogic->getGoodsIdByAttr($attr); // 根据 规格 查找当所有商品id
            $filter_goods_id = array_intersect($filter_goods_id,$goods_id_3); // 获取多个帅选条件的结果 的交集
        }

        $filter_menu  = $goodsLogic->get_filter_menu($filter_param,'goodsList'); // 获取显示的帅选菜单
        $filter_price = $goodsLogic->get_filter_price($filter_goods_id,$filter_param,'goodsList'); // 帅选的价格期间
        $filter_brand = $goodsLogic->get_filter_brand($filter_goods_id,$filter_param,'goodsList',1); // 获取指定分类下的帅选品牌
        $filter_spec  = $goodsLogic->get_filter_spec($filter_goods_id,$filter_param,'goodsList',1); // 获取指定分类下的帅选规格
        $filter_attr  = $goodsLogic->get_filter_attr($filter_goods_id,$filter_param,'goodsList',1); // 获取指定分类下的帅选属性

        $count = count($filter_goods_id);
        $page = new Page($count,4);
        if($count > 0)
        {
            $goods_list = M('goods')->where("goods_id in (".  implode(',', $filter_goods_id).")")->order("$sort $sort_asc")->limit($page->firstRow.','.$page->listRows)->select();
            $filter_goods_id2 = get_arr_column($goods_list, 'goods_id');
            if($filter_goods_id2)
                $goods_images = M('goods_images')->where("goods_id in (".  implode(',', $filter_goods_id2).")")->cache(true)->select();
        }
        $goods_category = M('goods_category')->where('is_show=1')->cache(true)->getField('id,name,parent_id,level'); // 键值分类数组
        $this->assign('goods_list',$goods_list);
        $this->assign('goods_category',$goods_category);
        $this->assign('goods_images',$goods_images);  // 相册图片
        $this->assign('filter_menu',$filter_menu);  // 帅选菜单
        $this->assign('filter_spec',$filter_spec);  // 帅选规格
        $this->assign('filter_attr',$filter_attr);  // 帅选属性
        $this->assign('filter_brand',$filter_brand);// 列表页帅选属性 - 商品品牌
        $this->assign('filter_price',$filter_price);// 帅选的价格期间
        $this->assign('goodsCate',$goodsCate);
        $this->assign('cateArr',$cateArr);
        $this->assign('filter_param',$filter_param); // 帅选条件
        $this->assign('cat_id',$id);
        $this->assign('page',$page);// 赋值分页输出
        $this->assign('sort_asc', $sort_asc == 'asc' ? 'desc' : 'asc');
        C('TOKEN_ON',false);

        if($_GET['is_ajax'])
            $this->display('ajaxGoodsList');
        else
            $this->display();
    }

    public function percentage($goods){

    }

    public function checkData($companyfield,$attr_value,$score){
        if(preg_match('/^([1-9]+)(.+)(下)/',$attr_value,$temp)){
            print_r($temp);
            if($temp[1]){
                if($companyfield >= $temp[1]){
                    $res  = (int)($temp[1]/$companyfield) * 100;
                }else{
                    $res  = $score;
                }
            }
        }else if(preg_match('/^([1-9]+)(.+)(上)/',$attr_value,$temp)){
            if($temp[1]){
                if($companyfield >= $temp[1]){
                    $res =  $score;
                }else{
                    $res =  (int)$companyfield / $temp[1] * 100;
                }
            }
        }
        return $res;
    }



    public function checkCondition($fieldvalue,$condition){

        if($condition['type'] == 1){//区间型
            if($condition['is_one']){
                $fieldvalue = floatval($fieldvalue);
                $condition['value'] = floatval($condition['value']);
                $condition['value2'] = floatval($condition['value2']);
                //var_dump($fieldvalue);
                //var_dump($condition['value']);
                //var_dump($condition['value2']);
                if(($fieldvalue) >= ($condition['value']) && ($fieldvalue) < ($condition['value2'])){
                    $value = $condition['score'];
                }else{
                    $value = $condition['info'];
                }
            }else{
                $value = $fieldvalue >$condition['value'] && $fieldvalue < $condition['value2'] ? $condition['score'] : 0;
            }
        }else if($condition['type'] == 2){//超越型
            if($condition['is_one']){
                if($fieldvalue >= $condition['value']){
                    $value =$condition['score'];
                }else{
                    $value =$condition['info'];
                }
            }else{
                if($fieldvalue >= $condition['value']){
                    $value = $condition['score'];
                }else if($fieldvalue > 0){
                    $value = $fieldvalue/$condition['value']*$condition['score'];
                }else{
                    $value =0;
                }

            }
        }else if($condition['type'] == 3){
            $attr_value = explode("\n",$condition['value']);
            $attr_value2 = explode("\n",$condition['value2']);
            foreach($attr_value as $k=>$v){
                if(floatval($fieldvalue) >= floatval($v)){
                    $value = floatval($attr_value2[$k]);
                    break;
                }
            }
        }else if($condition['type'] == 4){
            if($condition['is_one']){
                if(in_array($condition['id'],$fieldvalue)){
                    $value =$condition['score'];
                }else{
                    $value = $condition['info'];
                }
            }else{
                $value = $condition['score']/2;
            }
        }
        return $value;
    }
    /**
     * 商品列表页 ajax 翻页请求 查询
     */
    public function ajaxGoodsList() {
//      $company = I('post.');
//      $goods = M('Goods')->select();
//      foreach($goods as $key=>$value){
//          if($value['check_function']){
//              $function = 'check_'.$value['check_function'];
//              if(in_array($function,get_class_methods($this))){
//                  $goods[$key]['support'] = $this->$function($companyname['Name']);
//              }
//          }
//      }
//      $goodscategory = M('GoodsCategory')->select();
//
//
//      foreach($goodscategory as $key=>$value){
//          foreach($goods as $k=>$v){
//              if($v['cat_id'] == $value['id']){
//                  $goodscategory[$key]['goodslist'][] = $v;
//              }
//          }
//      }
//        $this->assign('goodscategory',$goodscategory);
        $this->display();
    }
    public function check_gaoxin($name){
        if(M('HighAndNew')->where(array('name'=>$name,'type'=>1))->find()){

            return '已认证';
        }else{
            return '未认证';
        }
    }
    public function checkGoodsRole(){
        $goodsid = I('gid');
        $role = M('condition_role')->where(array('gid'=>$goodsid))->select();
        foreach($role as $value){
            $r[] = $value['ccid'];
        }
        $where['id'] = array('in',$r) ;
        $where['score'] = array('neq',0) ;

        $condition_list = M('condition_list')->order('cid asc')->where($where)->select();
        $this->ajaxReturn($condition_list);
    }

    /**
     * 商品详情页
     */
    public function goodsInfo(){
        C('TOKEN_ON',true);
        $goodsLogic = new \Home\Logic\GoodsLogic();
        $goods_id = I("get.id");
        $goods = M('Goods')->where("goods_id = $goods_id")->find();
        if(empty($goods)){
            $this->tp404('此商品不存在或者已下架');
        }
        if($goods['brand_id']){
            $brnad = M('brand')->where("id =".$goods['brand_id'])->find();
            $goods['brand_name'] = $brnad['name'];
        }
        $goods_images_list = M('GoodsImages')->where("goods_id = $goods_id")->select(); // 商品 图册
        $goods_attribute = M('GoodsAttribute')->getField('attr_id,attr_name'); // 查询属性
        $goods_attr_list = M('GoodsAttr')->where("goods_id = $goods_id")->select(); // 查询商品属性表
        $filter_spec = $goodsLogic->get_spec($goods_id);

        $spec_goods_price  = M('spec_goods_price')->where("goods_id = $goods_id")->getField("key,price,store_count"); // 规格 对应 价格 库存表
        //M('Goods')->where("goods_id=$goods_id")->save(array('click_count'=>$goods['click_count']+1 )); //统计点击数
        $commentStatistics = $goodsLogic->commentStatistics($goods_id);// 获取某个商品的评论统计
        $this->assign('spec_goods_price', json_encode($spec_goods_price,true)); // 规格 对应 价格 库存表
        $goods['sale_num'] = M('order_goods')->where("goods_id=$goods_id and is_send=1")->count();

        //商品促销
        if($goods['prom_type'] == 3)
        {
            $prom_goods = M('prom_goods')->where("id = {$goods['prom_id']}  AND is_close=0")->find();
            $this->assign('prom_goods',$prom_goods);// 商品促销
        }

        $this->assign('commentStatistics',$commentStatistics);//评论概览
        $this->assign('goods_attribute',$goods_attribute);//属性值
        $this->assign('goods_attr_list',$goods_attr_list);//属性列表
        $this->assign('filter_spec',$filter_spec);//规格参数
        $this->assign('goods_images_list',$goods_images_list);//商品缩略图
    $goods['discount'] = round($goods['shop_price']/$goods['market_price'],2)*10;
        $this->assign('goods',$goods);
        $this->display();
    }

    /**
     * 商品详情页
     */
    public function detail(){
        //  form表单提交
        C('TOKEN_ON',true);
        $goodsLogic = new \Home\Logic\GoodsLogic();
        $goods_id = I("get.id");
        $goods = M('Goods')->where("goods_id = $goods_id")->find();
        $this->assign('goods',$goods);
        $this->display();
    }

    /*
     * 商品评论
     */
    public function comment(){
        $goods_id = I("goods_id",'0');
        $this->assign('goods_id',$goods_id);
        $this->display();
    }

    /*
     * ajax获取商品评论
     */
    public function ajaxComment(){
        $goods_id = I("goods_id",'0');
        $commentType = I('commentType','1'); // 1 全部 2好评 3 中评 4差评
        if($commentType==5){
            $where = "goods_id = $goods_id and parent_id = 0 and img !='' ";
        }else{
            $typeArr = array('1'=>'0,1,2,3,4,5','2'=>'4,5','3'=>'3','4'=>'0,1,2');
            $where = "goods_id = $goods_id and parent_id = 0 and ceil((deliver_rank + goods_rank + service_rank) / 3) in($typeArr[$commentType])";
        }
        $count = M('Comment')->where($where)->count();

        $page = new AjaxPage($count,5);
        $show = $page->show();
        $list = M('Comment')->where($where)->order("add_time desc")->limit($page->firstRow.','.$page->listRows)->select();
        $replyList = M('Comment')->where("goods_id = $goods_id and parent_id > 0")->order("add_time desc")->select();

        foreach($list as $k => $v){
            $list[$k]['img'] = unserialize($v['img']); // 晒单图片
        }
        $this->assign('commentlist',$list);// 商品评论
        $this->assign('replyList',$replyList); // 管理员回复
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }

    /*
     * 获取商品规格
     */
    public function goodsAttr(){
        $goods_id = I("get.goods_id",'0');
        $goods_attribute = M('GoodsAttribute')->getField('attr_id,attr_name'); // 查询属性
        $goods_attr_list = M('GoodsAttr')->where("goods_id = $goods_id")->select(); // 查询商品属性表
        $this->assign('goods_attr_list',$goods_attr_list);
        $this->assign('goods_attribute',$goods_attribute);
        $this->display();
    }
     /**
     * 商品查询列表页
     */
    public function search(){

        $filter_param = array(); // 帅选数组
        $id = I('get.id',0); // 当前分类id
        $brand_id = I('brand_id',0);
        $sort = I('sort','goods_id'); // 排序
        $sort_asc = I('sort_asc','asc'); // 排序
        $price = I('price',''); // 价钱
        $start_price = trim(I('start_price','0')); // 输入框价钱
        $end_price = trim(I('end_price','0')); // 输入框价钱
        if($start_price && $end_price) $price = $start_price.'-'.$end_price; // 如果输入框有价钱 则使用输入框的价钱
        $filter_param['id'] = $id; //加入帅选条件中
        $brand_id  && ($filter_param['brand_id'] = $brand_id); //加入帅选条件中
        $price  && ($filter_param['price'] = $price); //加入帅选条件中
        $q = urldecode(trim(I('q',''))); // 关键字查询
        $q  && ($_GET['q'] = $filter_param['q'] = $q); //加入帅选条件中
        //if(empty($q))
        //    $this->error ('请输入查询关键词');

        $goodsLogic = new \Home\Logic\GoodsLogic(); // 前台商品操作逻辑类
        $filter_goods_id = M('goods')->where("is_on_sale=1 and goods_name like '%{$q}%'  ")->cache(true)->getField("goods_id",true);

        // 过滤帅选的结果集里面找商品
        if($brand_id || $price)// 品牌或者价格
        {
            $goods_id_1 = $goodsLogic->getGoodsIdByBrandPrice($brand_id,$price); // 根据 品牌 或者 价格范围 查找所有商品id
            $filter_goods_id = array_intersect($filter_goods_id,$goods_id_1); // 获取多个帅选条件的结果 的交集
        }

        $filter_menu  = $goodsLogic->get_filter_menu($filter_param,'search'); // 获取显示的帅选菜单
        $filter_price = $goodsLogic->get_filter_price($filter_goods_id,$filter_param,'search'); // 帅选的价格期间
        $filter_brand = $goodsLogic->get_filter_brand($filter_goods_id,$filter_param,'search',1); // 获取指定分类下的帅选品牌

        $count = count($filter_goods_id);
        $page = new Page($count,4);
        if($count > 0)
        {
            $goods_list = M('goods')->where("goods_id in (".  implode(',', $filter_goods_id).")")->order("$sort $sort_asc")->limit($page->firstRow.','.$page->listRows)->select();
            $filter_goods_id2 = get_arr_column($goods_list, 'goods_id');
            if($filter_goods_id2)
                $goods_images = M('goods_images')->where("goods_id in (".  implode(',', $filter_goods_id2).")")->cache(true)->select();
        }
        $goods_category = M('goods_category')->where('is_show=1')->cache(true)->getField('id,name,parent_id,level'); // 键值分类数组
        $this->assign('goods_list',$goods_list);
        $this->assign('goods_category',$goods_category);
        $this->assign('goods_images',$goods_images);  // 相册图片
        $this->assign('filter_menu',$filter_menu);  // 帅选菜单
        $this->assign('filter_brand',$filter_brand);// 列表页帅选属性 - 商品品牌
        $this->assign('filter_price',$filter_price);// 帅选的价格期间
        $this->assign('goodsCate',$goodsCate);
        $this->assign('filter_param',$filter_param); // 帅选条件
        $this->assign('page',$page);// 赋值分页输出
        $this->assign('sort_asc', $sort_asc == 'asc' ? 'desc' : 'asc');
        C('TOKEN_ON',false);

        if($_GET['is_ajax'])
            $this->display('ajaxGoodsList');
        else
            $this->display();
    }

    /**
     * 商品查询列表页
     */
    public function ajaxSearch()
    {
        $goods_id = I("goods_id"); // 商品id
        $goods_num = I("goods_num");// 商品数量
        $goods_spec = I("goods_spec"); // 商品规格
        $result = $this->cartLogic->addCart($goods_id, $goods_num, $goods_spec,session_id,$this->user_id); // 将商品加入购物车
        exit(json_encode($result));
    }
    //多重查询
    public function getPatentBySearch($keyword,$pageIndex=1){
        $url = 'http://i.yjApi.com/Patent/Search?key='.$this->ApiKey.'&searchkey='.$keyword.'&pageIndex='.$pageIndex;
        return json_decode(file_get_contents($url),true);
    }
    //详情查询
    public function getPatentBySearchDetails($keyword){
        $url = 'http://i.yjApi.com/Patent/GetDetails?key='.$this->ApiKey.'&id='.$keyword;
        return json_decode(file_get_contents($url),true);
    }
    //公司专利查询
    public function getPatentBySearchList($keyword,$pageIndex=1){
        $url = 'http://i.yjapi.com/Patent/SearchPatents?key='.$this->ApiKey.'&searchKey='.$keyword.'&pageIndex='.$pageIndex;
        return json_decode(file_get_contents($url),true);
    }




    public function getPatentList($keyword){

        //$url = 'http://i.yjapi.com/Patent/Search?key='.$this->ApiKey.'&pageSize=50&searchKey='.$keyword;
        $url = 'http://i.yjapi.com/PatentV4/SearchPatents?key='.$this->ApiKey.'&pageSize=50&searchKey='.$keyword;
        return json_decode(file_get_contents($url),true);
    }
    public function getTrademarkList($keyword,$pageIndex=1){
        $url = 'http://i.yjapi.com/tm/Search?key='.$this->ApiKey.'&keyword='.$keyword.'&pageIndex='.$pageIndex;
        return json_decode(file_get_contents($url),true);
    }
    public function getTrademarkDetails($id){
        $url = 'http://i.yjapi.com/tm/GetDetails?key='.$this->ApiKey.'&id='.$id;
        return json_decode(file_get_contents($url),true);
    }
    
    //软件著作权多重查询
    public function getCopyRightList($keyword){
        $url = 'http://i.yjapi.com/CopyRight/GetSoftwareCr?key='.$this->ApiKey.'&personName='.$keyword;
        return json_decode(file_get_contents($url),true);
    }
    //软件著作权查询
    public function getCopyRightList2($keyword,$pageIndex=1){
        $url = 'http://i.yjapi.com/CopyRight/SearchSoftwareCr?key='.$this->ApiKey.'&searchkey='.$keyword.'&pageIndex='.$pageIndex;
        return json_decode(file_get_contents($url),true);
    }
    
    
    //根据申请人查询商标
    public function selectTrademarkByApplicant($keyword,$pageIndex=1){
        $url = 'http://i.yjapi.com/tm/SearchByApplicant?key='.$this->ApiKey.'&keyword='.$keyword.'&pageIndex='.$pageIndex;
        $result =  json_decode(file_get_contents($url),true);
        return $result;
    }


    //软件注册权查询
    public function ruanzhu(){

        $data = cc_check('Search/back');
        $a = ACTION_NAME;
        if ($data) {
           $this->redirect($data,"a=$a");
        }
        // $this->assign('history',$this->unset_keyword(M('SearchRecord')->order(array('create_time'=>'desc'))->where(array('uid'=>session('user.user_id'),'type'=>3))->limit(10)->select()));
        $this->assign('backurl',U('Index/index'));
        $this->assign('title','软著查询');
        $this->assign('right','<span id="submit-button">查询</span>');
        $this->assign('placeholder','例如,知小兵');
        
        
        $type = I('get.type') ? I('get.type') : 1;
        $keyword = urldecode(I('get.keyword'));
        // $this->assign('type',$type);

        $this->assign('keyword',$keyword);
         $hash = unlock_url(I('get.hash'));
        if($keyword != '' && $hash=="guanwangrenzheng"){
            // $this->add_record($keyword,3);
            if($type == 1){
                $map['Name']=array('like','%'.$keyword.'%');
                $count = M('copyright')->where($map)->count();
                // $result = $this->getCopyRightList2($keyword,I('get.p') ? I('get.p') : 1);
                $page= new Page($count,10);
                $result = M('copyright')->where($map)->limit($page->firstRow.','.$page->listRows)->select();
                // dump($result);

                $this->assign('page',$page->show());
                $this->assign('result',$result);
                // $this->display();

            }
        }
        $this->article();

        if(is_mobile()){
            $this->assign('ruanzhu',$result);
            $this->assign('ruanzhu1','ruanzhu');
            $this->display('msearch');
//            $this->mindex();
        }else{
            $this->display();
        }

    }



    //商标查询
    public function shangbiao(){
        // $this->assign('history',$this->unset_keyword(M('SearchRecord')->order(array('create_time'=>'desc'))->where(array('uid'=>session('user.user_id'),'type'=>4))->limit(10)->select()));
   
        // dump(session_is_registered());die;

        $data = cc_check('Search/back');
        $a = ACTION_NAME;
        if ($data) {
           $this->redirect($data,"a=$a");
        }
        $this->assign('backurl',U('Index/index'));
        $this->assign('title','商标查询');
        $this->assign('right','<span id="submit-button">查询</span>');
        $this->assign('placeholder','例如,知小兵');
            $keyword = urldecode(I('get.keyword'));

            $this->assign('keyword',$keyword);
            $hash = unlock_url(I('get.hash'));
        
            if ( $keyword && $hash=="guanwangrenzheng") {
                
                 $map=array();
                    $map['Name'] = array('LIKE','%'.$keyword.'%');
                    // dump($map);
                    $count =M('trademark')->where($map)->count();
                    // echo $count;
                    $Page  = new Page($count,10);
                    $show = $Page->show();
                    // $result = M('trademark')->select();
                    $result =M('trademark')->where($map)->limit($Page->firstRow.','.$Page->listRows)->select();
                    // dump(M('trademark')->getLastSql());
                    foreach ($result as $key => $value) {
                        $array=explode('@', $value['ImageUrl']);
                        // dump( $array);
                        $result[$key]['ImageUrl']=$array[0];
                    }
                    // $result = $this->getTrademarkList($keyword,I('get.p') ? I('get.p') : 1);
                    // $count = $result['Paging']['TotalRecords'];
                    // $page = new Page($count,10);
                    $this->assign('page',$Page->show());
                    $this->assign('result',$result);
                    // dump($result);
 
             }
               $this->article();

        if(is_mobile()){
            $this->assign('shangbiao',$result);
            $this->assign('shangbiao1','shangbiao');
            $this->display('msearch');
        }else{
            $this->display();
        }

    }
    //频繁操作验证
    public function back()
    {   
       header("Content-type:text/html;charset=utf-8");
       $a = $_SERVER["REQUEST_URI"];
       $array =explode('/',$a);
       // dump($array);
       $array2 = explode('.',$array[5]);
       $a = $array2[0];
       $this->assign('a',$a);
       $this->assign('title','操作验证');

       // dump($a);
        if (IS_POST) {
            // 螺丝猫验证
            $luotest_response = I("post.luotest_response");
            $luotest_check = captchaVerified($luotest_response);

            if ($luotest_check['code'] == 0) {              
                $this->redirect("Search/$a");    
                 // $this->redirect('');
            }else{

                $this->display();
            }
        }else{

            $this->display();
        }
    }

    public function ajaxHighNewList(){
        header("Content-type:text/html;charset=utf-8");
        $keyword = I('key');
        $type = I('type');

        $this->assign('type',I('type'));
        $this->assign('key',I('key'));
        $this->add_record($keyword,5);
        $this->assign('history',$this->unset_keyword(M('SearchRecord')->order(array('create_time'=>'desc'))->where(array('uid'=>session('user.user_id'),'type'=>5))->limit(10)->select()));


        $this->assign('type',$type);
        $model = M('HighAndNew');
        $mag = array();
        if($keyword){
            $map['name'] = array('like','%'.$keyword.'%');
        }
        if($type){
            $map['type'] = $type;
        }
        $count = $model->where($map)->count();
        $Page  = new AjaxPage($count,10);

        $show = $Page->show();
        $data = $model->where($map)->order($order_str)->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($data as $key => $value){
            if($company = M('Company')->where(array('Name'=>$value['name']))->find()){
                $data[$key]['company_id'] = $company['id'];
                $model->save(array('id'=>$value['id'],'company_id'=>$company['id']));
            }else{

                $url = 'http://i.yjapi.com/ECISimple/Search?key='.$this->ApiKey.'&keyword='.$value['name'];
                $res =  json_decode(file_get_contents($url),true);
                if($res['Status'] == 200){
                    foreach($res['Result'] as $k => $v){
                        if($k['Name'] == $value['name']){

                            if(!($com = $this->companyModel->where($map)->find())){
                                $v['StartDate'] = strtotime($v['StartDate']);
                                $v['add_time'] = time();
                                $v['update_time'] = time();
                                $id = $this->companyModel->add($v);
                                $v['id'] = $id;
                            }else{
                                $v['id'] = $com['id'];
                            }
                            $data[$key]['company_id'] = $v['id'];
                            $model->save(array('id'=>$value['id'],'company_id'=>$v['id']));
                            continue;
                        }

                    }
                }
            }
        }
        $this->assign('result',$data);
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }
    //高新
    public function gaoxin(){


        $data = cc_check('Search/back');
        $a = ACTION_NAME;
        if ($data) {
           $this->redirect($data,"a=$a");
        }

        if (IS_GET) {
            $keyword = urldecode(I('get.keyword'));
            $hash = unlock_url(I('get.hash'));
            // dump(I('get.token'));
            $type = I('get.type');
            if ($keyword && $hash=="guanwangrenzheng") {
               
            
            $this->assign('backurl',U('Index/index'));
            $this->assign('title','高新查询');
            $this->assign('right','<span id="submit-button">查询</span>');
            $this->assign('placeholder','例如,知小兵');
            $this->assign('type',$type);
            $this->assign('keyword',$keyword);
            $this->article();
            
            // $this->add_record($keyword,5);
            //$this->assign('history',$this->unset_keyword(M('SearchRecord')->order(array('create_time'=>'desc'))->where(array('uid'=>session('user.user_id'),'type'=>5))->limit(10)->select()));
            
            // if($keyword == ''){
            //     $this->error('请输入关键字');
            // }

               
            
            $model = M('HighAndNew');
            $map = array();
            
            $map['name'] = array('like','%'.$keyword.'%');
            
            if($type){
                $map['type'] = $type;
            }else{
                  $map['type']='1';
            }
         
            $count = $model->where($map)->count();
            $Page  = new \Think\Page($count,10);
            $show = $Page->show();


            $data = $model->where($map)->limit($Page->firstRow.','.$Page->listRows)->select();
            // dump( $data );
            foreach($data as $key => $value){
                if($company = M('Company')->where(array('Name'=>$value['name']))->find()) {
                    $data[$key]['company_id'] = $company['id'];
                    $model->save(array('id' => $value['id'], 'company_id' => $company['id']));
                }
            }
            
            $this->assign('result',$data);
            $this->assign('page',$show);
            // dump($data);
            
            }// 赋值分页输出
            // $this->assign('history',$this->unset_keyword(M('SearchRecord')->order(array('create_time'=>'desc'))->where(array('uid'=>session('user.user_id'),'type'=>5))->limit(10)->select()));
        }
        if(is_mobile()){
            $this->assign('gaoxin1','gaoxin');
            $this->assign('gaoxin',$data);

            $this->display('msearch');
//            $this->mindex();
        }else{
            $this->display();
        }

        // dump($data);
    }

    //专利
    public function patent(){

        $data = cc_check('Search/back');
        $a = ACTION_NAME;
        if ($data) {
           $this->redirect($data,"a=$a");
        }
        // $this->assign('history', $this->unset_keyword(M('SearchRecord')->order(array('create_time' => 'desc'))->where(array('uid' => session('user.user_id'), 'type' => 2))->limit(10)->select()));
        $this->assign('backurl', U('Index/index'));
        $this->assign('title', '专利查询');
        $this->assign('right', '<span id="submit-button">查询</span>');
        $this->assign('placeholder', '例如,知小兵');

        $keyword = urldecode(I('get.keyword'));
        $type = I('get.type') ? I('get.type') : 1;
        $p = I('get.p') ? I('get.p') : 1;
        // $this->assign('type',$type);
        $this->assign('keyword',$keyword);
        $hash = unlock_url(I('get.hash'));
        if(!empty($keyword) && $hash=="guanwangrenzheng"){
  
            $map['application_number | public_account | title| inventor |applicant'] = array('like','%'.$keyword.'%');
            $count= M('patent2')->where($map)->count();
            $page = new Page($count,7);
            $result = M('patent2')->where($map)->limit($page->firstRow.','.$page->listRows)->select();
            foreach($result as $key=>$value){
                $result[$key]['applicant'] = str_replace($keyword,'<font style="color:red;">'.$keyword.'</font>',$value['applicant']);
            }
            $this->assign('page',$page->show());
            $this->assign('result',$result);
          
        }

        // dump($result);
        $this->article();
        if(is_mobile()){
            $this->assign('patent1','patent');
            $this->assign('patent',$result);
            $this->display('msearch');
//            $this->mindex();
        }else{
            $this->display();
        }




    }

//某个公司知识产权列表
    public function mzhishiList($value = '')
    {
//        if(is_mobile()) {
        $company_id = I('get.id');
        $company = M('company')->where('id='.$company_id)->find();
        $type=I('get.type');
        if($type=='1'){
            $title = "商标列表";
            $count= M('trademark')->where(array('company_id'=>$company_id))->count();
            $page = new Page($count,10);
            $show = $page->show();

            $shangbiao =M('trademark')->where(array('company_id'=>$company_id))->limit($page->firstRow.','.$page->listRows)->select();
//            dump($shangbiao);
            foreach($shangbiao as $key=>$value){
                $data = explode('@',$value['ImageUrl']);
                $shangbiao[$key]['ImageUrl'] =  $data[0];
            }
            $this->assign('shangbiao',$shangbiao);


        }
        if ($type=='2'){
            $title = "专利列表";
            $count= M('patent2')->where("applicant='{$company['Name']}'")->count();
            $page = new Page($count,10);
            $show = $page->show();
            $patent =M('patent2')->where("applicant='{$company['Name']}'")->limit($page->firstRow.','.$page->listRows)->select();
//            dump(  $patent =M('patent2')->limit(1)->find());
            $this->assign('patent',$patent);
        }
        if($type=='3'){
            $title = "软著列表";
            $count= M('copyright')->where(array('company_id'=>$company_id))->count();
            $page = new Page($count,10);
            $show = $page->show();
            $ruanzhu =M('copyright')->where(array('company_id'=>$company['id']))->limit($page->firstRow.','.$page->listRows)->select();
            $this->assign('ruanzhu',$ruanzhu);
        }
        $this->assign('page',$show);
        $this->assign('company',$company);
        $this->assign('title',$title);
        $this->display();
//      }
    }



    //专利列表
    public function searchPatent2(){
        $this->assign('history', $this->unset_keyword(M('SearchRecord')->order(array('create_time' => 'desc'))->where(array('uid' => session('user.user_id'), 'type' => 2))->limit(10)->select()));
        $this->assign('backurl', U('Index/index'));
        $this->assign('title', '专利查询');
        $this->assign('right', '<span id="submit-button">查询</span>');
        $this->assign('placeholder', '例如,知小兵');

        $keyword = urldecode(I('get.key'));
        $type = I('get.type') ? I('get.type') : 1;
        $p = I('get.p') ? I('get.p') : 1;
        $this->assign('type',$type);
        $this->assign('keyword',$keyword);

        if(!empty($keyword)){
            $url = 'http://www.cnipsun.com/patent/search.do';
            $data = [

                'ascOrder'=>'',
                'cnSources'=>'fmzl_ft, syxx_ft, wgzl_ab, fmsq_ft',
                'field1'=>0,
                'field1Val'=>'',
                'field2'=>0,
                'field2Val'=>'',
                'field3'=>0,
                'field3Val'=>'',
                'pageNo'=>$p,
                'pageSize'=>10,
                'resultTypeSelect'=>1,
                'searchExpression'=>$keyword,
                //'searchExpressionDesc'=>"公开（公告）日 = '1985.1'",
                'sortMethod'=>'',
                'woSources'=>'twpatent, hkpatent'
            ];

            $result = json_decode(httpPost($url,$data),true);
            //var_dump($result);
            if($result['total'] > 0){
                foreach($result['pageResult']['records'] as $key=>$value){
                    $checksql = "SELECT * FROM `tp_patent3` WHERE application_number='" .$value['申请号']. "' LIMIT 1";
                    if($checkpatent = M('')->query($checksql)){
                        if($value['公开（公告）日'] > $checksql['open_day']){
                            $sql = "UPDATE `tp_patent3` " .
                             "SET public_account=" . "'" . $value['公开（公告）号'] . "'," .
                             "open_day=" . strtotime(str_replace('.','-',$value['公开（公告）日'])) . "," .
                             "trs_db=" . "'" . $value['trs_db'] . "'," .
                             "law_status_date=" . strtotime(str_replace('.','-',$value['lawStatusDate']) . "," .
                             "law_status=" . "'" . ($value['lawStatus']) . "' " .
                             " WHERE application_number='" . $value['申请号'])."'";
                            $updatearr = [
                                'public_account'=>$value['公开（公告）号'],
                                'open_day'=>strtotime(str_replace('.','-',$value['公开（公告）日'])),
                                'trs_db'=>$value['trs_db'],
                                'law_status_date'=>strtotime(str_replace('.','-',$value['lawStatusDate'])),
                                'law_status'=>$value['lawStatus']
                            ];


                            M('Patent3')->where(['application_number'=>$value['申请号']])->save($updatearr);
                            //echo $sql;

                        }
                    }else{
                        $valuestr = "'".$value['主分类号']."',".
                            "'".$value['operationDesc']."',".
                            "'".strtotime(str_replace('.','-',$value['lawStatusDate']))."',".
                            "'".$value['trs_db']."',".
                            "'".$value['页数']."',".
                            "'".$value['lawStatus']."',".
                            "'".addslashes($value['摘要'])."',".
                            "'".strip_tags($value['申请（专利权）人'])."',".
                            "'".$value['申请号']."',".
                            "'".$value['id']."',".

                            "'".$value['isHaveAttachmentTif']."',".
                            "'".$value['公开（公告）号']."',".
                            "'".addslashes($value['名称'])."',".
                            "'".strtotime(str_replace('.','-',$value['公开（公告）日']))."',".
                            "'".strtotime(str_replace('.','-',$value['申请日']))."',".
                            "'".strtotime(str_replace('.','-',$value['地址']))."',".
                            "'".strtotime(str_replace('.','-',$value['imgsListUrl']))."',".
                            "'".strtotime(str_replace('.','-',$value['potenceDesc']))."',".
                            "'".$value['isHavePdf']."',".
                            "'".strtotime(str_replace('.','-',$value['发布路径']))."'";

                        $sql = 'INSERT INTO `tp_patent3` ' .
                        '(`main_category`,`operation_desc`,`law_status_date`,`trs_db`,`page`,`law_status`,`desc`,`applicant`,`application_number`,`complex_id`,
                        `is_have_attachment_tif`,`public_account`,`title`,`open_day`,`application_date`,`address`,`imgs_list_url`,`potence_desc`,`is_have_pdf`,`publish_the_path`) '.
                        'VALUES ' .
                        '(' .$valuestr.')';
                        $temparr = [
                            'main_category'=>$value['主分类号'],
                            'operation_desc'=>$value['operationDesc'],
                            'law_status_date'=>strtotime(str_replace('.','-',$value['lawStatusDate'])),
                            'trs_db'=>$value['trs_db'],
                            'page'=>$value['页数'],
                            'law_status'=>$value['lawStatus'],
                            'desc'=>addslashes($value['摘要']),
                            'applicant'=>strip_tags($value['申请（专利权）人']),
                            'application_number'=>$value['申请号'],
                            'complex_id'=>$value['id'],

                            'is_have_attachment_tif'=>$value['isHaveAttachmentTif'],
                            'public_account'=>$value['公开（公告）号'],
                            'title'=>addslashes($value['名称']),
                            'open_day'=>strtotime(str_replace('.','-',$value['公开（公告）日'])),
                            'application_date'=>strtotime(str_replace('.','-',$value['申请日'])),
                            'address'=>strtotime(str_replace('.','-',$value['地址'])),
                            'imgs_list_url'=>strtotime(str_replace('.','-',$value['imgsListUrl'])),
                            'potence_desc'=>strtotime(str_replace('.','-',$value['potenceDesc'])),
                            'is_have_pdf'=>$value['isHavePdf'] ? 1 : 0,
                            'publish_the_path'=>strtotime(str_replace('.','-',$value['发布路径'])),

                        ];
                        M('Patent3')->add($temparr);

                        //echo $sql;
                        //M('')->query($sql);
                    }
                }
            }


            $this->assign('result',$result['pageResult']['records']);
            $page = new \Think\Page($result['total'],10);
            $this->assign('page',$page->show());
            $this->assign('count',$result['total']);

        }

        $this->display();
    }

    
    public function ajaxParent(){

        $this->display();
    }


    public function patentList2(){
        $company = M('Company')->find(I('get.id'));
        $result = baitu_get_patent_by_name($company['Name']);
        var_dump($result);
        $this->assign('title','专利列表-'.$company['Name']);
        $this->assign('backurl',U('Search/companyDetails',array('id'=>$company['id'])));
        $this->assign('result',$result);
        $this->assign('company',$company);
        $this->display();

    }

    public function ajaxPatent2(){
        $company_name = I('post.company_name');
        $url2 = 'http://120.78.133.212:8000/get_patent_count/?keyword='.urlencode($company_name).'&t=2&p=1&cn=2';
        $res2 = json_decode(file_get_contents($url2),true);
        M('Company')->where(array('id'=>I('get.id')))->save(
            array(
                'appearance_count'=>$res2['cubePatentSearchResponse']['total_hits'],
            )
        );
        $this->ajaxReturn($res2);
    }

    public function ajaxPatent3(){
        $company_name = I('post.company_name');
        $url2 = 'http://120.78.133.212:8000/get_patent_count/?keyword='.urlencode($company_name).'&t=2&p=1&cn=3';
        $res2 = json_decode(file_get_contents($url2),true);
        M('Company')->where(array('id'=>I('get.id')))->save(
            array(
                'practical_count'=>$res2['cubePatentSearchResponse']['total_hits'],
            )
        );
        $this->ajaxReturn($res2);
    }


    //专利详情
    public function patentDetails(){
        $this->assign('patent',M('Patent')->find(I('get.id')));
        $this->display();
    }

    //商标列表
    public function trademarkList2(){
        $company = M('Company')->find(I('get.id'));
        $result = M('Trademark')->where(array('company_id'=>$company['id']))->select();
        $this->assign('result',$result);
        $this->assign('company',$company);
        
        $this->assign('title','商标列表-'.$company['Name']);
        $this->assign('backurl',U('Search/companyDetails',array('id'=>$company['id'])));
        $this->display();
    }


    //专利详情
    public function copyrightDetails(){
        $this->assign('patent',M('Patent')->find(I('get.id')));
        $this->display();
    }



    public function focusList(){
        if(session('user_id')){
            $result = M('Focus')->where(array('user_id'=>session('user_id')))->select();
            foreach($result as $key=>$value){
                $company = M('Company')->where(array('id'=>$value['company_id']))->find();
                $result[$key]['Name'] = $company['Name'];
                $result[$key]['OperName'] = $company['OperName'];
                $result[$key]['StartDate'] = $company['StartDate'];
                if(M('ObjectFunding')->where(array('company_id'=>$value['company_id']))->find()){
                    $result[$key]['z'] = 1;
                    $result[$key]['money'] = number_format(M('ObjectFunding')->where(array('company_id'=>$value['company_id']))->sum('money'),2);
                }

            }
            $this->assign('result',$result);
            $this->assign('title','我的监控');
//            dump($result);
            if(is_mobile()){
                $this->display('mfocusList');
            }else{
                $this->display();
            }
        }else{
            $this->redirect('Login/login');
        }
    }

    public function ajaxFocusList(){
        $result = M('Focus')->where(array('user_id'=>session('user.user_id')))->select();
        foreach($result as $key=>$value){
            $company = M('Company')->where(array('id'=>$value['company_id']))->find();
            $result[$key]['Name'] = $company['Name'];
        }
        $this->assign('result',$result);
        
        $this->display();
    }



    public function chanageFocus(){
        $focus_status = I('post.focus_status');
//        dump( $focus_status);
        $company_id = I('post.company_id');
        $data = array('company_id'=>$company_id,'user_id'=>session('user_id'));
        if($focus_status == 1){
            M('Focus')->add($data);
            $arr = array('status'=>1,'msg'=>'监控成功','focus_status'=>'focus-on','text'=>'监控中','sql'=>M('Focus')->getLastSql());
        }else{
            M('Focus')->where($data)->delete();
            $arr = array('status'=>1,'msg'=>'取消监控成功','focus_status'=>'focus-off','text'=>'未监控');
        }

        $this->ajaxReturn($arr);
    }

    public function praise(){
        if(IS_POST){
            $company_id = I('post.cid');
            $where = array('cid'=>$company_id,'uid'=>session('user.user_id'));
            if(M('CompanyPraise')->where($where)->find()){
                $arr = array('status'=>0,'msg'=>'您已经点过赞了');
            }else{
                $where['create_time'] = time();
                M('CompanyPraise')->add($where);
                $arr = array('status'=>1,'msg'=>'感谢您的支持');
            }
            $this->ajaxReturn($arr);
        }



    }
    public function companyPosition(){
        $this->assign('rongyu',M('ConditionList')->where(array('cid'=>9))->select());
        $company = M('Company')->find(I('get.id'));


        if(!$company){
            $this->error('没有找到相关的企业');
        }

        $url = 'http://apis.map.qq.com/ws/geocoder/v1/?address='.$company['Address'].'&key='.$this->qqMapKey;
        $str = file_get_contents($url);
        $object = json_decode($str,true);
        //var_dump($object);
        $this->assign('lng',$object['result']['location']['lng']);
        $this->assign('lat',$object['result']['location']['lat']);
        $this->assign('object',$object);
        $this->assign('company',$company);
        $this->display();
    }
    //获取两点之间的距离
    public function distance(){
        $lng = I('lng');
        $lat = I('lat');
        $address = I('address');

        $url = 'http://apis.map.qq.com/ws/geocoder/v1/?address='.$address.'&key='.$this->qqMapKey;
        $str = file_get_contents($url);
        $object = json_decode($str,true);
        $url = 'http://apis.map.qq.com/ws/distance/v1/?mode=driving&from='.$lat.','.$lng.'&to='.$object['result']['location']['lat'].','.$object['result']['location']['lng'].'&key='.$this->qqMapKey;
        echo $url;
    }


    //提交项目订单
    public function subObjectOrder(){
        if(IS_POST){
            $data = I('post.');
            $data['user_id'] = session('user.user_id');
            $data['create_time'] = time();
            if(M('ObjectOrder')->add($data)){
                $this->ajaxReturn(array('status'=>1,'msg'=>'提交成功,我们会尽快与您取得联系!'));
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'提交失败,请稍后再试!'));
            }
        }
    }

     //查询守重
    public function shouzhong(){

        $data = cc_check('Search/back');
        $a = ACTION_NAME;
        if ($data) {
           $this->redirect($data,"a=$a");
        }
        //$this->assign('history',$this->unset_keyword(M('SearchRecord')->order(array('create_time'=>'desc'))->where(array('uid'=>session('user.user_id'),'type'=>6))->limit(10)->select()));
        $this->assign('backurl',U('Index/index'));
        $this->assign('title','守重查询');
        $this->assign('right','<span id="submit-button">查询</span>');
        $this->assign('placeholder','例如,知小兵');
        
        
        // $this->assign('history',$this->unset_keyword(M('SearchRecord')->order(array('create_time'=>'desc'))->where(array('uid'=>session('user.user_id'),'type'=>6))->limit(10)->select()));
        
        
        $keyword = urldecode(I('get.keyword'));
        $hash= unlock_url(I('get.hash'));
        $this->assign('keyword',$keyword);
        $model = M('Sz');
        $map = array();
          if ($keyword && $hash=="guanwangrenzheng") {
            $map['name'] = array('like','%'.$keyword.'%');
        }
        

        $count= $model->where($map)->count();
        // $url = "http://sz.gdgs.gov.cn/adminProject/getAllPublicity?page=" .(I('get.p') ?I('get.p') :1) ."&size=25&Q_state_I_IN=6&Q_sysOrganize.organizeName_S_LK=".$keyword."&_=".time().mt_rand(111,999);
        // //echo $url;
        // $data = json_decode(file_get_contents($url),true);

        //$data = $model->where($map)->order("id")->limit($Page->firstRow.','.$Page->listRows)->select();
        // $page  = new \Think\Page($data['totalRecord'],25);
        $page  = new \Think\Page($count,10);
        $show = $page->show();
        $data = $model->where($map)->limit($page->firstRow.','.$page->listRows)->select();
        // var_dump($data);
        if($data['resultList']){
            foreach($data['resultList'] as $key=>$value){
                if($company = M('')->query("select * from tp_company where Name='".$value['publicOrganizeName']."' limit 1")){
                    $data['resultList'][$key]['company_id'] = $company[0]['id'];

                }else{

                    $url = 'http://i.yjapi.com/ECISimple/Search?key='.$this->ApiKey.'&keyword='.$value['publicOrganizeName'];
                    $companyList =  json_decode(file_get_contents($url),true);
                    //var_dump($value['publicOrganizeName']);
                    if($companyList['Status'] == 200){
                        foreach($companyList['Result'] as $k => $v){
                            if(!($company = M('')->query("select * from tp_company where Name='".$v['Name']."' limit 1"))){

                                $v['StartDate'] = strtotime($v['StartDate']);
                                $v['add_time'] = time();
                                $v['update_time'] = time();
                                //var_dump($v);
                                $v['id'] = $this->companyModel->add($v);
                            }else{
                                $v['id'] = $company[0]['id'];
                            }
                            $data['resultList'][$key]['company_id'] = $v['id'];

                            continue;
                        }
                    }
                }
            }
        }


      foreach($data as $key => $value){
            if($value['company_id'] == 0){
                if($company = M('Company')->where(array('Name'=>$value['name']))->find()){
                    $data[$key]['company_id'] = $company['id'];
                    $model->save(array('id'=>$value['id'],'company_id'=>$company['id']));
                }else{
                    $url = 'http://i.yjapi.com/ECISimple/Search?key='.$this->ApiKey.'&keyword='.$value['name'];
                    $companyList =  json_decode(file_get_contents($url),true);
                    if($companyList['Status'] == 200){
                        foreach($companyList['Result'] as $k => $v){
                            $map = array('Name'=>$value['name']);
                            if(!($company = $this->companyModel->where($map)->find())){
                                $v['StartDate'] = strtotime($v['StartDate']);
                                $v['add_time'] = time();
                                $v['update_time'] = time();
                                $id = $this->companyModel->add($v);
                                $v['id'] = $id;
                            }else{
                                $v['id'] = $company['id'];
                            }
                            $data[$key]['company_id'] = $v['id'];
                            $model->save(array('id'=>$value['id'],'company_id'=>$v['id']));
                            continue;
                        }
                    }
                }
            }
        }
      foreach($data['resultList'] as $key=>$value){
            if(M('ObjectFunding')->where(array('company_id'=>$value['company_id']))->find()){
                $result[$key]['z'] = 1;
                $result[$key]['money'] = number_format(M('ObjectFunding')->where(array('company_id'=>$value['company_id']))->sum('money'),2);
            }

        }
        $this->article();
        // dump($data);
        $this->assign('result',$data);
        $this->assign('page',$show);// 赋值分页输出
        // dump($data);
        if(is_mobile()){
            $this->assign('shouzhong',$result);
            $this->assign('shouzhong1','shouzhong');
            $this->display('msearch');
//            $this->mindex();
        }else{
            $this->display();
        }



    }

    public function ajaxSzList(){
        header("Content-type:text/html;charset=utf-8");
        $keyword = I('keyword');
        $this->assign('keyword',I('keyword'));
        $this->add_record($keyword,6);
        $this->assign('history',$this->unset_keyword(M('SearchRecord')->order(array('create_time'=>'desc'))->where(array('uid'=>session('user.user_id'),'type'=>6))->limit(10)->select()));

        $model = M('Sz');
        $mag = array();
        if($keyword){
            $map['name'] = array('like','%'.$keyword.'%');
        }

        $count = $model->where($map)->count();
        $Page  = new AjaxPage($count,10);

        $show = $Page->show();
        $data = $model->where($map)->order("id")->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($data as $key => $value){
            if($value['company_id'] == 0){
                if($company = M('Company')->where(array('Name'=>$value['name']))->find()){
                    $data[$key]['company_id'] = $company['id'];
                    $model->save(array('id'=>$value['id'],'company_id'=>$company['id']));
                }else{
                    $url = 'http://i.yjapi.com/ECISimple/Search?key='.$this->ApiKey.'&keyword='.$value['name'];
                    $companyList =  json_decode(file_get_contents($url),true);
                    if($companyList['Status'] == 200){
                        foreach($companyList['Result'] as $k => $v){
                            $map = array('Name'=>$value['name']);
                            if(!($company = $this->companyModel->where($map)->find())){
                                $v['StartDate'] = strtotime($v['StartDate']);
                                $v['add_time'] = time();
                                $v['update_time'] = time();
                                $id = $this->companyModel->add($v);
                                $v['id'] = $id;
                            }else{
                                $v['id'] = $company['id'];
                            }
                            $data[$key]['company_id'] = $v['id'];
                            $model->save(array('id'=>$value['id'],'company_id'=>$v['id']));
                            continue;
                        }
                    }
                }
            }
        }


        $this->assign('result',$data);
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }
    //ajax获取同行对比
    public function ajaxGetContrast(){
        if(IS_POST){
            $company_id = I('post.company_id');
            $flag = I('post.flag');
            $company = M('Company')->find($company_id);

            //解剖企业的经营范围

            //$tags = $this->scws($company['CompanyDetails']['Scope']);

            //$tags = explode(array('、','；'),$company['CompanyDetails']['Scope']);
            $tags2 = preg_split( "/(、|,|；|，|。)/", $company['CompanyDetails']['Scope'] );
            $strArr = array('项目除外','行政法规','法律','限制的项目须取得许可后方可经营）^','不得从事公开募集基金管理业务）','国务院决定规定需前置审批和禁止的项目）^','国内贸易');
            $map2 = '';
            foreach($tags2 as $key => $value){
                foreach($strArr as $k => $v){
                    if(strpos($value,$v) !== false || $value == ''){
                        unset($tags2[$key]);
                    }
                }
            }
            if($tags2){
                foreach($tags2 as $value){
                    $map2 .= "cd.Scope like '%".$value."%' or ";
                }
            }
            $map2 = substr($map2,0,-3);
            $tags = str_replace(
                array('深圳市','深圳','有限','集团','实业','管理','股份','公司','咨询','服务','科技'),
                array('',    '',     '',    '',    '',    '',    '',    '',    '',   '',    ''),
                $company['Name']
            );

            if(!!$resulttags = $this->scws($tags)){
                $tags = $resulttags;
            }
//          echo "<div style='display:none'>";
//          var_dump($tags);
//          echo "</div>";
            $map = '';
            if($tags){
                foreach($tags as $value){
                    $map .= "c.Name like '%".$value."%' or ";
                }
            }
            $map = substr($map,0,-3);
            if($flag){
                $order =" order by rand() ";
            }else{
                $order ="";
            }
            if($map){
                $sql = "select c.*,cd.* from tp_company c inner join tp_company_details cd on c.id=cd.company_id where (".$map.")  and c.zizhu=1 and c.Name like '%深圳%' ".$order." limit 20";
            }else{
                $sql = "select c.*,cd.* from tp_company c inner join tp_company_details cd on c.id=cd.company_id where c.zizhu=1 and c.Name like '%深圳%' ".$order." limit 20";
            }
            if($map2){
                $sql2= "select c.*,cd.* from tp_company c inner join tp_company_details cd on c.id=cd.company_id where (".$map2.") and c.zizhu=1 and c.Name like '%深圳%' ".$order." limit 20";
            }else{
                $sql2= "select c.*,cd.* from tp_company c inner join tp_company_details cd on c.id=cd.company_id where  c.zizhu=1 and c.Name like '%深圳%' ".$order." limit 20";
            }


            $res = M()->query($sql);
            $res2 = M()->query($sql2);




            $temp = array();
            if($res){
                foreach($res as $a=>$b){
                    if($b['Name'] != $company['Name']){
                        $temp[] = $b['id'];
                    }else{
                        unset($res[$a]);
                    }
                }
            }
            if($res2){
                foreach($res2 as $a=>$b){
                    if($b['Name'] != $company['Name']){
                        $temp2[] = $b['id'];
                    }else{
                        unset($res2[$a]);
                    }

                }
            }


            $result = array_intersect($temp,$temp2);

            $map3 = '';
            if(count($res) > 0 && count($res2) >0 && count($result) >0 ){
                foreach ($result as $value){
                    $map3 .= "'".$value."'".",";
                }
                $map3 = substr($map3,0,-1);
                $sql3= "select c.*,cd.* from tp_company c inner join tp_company_details cd on c.id=cd.company_id where c.id in(".$map3.") and c.zizhu=1 and c.Name like '%深圳%' ".$order." limit 10";

                $res = M()->query($sql3);
            }else if(count($res2) > 0 && empty($res)){
                $res = $res2;
            }


            if($res){
                foreach($res as $a=>$b){
                    //if(strpos($b['Name'],'深圳') !== false && $b['Name'] != $company['Name']){
                        $object2 = M('ObjectFunding')->order('money desc')->where(array('company_id'=>$b['id']))->select();
                        $money2 = 0;
                        $sortarr = array();

                        foreach($object2 as $key => $value){
                            $object2[$key]['money2'] = intval(floatval($value['money']) * 100);
                            $o = M('Object')->find($value['oid']);
                            $money2 += (float)$value['money'];
                            $object2[$key]['object'] = $o['title'];
                        }
                        foreach($object2 as $key => $value){
                            foreach($value as $k=>$v){
                                $sortarr[$k][$key] = $v;
                            }
                        }


                        array_multisort($sortarr['money2'],SORT_DESC, $object2);
                        $res[$a]['zizhu'] = $object2;
                        $res[$a]['money2'] = $money2;
                    //}else{
                       // unset($res[$a]);
                   // }
                }

                $res = array_slice($res,0,3);
//              echo "<div style='display:none'>";
//              var_dump($res);
//
//              $sortarr = array();
//              foreach($res as $key => $value){
//                  $res[$key]['money3'] = intval(floatval($value['money2']) * 100);
//
//              }
//              foreach($res as $key => $value){
//                  foreach($value as $k=>$v){
//                      $sortarr[$k][$key] = $v;
//                  }
//              }
//
//              array_multisort($sortarr['money3'],SORT_DESC, $res);
//
//
//              echo "</div>";
                $this->assign('recommended',$res);
            }

        }
        $this->display();
    }


    public function ajaxGetContrastByProduct(){
        if(IS_POST){
            $company_id = I('post.company_id');
            $keyword = I('post.keyword');

            $where['Scope'] = array('like','%'.$keyword.'%');
            $where['id'] = array('neq',$company_id);
            $where['zizhu'] = 1;
            $res = M('Company')->where($where)->limit(10)->select();
            if($res){
                foreach($res as $a=>$b){
                    $object2 = M('ObjectFunding')->order('money desc')->where(array('company_id'=>$b['id']))->select();
                    $money2 = 0;
                    $sortarr = array();

                    foreach($object2 as $key => $value){
                        $object2[$key]['money2'] = intval(floatval($value['money']) * 100);
                        $o = M('Object')->find($value['oid']);
                        $money2 += (float)$value['money'];
                        $object2[$key]['object'] = $o['title'];
                    }
                    foreach($object2 as $key => $value){
                        foreach($value as $k=>$v){
                            $sortarr[$k][$key] = $v;
                        }
                    }

                    array_multisort($sortarr['money2'],SORT_DESC, $object2);
                    $res[$a]['zizhu'] = $object2;
                    $res[$a]['money2'] = $money2;

                }

                usort($res,function($a,$b){
                    if ($a['money2']==$b['money2']) return 0;
                    return ($a['money2']>$b['money2'])?-1:1;
                });
                $res = array_slice($res,0,3);
                $this->assign('recommended',$res);
//                $this->ajaxreturn ($res);
            }

        }

        $this->display();
    }

    // 查询公司 
    public function searchCompany(){
        header("Content-Type:text/html;charset=utf-8");
        // header("Access-Control-Allow-Origin: *");
        if(IS_POST){
            //$cookie_company = cookie('company');
            // if($keyword = I('post.keyword')){
            //     // $this->add_record($keyword,7);
            // }
            $keyword = I('post.keyword');
            $map['Name'] = array('like','%'.$keyword.'%');
            $result = M('Company')->where($map)->limit(10)->select();
            $this->assign('result',$result);
            $this->assign('keyword',$keyword);
            $this->display();
        }

    }

    //资助企业列表
    public function fundingCompany(){
        $this->assign('title','优质企业查询');
        $this->article();
        if(is_mobile()){
            $this->display("mfundingCompany");
        }else{
            $this->display();

        }

    }

//    //ajax获取项目列表
    public function ajaxCompanyList(){
        if(IS_POST){
            $model = M('company');
            $map = array();
            $data = I('post.');
            if($data['key']){ //关键字
                $map['Name']=array('like','%'.$data['key'].'%');
            }
//            金额
            if($data['money']!=''){
                if ($data['money']=='0'){
                    $map['total_money']='0';
                }elseif($data['money']=='10000+'){
                    $map['total_money'] = array('EGT',10000);
                }else{
                    $data2=explode('-',$data['money']);
                    $data2[0]=intval($data2[0]);
                    $data2[1]=intval($data2[1]);
                    $map['total_money'] = array('BETWEEN',$data2);
                }
            }
//            区域
            if ($data['zone']){
                $map['address | address2']=array('like','%'.$data['zone'].'%');
            }
//            年份
            if($data['year']){
                if ($data['year']=='10+'){
                    $data3 = time()-intval($data['year'])*365*24*60*60;
                    $map['StartDate']=array('ELT', $data3);
                }else{
                    $data3=explode('-',$data['year']);
                    $data4[0]=time()- intval($data3[1])*365*24*60*60;
                    $data4[1]=time()- intval($data3[0])*365*24*60*60;
                    $map['StartDate'] = array('BETWEEN',$data4);
                }
            }
//            高新
            if ($data['gg']) {
                $map['gaoxin'] =$data['gg'];
            }
//            新三板
            if($data['xinsanban']){
                $map['xinsanban'] = $data['xinsanban'];
            }
//            上市
            if($data['shangshi']){
                $map['shangshi'] = $data['shangshi'];
            }
//            行业
            if($data['industry']){
                $map['industry|Scope'] = array('LIKE','%'.$data['industry'].'%');
            }
            $map['zizhu']='1';
            $count = $model->where($map)->limit(100)->count();
            if( $count>=100){
                $count = 100;
            }else{
                $count = $model->where($map)->limit(100)->count();
            }
            $Page  = new AjaxPage($count,10);
            if($Page->totalRows>=100){
                $Page->totalRows="100+";
            }
            $show = $Page->show();
            if($_GET['p']<=100){
                $result = $model->where($map)->page($_GET['p'],10)->order("total_money desc")->select();
                foreach($result as $key=>$value){
                    $result[$key]['Name'] = str_cut($result[$key]['Name'],18);
                    $result[$key]['Name'] = str_replace($keyword, '<span style="color:#1baf8d;font-size:16px;">'.$keyword.'</span>', $result[$key]['Name']);
                    if(M('ObjectFunding')->where(array('company_id'=>$value['id']))->find()){
//                        $result[$key]['z'] = 1;
                        $money = 0;
                        $object = M('ObjectFunding')->where(array('company_id'=>$value['id']))->select();
                        foreach($object as $k=>$v){
                            $money += str_replace(',','',$v['money']);
                        }
                        $result[$key]['money'] = number_format($money,2);
                        if(empty($result[$key]['total_money']) && $result[$key]['money']!='0.00' ){
                            $sql = "update tp_company set total_money=".$result[$key]['money'] ." where id=".$value['id'];
                            $res = M('company')->execute($sql);
                        }
                    }
                }
            }
            $this->assign('result',$result);
            $this->assign('page',$show);// 赋值分页输出
            if(is_mobile()){
                $this->display("majaxCompanyList");
            }else{
                $this->display();
            }
        }


    }

   public function article($value='')
    {
        $article = M('Article')->limit(4)->order('add_time desc')->select();
        $mapa =array();
        $mapa['Name']=array('LIKE','%深圳%');
        $zizhu= M('Company')->where($mapa)->order('add_time desc')->limit(4)->select();
        $this->assign('article',$article);
        $this->assign('zizhu',$zizhu);


    }

    public function test($value='')
    {
        header("Content-Type:text/html;charset=utf-8");
        $time= time()-1*4*5*60*60;
        dump(M('users')->where(array('mobile'=>"15218109660"))->save(array("reg_time"=>$time)));

    }




}