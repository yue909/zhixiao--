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
use Think\Page;
use Think\Verify;
use QL\QueryList;
class UpdateController extends BaseController {



		public $attachment = '';

		public $content = '';
	public $ApiKey = '9d4a807ff4154ae7af97c10d43b250f9';
	public function updateIndustry(){
		header("Content-type:text/html;charset=utf-8;");
		$id = $_GET['id'];
		if(!$id){
			$id = 1;
		}

		$companyModel = M('Company');
		$html = file_get_contents('https://www.qichacha.com/firm_5d9ea298d85b6ffa77bd25520c731c0e.shtml');
		$url = 'http://www.qichacha.com/firm_5d9ea298d85b6ffa77bd25520c731c0e.shtml';
		$data = QueryList::Query($url,
			array(
				"content" =>array("#Cominfo","html"),
			)
		)->html;
		file_put_contents('test.html',$data);



//		if($company = M('Company')->find($id)){
//				echo "<h2>正在更新".$company['Name']."</h2>";
//			$this->doUpdateIndustry($company['Name'],$id);
//
//		}


//		if($next = M('Company')->where("id > ".$id)->find() ){
//			echo "<script>location.href='http://test.zhixiaobing.com/index.php/Home/Update/updateIndustry/id/".$next['id']."'</script>";
//		}
	}
    public function test(){
        header("Content-type:text/html;charset=utf-8;");
        echo strtotime("2017-03-09");
        // $res = array(
        //     array(
        //         'Name'=>"深圳市赛为智能股份有限公司 ",
        //     ),
        //     array(
        //         'Name'=>"深圳市赛特罗生物医疗技术有限公司 ",
        //     ),
        //     array(
        //         "Name"=>"深圳市赛纳科技有限公司 "
        //     ),
        // );
        // // foreach ($res as $key => $value) {
        // //     var_dump(trim($value['Name']));
        // // }
        // //
        // $company = M('Company')->find(87294);
        // $str = mb_substr($company['Name'],-1,1,'utf-8');

        // // $search = array(" ","　","\t","\n","\r");
        // // $replace = array("","","","","");
        // $where['id'] = array('egt',87295);
        // $res = M('Company')->where($where)->select();
        // foreach ($res as $key => $value) {
        //     if(strpos($value['Name'],$str)){
        //         M('Company')->delete($value['id']);
        //     }
        // }
    }
    public function shichangjiandu(){
        set_time_limit(0);
        header("Content-type:text/html;charset=utf-8;");
        // header("Content-type:text/html;charset=utf-8;");
        // $url = 'http://www.szscjg.gov.cn/xxgk/zjxx/qtzjxx/201412/t20141217_2760315.htm';
        // $html = file_get_contents($url);
        // $objectModel = M('Object');
        // $company_model = M('Company');
        // $fundingModel = M('ObjectFunding');
        // $publicityModel = M('Publicity');
        // $data2 = QueryList::Query($html,
        //     array(
        //     "tr" => array("table tr","html"),
        //     )
        // )->data;
        // preg_match_all();
        // $table =  $data2[0]['html'];

        // foreach ($data2 as $k => $v) {
        //     $v['tr'] = str_replace(array("\r","\t","\n","　"),array("","","",""),$v['tr']);
        //     preg_match_all("/<td.*>(.+)<\/td>/U",$v['tr'],$arr);
        //     $temp = array();
        //     $temp['key'] =  strip_tags(trim($arr[1][0]));
        //     $companyname = strip_tags(trim($arr[1][1]));
        //     $temp['money'] = str_replace("　","",trim(strip_tags($arr[1][2]))) ;
        //     $temp['batch'] = "2013-9";
        //     $temp['oid'] = 20132015;
        //     $temp['lid'] = 20132173;


        //     if($temp['money'] && is_numeric(strip_tags(trim($arr[1][0])))){
        //         $a[] = $temp;

        //         //var_dump($temp);
        //         // if(!$companydata = $company_model->where(array('Name'=>$companyname))->find()){
        //         //     $companydata['id'] = $company_model->add(array('Name'=>$companyname,'zizhu'=>1,'add_time'=>time()));
        //         //     $this->updateCompany($companydata['id'],$companyname);
        //         // }
        //         // $temp['company_id'] =  $companydata['id'];
        //         // if(!$fundingModel->where($temp)->find()){
        //         //     $fundingModel->add($temp);
        //         //     $count++;
        //         // }
        //     }
        // }
        // var_dump($a);
        // exit();


        $url = 'http://www.szscjg.gov.cn/xxgk/zjxx/qtzjxx/index.htm';
        $html =file_get_contents($url);
        $data2 = QueryList::Query($html,
            array(
            "title" => array(".publicList ul li.pclist a","title"),
            "href" => array(".publicList ul li.pclist a","href"),
            "add_time" => array(".publicList ul li.pclist a span","text","",function($time){
                return strtotime($time);
            }),
            )
        )->data;
        //http://www.szscjg.gov.cn/xxgk/zjxx/qtzjxx
        $objectModel = M('Object');
        $company_model = M('Company');
        $fundingModel = M('ObjectFunding');
        $publicityModel = M('Publicity');
        $now = M('ObjectAutoMonitoring')->find(1);
        // var_dump($now);
        // var_dump($data2[0]);
        // var_dump(1488988800=="1488988800");
        foreach($data2 as $key => $value){
            if(($value['add_time'] == $now['time']) && ($value['title'] == $now['title'])){
                $array_key = $key;
                break;
            }
        }
        //var_dump($array_key);
        if($array_key !== false){
            $data2 = array_slice($data2,0,$array_key);
        }

        //var_dump($data2);

        //$data2 = array_slice($data2,0,5);
        if(!empty($data2)){
            //var_dump($data2[0]);
            M('ObjectAutoMonitoring')->where(array('id'=>1))->save(array('title'=>$data2[0]['title'],'time'=>$data2[0]['add_time']));
            //exit();
            foreach($data2 as $key => $value){
                $companyCount = 0;
                $object = array();
                if( strpos($value['title'],'拨款') ) {
                    //深圳市第一批境外商标注册申请资助周转金拨款
                    preg_match("/(深圳市)第(.+)批(.+)(申请资助|资助|申请资助周转金)拨款/",$value['title'],$res);
                    //var_dump($arr);
                    if( strpos($value['title'],'计算机软件著作权登记') ){
                        $oid = 20132017;
                        $subject = '计算机软件著作权登记资助拨款';
                    }elseif( strpos($value['title'],'专利') ){
                        $oid = 20132015;
                        $subject = '专利申请资助拨款';
                    }elseif( strpos($value['title'],'境外商标注册') ){
                        $oid = 20132014;
                        $subject = '境外商标注册资助拨款';
                    }elseif( strpos($value['title'],'知识产权代理机构') ){
                        $oid = 234;
                        $subject = '知识产权代理机构资助拨款';

                    }

                    if($oid){
                        $obj = $objectModel->find($oid);
                        if(!$publicity = $publicityModel->where(array('title'=>$value['title'],'oid'=>$oid))->find()){
                            $publicity = array();
                            $publicity['title'] = $value['title'];
                            $publicity['oid'] = $oid;
                            $publicity['add_time'] = $value['add_time'];
                            $publicity['time'] = $obj['time'];
                            $publicity['department'] = $obj['department'];
                            $publicity['url'] = $obj['url'];
                            $publicity['money'] = $obj['money'];
                            $publicity['status'] = $obj['status'];
                            $publicity['level_id'] = $obj['level_id'];
                            $publicity['efforts_id'] = $obj['efforts_id'];
                            $publicity['type_id'] = $obj['type_id'];
                            $publicity['wap_id'] = $obj['wap_id'];
                            $publicity['description'] = $obj['description'];
                            $publicity['content'] = $obj['content'];
                            $publicity['id'] = $publicityModel->add($publicity);
                        }else{
                            $publicity['url'] = "http://www.szscjg.gov.cn/xxgk/zjxx/qtzjxx/".$value['href'];
                            $publicityModel->save($publicity);
                        }
                        $html = file_get_contents("http://www.szscjg.gov.cn/xxgk/zjxx/qtzjxx/".$value['href']);
                        $data2 = QueryList::Query($html,
                            array(
                            "tr" => array("table tr","html"),
                            )
                        )->data;
                        //var_dump("http://www.szscjg.gov.cn/xxgk/zjxx/qtzjxx/".$value['href']);
                        $batch = date("Y",$value['add_time']). "-" .changeNumber($res[2]);
                        $count = 0;
                        $a = array();
                        if(!$data2){
                            $data2 = QueryList::Query($html,
                                array(
                                "p" => array(".Custom_UnionStyle","html"),
                                )
                            )->data;
                            foreach ($data2 as $key => $value) {
                                preg_match_all("/<p.*>(.+)<\/p>/",$value['p'],$arr);
                                foreach ($arr[0] as $k => $v) {
                                    preg_match_all("/([\d]{1,5})\s+(.+)\s+([\d]+)/",$v,$arr2);
                                    if($arr2[2][0]){
                                        $temp = array();
                                        $companyname = my_trim($arr2[2][0]);
                                        //$temp['companyname'] = my_trim($companyname);
                                        $temp['money'] = number_format($arr2[3][0]/10000,3)."万元";
                                        $temp['batch'] = $batch;
                                        $temp['subject'] = $subject;
                                        $temp['oid'] = $oid;
                                        $temp['lid'] = $publicity['id'];



                                        if(!$companydata = $company_model->where(array('Name'=>$companyname))->find()){
                                            $companydata['id'] = $company_model->add(array('Name'=>$companyname,'zizhu'=>1,'add_time'=>time()));
                                            $this->updateCompany($companydata['id'],$companyname);
                                        }
                                        $temp['company_id'] =  $companydata['id'];
                                        //var_dump($funding);
                                        if(!$fundingModel->where($temp)->find()){
                                            $fundingModel->add($temp);
                                            $count++;
                                        }
                                    }
                                }
                            }
                        }else{

                            $table =  $data2[0]['html'];
                            foreach ($data2 as $k => $v) {
                                $v['tr'] = str_replace(array("\r","\t","\n","　"),array("","","",""),$v['tr']);
                                preg_match_all("/<td.*>(.+)<\/td>/U",$v['tr'],$arr);
                                //var_dump($arr);
                                if(is_numeric(my_trim(strip_tags($arr[1][0])))){
                                    $temp = array();
                                    $companyname = my_trim(strip_tags($arr[1][1]));

                                    //$temp['companyname'] =  my_trim($companyname);
                                    //preg_match_all("/\s/",$temp['companyname'],$r);
                                    //var_dump($r);
                                    $temp['money'] = number_format(strip_tags(trim($arr[1][2]))/10000,3)."万元";
                                    $temp['batch'] = $batch;
                                    $temp['subject'] = $subject;
                                    $temp['oid'] = $oid;
                                    $temp['lid'] = $publicity['id'];
                                    //var_dump($temp);
                                    if($companyname && is_numeric(strip_tags(my_trim($arr[1][0])))){
                                        if(!$companydata = $company_model->where(array('Name'=>$companyname))->find()){
                                            $companydata['id'] = $company_model->add(array('Name'=>$companyname,'zizhu'=>1,'add_time'=>time()));
                                            $this->updateCompany($companydata['id'],$companyname);
                                        }
                                        $temp['company_id'] =  $companydata['id'];
                                        if(!$fundingModel->where($temp)->find()){
                                            $fundingModel->add($temp);
                                            $count++;
                                        }
                                    }
                                }



                            }
                        }
                    }

                }
            }



        }

    }





	public function doUpdateIndustry($name,$id){
		if($name){

			$url = 'http://m.qichacha.com/search?key='.$name;
			$html = $this->httpGet($url);
			$data2 = QueryList::Query($html,
				array(
				"title" => array(".list-item-name","text"),
				"href" => array(".a-decoration","href")
				)
			)->data;

			if(count($data2) >0){
				foreach($data2 as $key => $value){
					if($value['title'] == $name){
						$url = 'https://www.qichacha.com'.$value['href'];

						//$html = $this->httpGet($url);
						$html = file_get_contents('https://www.qichacha.com/firm_5d9ea298d85b6ffa77bd25520c731c0e.shtml');

						$data = QueryList::Query($url,
							array(
								"content" =>array("#Cominfo .m_changeList","html"),
							)
						)->data;

						$data = QueryList::Query($html,
							array(
								"content" =>array("#Cominfo .m_changeList","html"),
							)
						)->data;


						if($data[0]['content']){
							$search = array(" ","　","\n","\r","\t");
							$replace = array("","","","","");
							$content =  str_replace($search, $replace, $data[0]['content']);

							preg_match_all("/<tr>(.+)<\/tr>/U",$content,$arr);

							$details = array();
							if($arr){
								foreach($arr[1] as $k=>$v){
									preg_match_all("/<tdclass=.+>(.+)<\/td>/U",$v,$arr2);

									if($arr2){
										$first = trim($arr2[1][0]);
										if($first == '法定代表人：'){
											$details['RegistCapi'] =  $arr2[1][3];
										}elseif($first == '成立日期：'){
											$details['TermStart'] =  strtotime(trim($arr2[1][1]));
											$details['EconKind'] =  trim($arr2[1][3]);

										}elseif($first == '营业期限：'){
											$details['TeamEnd'] =  mb_substr(trim($arr2[1][1]),13);
											$details['EndDate'] =  mb_substr(trim($arr2[1][1]),13);
											$details['BelongOrg'] =  trim($arr2[1][3]);

										}elseif($first == '核准日期：'){
											$details['CheckDate'] =  strtotime(trim($arr2[1][1]));

										}elseif($first == '所属地区'){
											$details['Province'] =  trim($arr2[1][1]);
											M('Company')->where(array('id'=>$id))->save(array('id'=>$id,'industry'=>trim($arr2[1][3]),'qichacha_url'=>$value['href']));
										}elseif($first == '经营范围：'){
											$details['Scope'] =  trim($arr2[1][1]);

										}elseif($first == '企业地址：'){
											preg_match_all("/(.+)<adata-toggle(.+)a>/",trim($arr2[1][1]),$address);

											$details['Address'] =  $address[1][0];

										}
									}
								}
								if(M('CompanyDetails')->where(array('company_id'=>$id))->find()){
									M('CompanyDetails')->where(array('company_id'=>$id))->save($details);
								}else{
									$details['company_id'] = $id;
									M('CompanyDetails')->add($details);
								}
							}

						}




//						$data = QueryList::Query($html,
//							array(
//							"name" => 		array(".company-top-name","text"),
//							"hangye" => 	array("#Cominfo .m_changeList tr:eq(6) td:last","text"),
//							"BelongOrg"=>	array("#Cominfo .m_changeList tr:eq(4) td:last","text"),
//							"Province"=>	array("#Cominfo .m_changeList tr:eq(6) td:eq(1)","text"),
//							"RegistCapi"=>	array("#Cominfo .m_changeList tr:eq(2) td:eq(3)","text"),
//							"EconKind"=>	array("#Cominfo .m_changeList tr:eq(3) td:eq(3)","text"),
//							"Address"=>		array("#Cominfo .m_changeList tr:eq(7) td:eq(1)","text"),
//							"Scope"=>		array("#Cominfo .m_changeList tr:eq(-1) td:eq(1)","text"),
//							"TermStart"=>	array("#Cominfo .m_changeList tr:eq(3) td:eq(1)","text"),
//							"TeamEnd"=>		array("#Cominfo .m_changeList tr:eq(4) td:eq(1)","text"),
//							"EndDate"=>		array("#Cominfo .m_changeList tr:eq(4) td:eq(1)","text"),
//							"CheckDate"=>	array("#Cominfo .m_changeList tr:eq(5) td:eq(1)","text"),
//							"RegistCapi"=>	array("#Cominfo .m_changeList tr:eq(6) td:eq(1)","text"),
//							)
//						)->data;
//						var_dump($data);
//						if($data){
//							if($data[0]['hangye']){
//								M('Company')->where(array('id'=>$id))->save(array('id'=>$id,'industry'=>$data[0]['hangye'],'qichach_url'=>$value['href']));
//							}
//						}
					}
				}
			}
		}
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







	//更新项目
	public function addObject(){
        set_time_limit(0);
        header("Content-Type: text/html;charset=utf-8");
		$url  = "http://www.shenkexin.com/project/index.html?req=t1-0,";
		$t1 = array(11,12);
		$t2 = array(21,22,23,24);
		$t3 = array(31,32,33,34);
		$t4 = array(401,402,403,404,405,406,407,408,409,410,499);
		$t5 = array(51,52,53);
		$count = 0;

		$model = M('Object');
        $objectNumber = 0;
		foreach($t2 as $a){
			foreach($t3 as $b){
				foreach($t4 as $c){
					foreach($t5 as $d){
						$weburl = $url.'t2-'.$a.',t3-'.$b.',t4-'.$c.',t5-'.$d;

						$data = QueryList::Query($weburl,
							array(
								"title"=>array(".list1 .row-flex .col a","text"),
								"url"=>array(".list1 .row-flex .col a","href","",function($url){
                                    return 'http://www.shenkexin.com'.$url;
                                }),
								"money"=>array(".list1 .row-flex .col1","text","",function($money){
									return $money == '-' ? '' : $money;
								}),
								"time"=>array(".list1 .row-flex .col3","text"),
								"status"=>array(".list1 .row-flex .col4 p","text","",function($status){
									return $status == '申报已结束' ? 0 : 1;
								}),
								"id"=>array(".list1 .row-flex .col a","href","",function($id){
									preg_match('/([0-9]{1,4})/',$id,$arr);
									if($arr){
										return (int)$arr[0];
									}
								}),

							)
						)->data;
						if(!empty($data)){
							$count += count($data);
							foreach($data as $key => $value){
								//var_dump($value);
								$value['level_id'] = $a;
								$value['efforts_id'] = $b;
								$value['type_id'] = $c;
								$value['wap_id'] = $d;
								//$temp[] = $value['id'];
								if(!$model->find($value['id'])){

									if($model->add($value)){
                                        $objectNumber++;
									}
								}
							}

							//echo $weburl;
						//	var_dump($data);
						}
					}
				}
			}
		}
        echo $objectNumber;

	}
	//更新项目关联企业
	function updateFunding(){
        set_time_limit(0);
        header("Content-Type: text/html;charset=utf-8");
		$model = M('Object');
		$id= $_GET['id'] ? $_GET['id'] : 360;
		$object_funding_model = M('ObjectFunding');

		$company_model = M('Company');
		$object = $model->find($id);
		//var_dump($object);

		$url = $object['url'];
		$html = file_get_contents($url);
		$data = QueryList::Query($html,
			array(
				"description" => array(".resume p","text"),
				"department"=>array(".resume li:eq(2) span","text"),
				//"attachment" => array(".article1:last .change_txt","html"),
				"li" => array(".tab1 .hd","html","",function($li){
					preg_match_all("/<li.*>(.+)<\/li>/U",$li,$arr);
					if($arr[1]){
						foreach($arr[1] as $key =>$value){
							if($value == '政策依据'){
								return 	$key;
							}
						}
					}
				}),
				"content" => array(".article1:first .change_txt","html"),
				"nitice_ids"=>array(".article1 .main .s2","html","",function($notice){
					preg_match_all("/notice-(.+?)\./",$notice,$id);
					if($id){
						$str = '';
						foreach($id[1] as $key=>$value){
							$str .= $value.',';
						}
						return substr($str,0,-1);
					}else{
						return '';
					}
				}),
				"condition_ids"=>array(".apply-info1:eq(0)","html","",function($condition_ids){
					preg_match_all("/detail-(.+?)\./",$condition_ids,$id);
					if($id){
						$str = '';
						foreach($id[1] as $key=>$value){
							$str .= $value.',';
						}
						return substr($str,0,-1);
					}else{
						return '';
					}
				}),

				"file"=>array(".article1 .main .s1","html","",function($file) use($object){
					preg_match_all("/href=[\'\"](.+?)[\'\"]/",$file,$href);
					preg_match_all("/title=[\'\"](.+?)[\'\"]/",$file,$title);
					//var_dump($href);
					//var_dump($title);
					$site_url = 'http://www.shenkexin.com';
					$file_model = M('ObjectFile');
					foreach($href[1] as $key => $value){
						$dir = explode('/',$value);
						$ext = strrchr($url,'.');
						if($dir){
							$filename = $dir[count($dir)-1];
						}else{
							$filename = time().'.'.$ext;
						}
						$save_dir = '.';
						foreach($dir as $k => $v){
							if(!empty($v) && !strpos($v,'.')){
								$save_dir .= '/'.$v;
							}
						}
						if($result = $this->getFile($site_url.$value,$save_dir,$filename)){
							if(file_exists($result['save_path']) && !$file_model->where(array('oid'=>$object['id'],'title'=>$title[1][$key],'url'=>$result['save_path']))->find()){
								$file_model->add(array('oid'=>$object['id'],'title'=>$title[1][$key],'url'=>$result['save_path']));
							}
						}
					}
				}),
				"funding"=>array(".list4","html","",function($funding) use($object){

					$object_funding_model = M('ObjectFunding');
					$company_model  = M('Company');
					$object_model  = M('Object');
					preg_match_all("/data-lid=[\'\"]([0-9]+?)[\'\"]/",$funding,$lid);
					preg_match_all("/<span>([0-9]+?)个<\/span>/",$funding,$number);
					preg_match_all("/href=[\'\"](.+?)[\'\"]/",$funding,$href);
					if($href[1]){
						foreach($href[1] as $a=>$b){
							preg_match_all("/publicity-(.+?)\./",$b,$pid);
							$pids .= $pid[1][0].',';
							$pids_arr[] = $pid[1][0];
						}
						$pids = substr($pids,0,-1);
						$object_model->where(array('id'=>$object['id']))->save(array('publicity_ids'=>$pids));
					}
					return array($number[1],$pids_arr);

				}),



			))->data;



		if($data){
			$object_funding_model = M('ObjectFunding');
			if($data[0]['li']){
				$data3 = QueryList::Query($html,
					array(
						"attachment" => array(".article1:eq(".$data[0]['li'].") .change_txt","html"),

						))->data;
				$model->where("id=".$object['id'])->save(array('attachment'=>$data3[0]['attachment']));
			}
			$count_company = 0;
			$count_funding = 0;
			//var_dump($data[0]['funding']);
			if($data[0]['funding'] && $data[0]['funding'][0] && $data[0]['funding'][1]){
				foreach($data[0]['funding'][0] as $k=>$v){
					for($i=1; $i <= (int)ceil($v/10); $i++){
						$url = 'http://www.shenkexin.com/project/detail.html?do=funcorp&lid='.$data[0]['funding'][1][$k].'&idx='.$i;
						//var_dump($url);
						$html2 = file_get_contents($url);
						$data2 = QueryList::Query($html2,
								array("content"=>array("tr","html"))
						)->data;

						foreach($data2 as $key=>$value){
							preg_match_all("/<td.*>(.*)<\/td>/",$value['content'],$arr);
							if(count($arr[1]) > 1){
								//var_dump($arr[1]);
								$count_company++;
								$company['Name'] = $arr[1][0];
                                $object_funding = array();
								//$object_funding['company_id'] = 1;
								//$object_funding['name'] = $arr[1][0];
								$object_funding['subject'] = $arr[1][1];
								$object_funding['batch'] = $arr[1][2];
								$object_funding['money'] = $arr[1][3] == '-' ? '' : $arr[1][3];
								$object_funding['lid'] = (int)$data[0]['funding'][1][$k];
								$object_funding['oid'] = (int)$object['id'];
								//var_dump($arr[1]);


								if(!$companydata = $company_model->where(array('Name'=>$company['Name']))->find()){
									$company['add_time'] = time();
									$companydata['id'] = $company_model->add($company);
									$this->updateCompany($companydata['id'],$company['Name']);
								}
								$object_funding['company_id'] = $companydata['id'];
								//var_dump($object_funding);
								if(!$object_funding_model->where($object_funding)->find()){
									if($object_funding_model->add($object_funding)){
										$count_funding++;
									}
								}
							}
						}


					}
				}
			}

			unset($data[0]['funding']);
			unset($data[0]['file']);
			unset($data[0]['li']);
			$model->where("id=".$object['id'])->save($data[0]);
		}

		echo $count_company;

        // if($o = M('Object')->where("id > ".$id)->find()){
        //     $url =  'http://zhixiaobing.com/index.php/Home/Update/updateFunding/id/'.$o['id'];
        //     echo "<script>location.href='".$url."';</script>";
        // }







	}



	public function updateCompany($id,$name){
		$model = M('Company');
		$url = 'http://i.yjapi.com/ECISimple/Search?key='.$this->ApiKey.'&keyword='.$name;
		$res =  json_decode(file_get_contents($url),true);


		if($res['Status'] == 200){
			if(count($res['Result']) == 1){
				$companyBase = array(
					'KeyNo'=>$res['Result']['KeyNo'],
					'OperName'=>$res['Result']['OperName'],
					'StartDate'=>strtotime($res['Result']['StartDate']),
					'Status'=>$res['Result']['Status'],
					'No'=>$res['Result']['No'],
					'update_time'=>time(),
					'zizhu'=>1,
				);
			}else{
				foreach($res['Result'] as $key => $value){
					if($value['Name'] == $name){
						$companyBase = array(
							'KeyNo'=>$value['KeyNo'],
							'OperName'=>$value['OperName'],
							'StartDate'=>strtotime($value['StartDate']),
							'Status'=>$value['Status'],
							'No'=>$value['No'],
							'update_time'=>time(),
							'zizhu'=>1,
						);
						break;
					}
				}

			}


			if(!empty($companyBase)){
				$model->where(array('id'=>$id))->save($companyBase);
			}

//			$companyDetails = array(
//				'BelongOrg'=>$res['Result']['BelongOrg'],
//				'Province'=>$res['Result']['Province'],
//				'UpdatedDate'=>strtotime($res['Result']['UpdatedDate']),
//				'RegistCapi'=>$res['Result']['RegistCapi'],
//				'EconKind'=>$res['Result']['EconKind'],
//				'Province'=>$res['Result']['Province'],
//				'Address'=>$res['Result']['Address'],
//				'Scope'=>$res['Result']['Scope'],
//				'EndDate'=>strtotime($res['Result']['EndDate']),
//				'TermStart'=>strtotime($res['Result']['TermStart']),
//				'TeamEnd'=>strtotime($res['Result']['TeamEnd']),
//				'CheckDate'=>strtotime($res['Result']['CheckDate']),
//			);
//			if($modelDetails->where(array('company_id'=>$id))->find()){
//				$modelDetails->where(array('company_id'=>$id))->save($companyDetails);
//			}else{
//				$companyDetails['company_id'] = $id;
//				$modelDetails->add($companyDetails);
//			}

		}
	}

	//更新申报通知 notices
	public function updateNotices(){
        set_time_limit(0);
        header("Content-Type: text/html;charset=utf-8");
		$url  = "http://www.shenkexin.com/project/notices.html?req=t1-0,";
		$t1 = array(11,12);
		$t2 = array(21,22,23,24);
		$t3 = array(31,32,33,34);
		$t4 = array(401,402,403,404,405,406,407,408,409,410,499);
		$t5 = array(51,52,53);
		$count = 0;

		$model = M('Notice');

		foreach($t2 as $a){
			foreach($t3 as $b){
				foreach($t4 as $c){
					foreach($t5 as $d){
						$weburl = $url.'t2-'.$a.',t3-'.$b.',t4-'.$c.',t5-'.$d;
						$pagedata = QueryList::Query($url,
							array(
								"number"=>array(".pager li:first a","text","",function($number){
									preg_match('/([0-9]{1,5})/',$number,$arr);
									if($arr){
										return (int)$arr[0];
									}
								}),
							)
						)->data;
						$value['level_id'] = $a;
						$value['efforts_id'] = $b;
						$value['type_id'] = $c;
						$value['wap_id'] = $d;
						if($pagedata[0]['number']){
							for($i=1;$i<=ceil($pagedata[0]['number']/15);$i++){
								$this->addNotice($url.'&idx='.$i,$value);
							}
						}else{
							$this->addNotice($url,$value);
						}
					}
				}
			}
		}


	}

	//更新notices 内容部分
	public function updateNoticesContent(){
        set_time_limit(0);
        header("Content-Type: text/html;charset=utf-8");

		$noticeModel = M('Notice');
		$notice = $noticeModel->select();
		//$notice = array_slice($notice,5);



		$site_url = 'http://www.shenkexin.com';
		foreach($notice as $k => $v){
			$tempdata = QueryList::Query($site_url.$v['url'],
				array(
					"description" => array(".resume p","text"),
					"end_time" => array(".clr li:first","text","",function($end_time){
						return strtotime($end_time);
					}),
					"department" => array(".clr li:eq(2)","text"),
					"money" => array(".clr li:last","text"),
					"content" => array(".article1:first .change_txt","html"),
					"file"=>array(".article1 .main .link-list","html","",function($file) use($v){
						preg_match_all("/href=[\'\"](.+?)[\'\"]/",$file,$href);
						preg_match_all("/title=[\'\"](.+?)[\'\"]/",$file,$title);
						$site_url = 'http://www.shenkexin.com';
						$file_model = M('NoticeFile');
						foreach($href[1] as $key => $value){
							$dir = explode('/',$value);
							$ext = strrchr($url,'.');
							if($dir){
								$filename = $dir[count($dir)-1];
							}else{
								$filename = time().'.'.$ext;
							}
							$save_dir = '.';
							foreach($dir as $x => $y){
								if(!empty($y) && !strpos($y,'.')){
									$save_dir .= '/'.$y;
								}
							}


							if($result = $this->getFile($site_url.$value,$save_dir,$filename)){
								if(file_exists($result['save_path']) && !$file_model->where(array('nid'=>$v['id'],'title'=>$title[1][$key],'url'=>$result['save_path']))->find()){
									$file_model->add(array('nid'=>$v['id'],'title'=>$title[1][$key],'url'=>$result['save_path']));
								}
							}
						}
					}),

				)
			)->data;

			if(!empty($tempdata[0])){
				$noticeModel->where(array('id'=>$v['id']))->save($tempdata[0]);
			}
		}
	}

	public function addNotice($url,$value=array()){
		$model = M('Notice');
		$tempdata = QueryList::Query($url,
			array(
				"title"=>array(".list1 h2 a","text"),
				"url"=>array(".list1 h2 a","href"),
				"time"=>array(".list1 .time","text","",function($time){
					return preg_replace("/申报时间：/","",$time);
				}),
				"status"=>array(".list1 h2 span.s13","text","",function($status){
					return $status == '申报已结束' ? 0 : 1;
				}),
				"id"=>array(".list1 h2 a","href","",function($id){
					preg_match('/notice-(.+)\./',$id,$arr);
					if($arr){
						return (int)$arr[1];
					}
				}),
			)
		)->data;
		if(!empty($tempdata)){
			foreach($tempdata as $key => $value){
				if(!$model->find($value['id'])){
					$model->add($value);
				}
			}

		}
	}

	//更新立项公示
	public function updatePublicity(){
        set_time_limit(0);
        header("Content-Type: text/html;charset=utf-8");
		$url = 'http://www.shenkexin.com/project/publicitys.html';
		//?idx=1
		$pagedata = QueryList::Query($url,
			array(
				"number"=>array(".pager li:first a","text","",function($number){
					preg_match('/([0-9]{1,5})/',$number,$arr);
					if($arr){
						return (int)$arr[0];
					}
				}),
			)
		)->data;

		if($pagedata[0]['number']){
			for($i=1;$i<=ceil($pagedata[0]['number']/15);$i++){
				$this->addPublic($url.'?idx='.$i,array());
			}
		}

	}

	//更新立项公示的内容
	public function updatePublicityContent(){
        set_time_limit(0);
        header("Content-Type: text/html;charset=utf-8");
		$noticeModel = M('Publicity');
        $type = M('ObjectType')->select();
        $id = $_GET['id'] ? $_GET['id'] : 1;
		$notice = $noticeModel->find($id);


        $money = mb_substr($notice['money'],0,-1);

        if($money<50){
            $update['efforts_id'] = 31;
        }elseif($money>=50 && $money<100){
            $update['efforts_id'] = 32;
        }elseif($money>=100 && $money<500){
            $update['efforts_id'] = 33;
        }elseif($money>=500){
            $update['efforts_id'] = 34;
        }

        $noticeModel->where(array('id'=>$notice['id']))->save($update);

        $tempdata = QueryList::Query($notice['url'],
                array(
                    "description" => array(".resume p","text"),
                    "end_time" => array(".resume div .clr li:first span","text","",function($end_time){
                        return strtotime($end_time);
                    }),
                    "type_id" => array(".resume div .clr li:eq(1) span","text","",function($type_id) use($type){
                        foreach($type as $key=>$value){
                            if($value['name'] == $type_id){
                                return $value['id'];
                            }
                        }
                    }),
                    "way" => array(".resume div .clr li:last span","text"),
                    "department" => array(".clr li:eq(2)","text"),
                    "content" => array(".article1:first .change_txt","html"),
                    "file"=>array(".article1 .main .link-list","html","",function($file) use($notice){
                        preg_match_all("/href=[\'\"](.+?)[\'\"]/",$file,$href);
                        preg_match_all("/title=[\'\"](.+?)[\'\"]/",$file,$title);
                        $site_url = 'http://www.shenkexin.com';
                        $file_model = M('PublicityFile');

                        foreach($href[1] as $key => $value){
                            $dir = explode('/',$value);
                            $ext = strrchr($url,'.');
                            if($dir){
                                $filename = $dir[count($dir)-1];
                            }else{
                                $filename = time().'.'.$ext;
                            }
                            $save_dir = '.';

                            foreach($dir as $x => $y){
                                if(!empty($y) && !strpos($y,'.')){
                                    $save_dir .= '/'.$y;
                                }
                            }

                            if($result = $this->getFile($site_url.$value,$save_dir,$filename)){
                                if(file_exists($result['save_path']) && !$file_model->where(array('pid'=>$notice['id'],'title'=>$title[1][$key],'url'=>$result['save_path']))->find()){
                                    $file_model->add(array('pid'=>$notice['id'],'title'=>$title[1][$key],'url'=>$result['save_path']));
                                }
                            }
                        }
                    }),

                )
        )->data;



        if(!empty($tempdata[0])){
            unset($tempdata[0]['file']);
            $noticeModel->where(array('id'=>$notice['id']))->save($tempdata[0]);
        }
         if($next = $noticeModel->where("id > ".$id)->find() ){
             echo "<script>location.href='http://www.zhixiaobing.com/index.php/Home/Update/updatePublicityContent/id/".$next['id']."'</script>";
         }











	}



	public function addPublic($url,$value=array()){
		$model = M('Publicity');
		$tempdata = QueryList::Query($url,
			array(
				"title"=>array(".list1 h2 a","text"),
				"url"=>array(".list1 h2 a","href","",function($url){
                    return 'http://www.shenkexin.com'.$url;
                }),
				// "add_time"=>array(".list1 .clr span.s1:first","text","",function($add_time){
				// 	return strtotime(preg_replace("/发布时间：/","",$add_time));
				// }),
                "li"=>array(".list1 .clr","html","",function($li){
                    preg_match_all("/<span.+>(.+)<\/span>/U",$li,$arr);
                    return $arr[1];
                }),
				"id"=>array(".list1 h2 a","href","",function($id){
					preg_match('/publicity-(.+)\./',$id,$arr);
					if($arr){
						return (int)$arr[1];
					}
				}),
			)
		)->data;

		if(!empty($tempdata)){
			foreach($tempdata as $key => $value){

                if($value['li'][0]){
                    $value['add_time'] = strtotime(preg_replace("/发布时间：/","",$value['li'][0]));
                }
                if($value['li'][2] == '人才计划'){
                    $value['type_id'] = 401;
                }elseif($value['li'][2] == '技术创新'){
                    $value['type_id'] = 402;
                }elseif($value['li'][2] == '技术改造'){
                    $value['type_id'] = 403;
                }elseif($value['li'][2] == '高技术产业化'){
                    $value['type_id'] = 404;
                }elseif($value['li'][2] == '企业管理提升'){
                    $value['type_id'] = 405;
                }elseif($value['li'][2] == '市场拓展'){
                    $value['type_id'] = 406;
                }elseif($value['li'][2] == '品牌培育'){
                    $value['type_id'] = 407;
                }elseif($value['li'][2] == '税费优惠'){
                    $value['type_id'] = 408;
                }elseif($value['li'][2] == '资质认定'){
                    $value['type_id'] = 409;
                }elseif($value['li'][2] == '文化创意'){
                    $value['type_id'] = 410;
                }elseif($value['li'][2] == '其他方向'){
                    $value['type_id'] = 499;
                }
                if($value['li'][3]){
                    $value['number'] = preg_replace("/资助企业数：/","",$value['li'][3]);
                }
                if($value['li'][4]){
                    $value['total_money'] = preg_replace("/资助总金额：/","",$value['li'][4]);
                }
                if($value['li'][5]){
                    $value['money'] = preg_replace("/最高资助额：/","",$value['li'][5]);
                }
                unset($value['li']);
				if(!$model->find($value['id'])){
					$model->add($value);
				}else{
                    $model->save($value);
                }
			}

		}
	}

	/*
	*功能：php完美实现下载远程图片保存到本地
	*参数：文件url,保存文件目录,保存文件名称，使用的下载方式
	*当保存文件名称为空时则使用远程文件原来的名称
	*/
	public function getFile($url,$save_dir='',$filename='',$type=0){
		if(trim($url)==''){
			return array('file_name'=>'','save_path'=>'','error'=>1);
		}
		if(trim($save_dir)==''){
			$save_dir='./';
		}
		if(trim($filename)==''){//保存文件名
			$ext=strrchr($url,'.');
			$filename=time().$ext;
		}
		if(0!==strrpos($save_dir,'/')){
			$save_dir.='/';
		}
		//创建保存目录
		if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
			return array('file_name'=>'','save_path'=>'','error'=>5);
		}
		//获取远程文件所采用的方法
		if($type){
			$ch=curl_init();
			$timeout=3;
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
			$img=curl_exec($ch);

			//amazon https://forums.aws.amazon.com/message.jspa?messageID=196878


			curl_close($ch);
		}else{
			ob_start();
			readfile($url);
			$img=ob_get_contents();
			ob_end_clean();
		}
		//$size=strlen($img);
		//文件大小
		$fp2=@fopen($save_dir.$filename,'w');
		fwrite($fp2,$img);
		fclose($fp2);
		unset($img,$url);
		return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0);
	}







	public function zhuanlizizhiwang(){
		set_time_limit(0);
		header("Content-type:text/html;charset=utf-8;");



		$url = 'http://www.sz.gov.cn/gsj/zjxx/qtzjxx/index_15868.htm';
		$html = file_get_contents($url);

		$data2 = QueryList::Query($html,
			array(
			"title" => array(".list div a","title"),
			"href" => array(".list div a","href"),
			"add_time" => array(".maintxt .rightmain .list div span","text","",function($time){
				return strtotime($time);
			}),
			)
		)->data;

		$objectModel = M('Object');
		$company_model = M('Company');
		$fundingModel = M('ObjectFunding');
		$data2 = array_slice($data2,0,5);





		foreach($data2 as $key => $value){
			$companyCount = 0;
			if( strpos($value['title'],'拨款') && (date("Y-m",$value['add_time']) == date("Y-m",time())) ) {
				preg_match("/(深圳市)第(.+)批(.+)(申请资助|资助|申请资助周转金)拨款/",$value['title'],$arr);



		        if($arr[2] == '一'){
		            $batch = "1";
		        }elseif($arr[2] == '二'){
		            $batch = "2";
		        }elseif($arr[2] == '三'){
		            $batch = "3";
		        }elseif($arr[2] == '四'){
		            $batch = "4";
		        }elseif($arr[2] == '五'){
		            $batch = "5";
		        }elseif($arr[2] == '六'){
		            $batch = "6";
		        }elseif($arr[2] == '七'){
		            $batch = "7";
		        }elseif($arr[2] == '八'){
		            $batch = "8";
		        }elseif($arr[2] == '九'){
		            $batch = "9";
		        }elseif($arr[2] == '十'){
		            $batch = "10";
		        }


				if($arr[1] == '深圳市' && $arr[2] && $arr[3] && $arr[4]){
					if(!$object = $objectModel->where(array('title'=>$arr[1].$arr[3].$arr[4],'year'=>$value['add_time']))->find()){
						$temp = array();
						$temp['title'] = $arr[1].$arr[3].$arr[4];
						$temp['time'] = '常年';
						$temp['level_id'] = 24;
						$temp['efforts_id'] = 0;
						$temp['type_id'] = 402;
						$temp['wap_id'] = 51;
                        $temp['add_time'] = $value['add_time'];
						$temp['url'] = 'http://www.sz.gov.cn/gsj/zjxx/qtzjxx'.substr($value['href'],1);
                        $temp['content'] = $this->content;
                        if(date("Y",$value['add_time']) == date("Y",time())){
                        	$temp['status'] = 1;
                        }else{
                        	$temp['status'] = 0;
                        }

                        $temp['attachment'] = $this->attachment;
                        $temp['year'] = $value['add_time'];
                        if($o = $objectModel->max('id')){
                            if($o<20132013){
                                $temp['id'] = 20132013;
                            }else{
                                $temp['id'] = $o +1;
                            }

                        }

                        $object['id'] = $objectModel->add($temp);
					}

					$temp = $objectModel->find($object['id']);

					$department = QueryList::Query(
                        $temp['url'],
                        array(
                            "department" => array(".maintxt.archive .ar .subtitle .l","text","",function($department){
                                $department = iconv('GBK','UTF-8',$department);
                                $search = array("来源："," ","　","\n","\r","\t");
                                $replace = array("","","","","","");
                                $department =  str_replace($search, $replace, $department);
                                preg_match_all("/(.+)日期(.+)/U",$department,$arr);
								return ($arr[1][0]);

                            }),

                        )
                    )->data;


					$html = (file_get_contents($temp['url']));
					$html = iconv('GBK','UTF-8',$html);
                    $search = array("　","\n","\r","\t");
                    $replace = array("","","","");
                    $html =  str_replace($search, $replace, $html);
					preg_match_all("/<div\s*class=(\"|')contentTxt(\"|')>(.+)<\/div>/i",$html,$htmlarr);

					preg_match_all("/<tr(.+)>(.+)<\/tr>/iU",$htmlarr[0][0],$arr2);
					preg_match_all("/<P>(.+)<\/P>/U",$htmlarr[0][0],$arr3);
					$data = array();
					if(count($arr3[1]) > 3){
						foreach($arr3[1] as $a=>$b){
							preg_match_all("/^(\d+)\s+(.+)\s+(\d+)$/iU",trim($b),$d);

							if($d){
								$tempzizhu = array();
								$tempzizhu[] = $d[1][0];
								$tempzizhu[] = $d[2][0];
								$tempzizhu[] = $d[3][0];
								$data[]= $tempzizhu;
							}
						}


					}else{
						foreach($arr2[0] as $a=>$b){
							preg_match_all("/<td(.+)>(.+)<\/td>/iU",$b,$d);
							preg_match_all("/<p(.+)>(.+)<\/p>/iU",$b,$d2);
		                    $search = array("","&nbsp;","　","\n","\r","\t");
		                    $replace = array("","","","","","");
							foreach ($d[2] as $x => $y) {
								$d[2][$x] = str_replace($search, $replace, trim(strip_tags($y)));
							}
							if(is_numeric($d[2][2])){
								$data[] = $d[2];
							}
						}

					}




                    $fundingCount = 0;
                    $maxMoney = 0;

                    foreach ($data as $k => $v) {
                        if(is_numeric($v[2])){
                            $funding = array();
                            $funding['subject'] =  $temp['title'];
                            $funding['batch'] =  date("Y",$value['add_time'])."-".$batch;
                            $funding['money'] =  number_format($v[2]/10000,3).'万';
                            $funding['oid'] =  $object['id'];
                            if($v[2]>$maxMoney){
                            	$maxMoney = $v[2];
                            }
							if(!$companydata = $company_model->where(array('Name'=>$v[1]))->find()){
								//var_dump($temp['url']);
								//var_dump($v[1]);
								$companydata['id'] = $company_model->add(array('Name'=>$v[1],'zizhu'=>1,'add_time'=>time()));
								$companyCount++;
								$this->updateCompany($companydata['id'],$v[1]);
							}
							$funding['company_id'] =  $companydata['id'];
							//var_dump($funding);
							if(!$fundingModel->where($funding)->find()){

								$fundingModel->add($funding);
								$fundingCount++;
							}


                        }

                    }
                    //更新项目金额
                    if($maxMoney<500000){
                    	$objectData['efforts_id'] = 31;
                    }elseif($maxMoney>=500000 && $maxMoney<1000000){
                    	$objectData['efforts_id'] = 32;
                    }elseif($maxMoney>=1000000 && $maxMoney<5000000){
                    	$objectData['efforts_id'] = 33;
                    }elseif($maxMoney>5000000){
                    	$objectData['efforts_id'] = 34;
                    }

                    $objectData['money'] =number_format($maxMoney/10000,3).'万元';
                    $objectData['department'] = $department[0]['department'];
                    $objectData['id'] = $object['id'];
                    $objectData['description'] = $arr[1].$arr[3].$arr[4].'是由深圳市市场和质量监督管理委员会所审批的项目资助,审核通过后,最高可获取'.$objectData['money'];
                    $objectModel->save($objectData);
				}

				var_dump('第'.($key+1).'轮更新完成');
				var_dump('共新增'.$companyCount.'家企业,'.$fundingCount.'项资助列表');

			}






		}
	}


    //龙华区级资助
    public function longhuaQujizizhu(){
        set_time_limit(0);
        header("Content-type:text/html;charset=utf-8;");
        $company_model = M('Company');
        $objectModel = M('Object');
        $fundingModel = M('ObjectFunding');

        $url = 'http://jfj.szlhxq.gov.cn/lhjjfwj/wsfw/kjcxjl10/zyzz74/qjzz36/index.html';

        $siteurl = 'http://jfj.szlhxq.gov.cn';


        $data2 = QueryList::Query($url,

            array(

            "title" => array("td.art_tit a","title"),
            "href" => array("td.art_tit a","href"),
            "add_time" => array("td.date","text","",function($time){
                return strtotime($time);
            }),
            "create" => array("td.date","text")
            )
        )->data;
        //$data2 = array_slice($data2,0,1);


        $delstr = array('拟资助企业','拟资助','名单公示','名单');
        foreach($data2 as $key => $value){
            preg_match_all("/([\d]{4})(.+)(名单)(.*)/",$value['title'],$arr);
           if($arr[0]){

                $arr[0] = str_replace($delstr,array('','','',''),$arr[0]);

                preg_match_all("/第?(.+?)批?/",$arr[0][0],$pici);
                if($pici[1][0]){
                    $batch = changeNumber($pici[1][0]);
                }elseif(strpos($arr[0][0],'首批') !== false){
                    $batch = '1';
                }else{
                    $batch = '';
                }

                $title = $arr[0][0];

                if(!$object = $objectModel->where(array('title'=>$title,'year'=>$value['add_time']))->find()){
                    $temp = array();
                    $temp['title'] = $title;
                    $temp['time'] = '常年';
                    $temp['level_id'] = 24;
                    $temp['efforts_id'] = 0;
                    $temp['type_id'] = 402;
                    $temp['wap_id'] = 51;
                    $temp['add_time'] = $temp['year'] = $value['add_time'];
                    $temp['url'] = $siteurl.$value['href'];
                    $temp['content'] = '';
                    if(date("Y",$value['add_time']) == date("Y",time())){
                        $temp['status'] = 1;
                    }else{
                        $temp['status'] = 0;
                    }
                    $temp['attachment'] = '';
                    if($o = $objectModel->max('id')){
                        if($o<20132013){
                            $temp['id'] = 20132013;
                        }else{
                            $temp['id'] = $o +1;
                        }
                    }
                    $object['id'] = $objectModel->add($temp);
                }else{
                    $temp = $objectModel->find($object['id']);
                }


                $data = QueryList::Query($temp['url'],
                    array(
                    "content" => array(".content_con","html"),
                    )
                )->data;
                $data[0]['content'] = str_replace(array("\r","\n","\t"),array("","",""),$data[0]['content']);
                preg_match_all("/<a href=\"(.+)\"(.+)>(.+)<\/a>/U",$data[0]['content'],$file);


                if(!$file[1]){

                    $content_table = array();
                    $data = QueryList::Query($temp['url'],
                        array(
                        "content" => array(".content_con table tr","html"),
                        )
                    )->data;

                    foreach ($data as $contentkey => $contentvalue) {
                        $contentvalue['content'] = str_replace(array("　","\n","\r","\t"),array("","","",""),$contentvalue['content']);
                        //var_dump($contentvalue['content']);
                         preg_match_all('/<td(.*)>(.+)<\/td>/U',$contentvalue['content'],$file_td);
                         $temp_file_td = array();
                         foreach($file_td[2] as $file_td_key => $file_td_value){
                            $temp_file_td[] = trim(strip_tags($file_td_value));
                         }

                         $content_table[] = $temp_file_td;
                         // var_dump($content_table);
                    }


                    $temp_header = array_slice($content_table,0,1);
                    foreach ($temp_header[0] as $temp_header_key => $temp_header_value) {
                        if($temp_header_value == '单位名称' || $temp_header_value == '企业名称' || $temp_header_value == '申请单位'){
                            $company_key = $temp_header_key;
                            break;
                        }
                    }


                    if($company_key){
                        unset($content_table[0]);
                        foreach ($content_table as $$content_table_key => $v) {
                                if(count($v) == count($temp_header[0])){
                                    $companyName = $v[$company_key];
                                }else{
                                    $companyName = $v[$company_key-1];
                                }

                                $subject = $title;
                                $funding = array();
                                $funding['subject'] =  $subject;
                                $funding['batch'] =  date("Y",$value['add_time'])."-".$batch;
                                //$funding['money'] = number_format($money,2) .'万';
                                $funding['oid'] =  $object['id'];
                                //$funding['company'] =  $companyName;
                                //$funding['name'] =  $v['C'];

                                $maxMoney = '未公开';


                                if(!$companydata = $company_model->where(array('Name'=>$companyName))->find()){
                                    $companydata['id'] = $company_model->add(array('Name'=>$companyName,'zizhu'=>1,'add_time'=>time()));
                                    $companyCount++;
                                }
                                $this->updateCompany($companydata['id'],$v['C']);
                                $funding['company_id'] =  $companydata['id'];
                                //var_dump($funding);
                                if(!$fundingModel->where($funding)->find()){
                                    $fundingModel->add($funding);
                                    $fundingCount++;
                                }


                        }
                    }


                }else{
                    //var_dump($file[1]);
                    foreach($file[1] as $file_key => $file_value){
                        preg_match_all("/\/(.+)\.(xls|xlsx)$/U",$file_value,$file_value_path);
                        $file_value_path_arr = explode("/",$file_value_path[1][0]);
                        $downfile = $this->getFile($siteurl.$file_value,'./Public/upload/File/'.date("Y-m-d",time()),$file_value_path_arr[count($file_value_path_arr)-1].".".$file_value_path[2][0]);

                    if($downfile['error'] == 0){
                        $data = readExcel($downfile['save_path']);


                        $temp_header = array_slice($data,0,5);
                        $num = null;
                        $key_file = null;
                        //var_dump($temp_header);
                        foreach ($temp_header as $temp_header_key => $temp_header_value) {
                            //var_dump($temp_header_value);
                            foreach ($temp_header_value as $temp_header_value_key => $temp_header_value_value) {

                                if($temp_header_value_value == '序号'){
                                    $num = $temp_header_value_key;
                                }
                                if($temp_header_value_value == '企业名称' || $temp_header_value_value == '名称' || $temp_header_value_value == '单位名称'){
                                    $key_file = $temp_header_value_key;
                                }
                                if($num && $key_file){
                                    break;
                                }
                            }

                        }
                        //var_dump($key_file);
                        if($num && $key_file){
                            $next_num = $num++;
                            foreach ($data as $k => $v) {

                                if($v[$num] != '序号' && $v[$key_file] !='' && $v[$key_file] !='企业名称' && $v[$key_file] !='名称' && $v[$key_file] !='单位名称'  && $v[$next_num] !='' ){

                                    $companyName = $v[$key_file];

                                    $subject = $title;
                                    $funding = array();
                                    $funding['subject'] =  $subject;
                                    $funding['batch'] =  date("Y",$value['add_time'])."-".$batch;
                                    $funding['oid'] =  $object['id'];
                                    //$funding['name'] =  $companyName;
                                    //var_dump($funding);

                                    var_dump($funding);
                                    if(!$companydata = $company_model->where(array('Name'=>$companyName))->find()){
                                        $companydata['id'] = $company_model->add(array('Name'=>$companyName,'zizhu'=>1,'add_time'=>time()));
                                        $companyCount++;
                                    }
                                    $this->updateCompany($companydata['id'],$companyName);
                                    $funding['company_id'] =  $companydata['id'];
                                    //var_dump($funding);
                                    if(!$fundingModel->where($funding)->find()){
                                        $fundingModel->add($funding);

                                        $fundingCount++;
                                    }

                                }
                            }



                        }


                    }



                }





                    //更新项目金额
                    if($maxMoney<50*10000){
                        $objectData['efforts_id'] = 31;
                    }elseif($maxMoney>=50*10000 && $maxMoney<100*10000){
                        $objectData['efforts_id'] = 32;
                    }elseif($maxMoney>=100*10000 && $maxMoney<500*10000){
                        $objectData['efforts_id'] = 33;
                    }elseif($maxMoney>500*10000){
                        $objectData['efforts_id'] = 34;
                    }

                    $objectData['money'] = $maxMoney;
                    $objectData['department'] = '龙华区经济促进局';
                    $objectData['id'] = $object['id'];
                    $objectData['description'] = $title.'是由龙华区经济促进局审批的项目资助,审核通过后,最高可获取'.$objectData['money'];
                    $objectModel->save($objectData);



                }

            }

            var_dump('第'.($key+1).'轮更新完成');
            var_dump('共新增'.$companyCount.'家企业,'.$fundingCount.'项资助列表');
        }


    }

    public function updatePublicityOid(){
        $object = M('Object')->select();
        $model = M('Publicity');
        foreach ($object as $key => $value) {
            if($value['publicity_ids']){
                var_dump($value['publicity_ids']);
                $model->where("id in(".$value['publicity_ids'].")")->save(array('oid'=>$value['id']));
            }
        }
    }
}