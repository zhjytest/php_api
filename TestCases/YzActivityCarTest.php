<?php


require_once dirname(__FILE__).'/BaseCase.php';
require_once dirname(__FILE__).'/../CommonClass/ApiName.php';
require_once dirname(__FILE__).'/../CommonClass/ApiParms.php';

class YzActivityCarTest extends BaseCase{
	//包含：网点列表，选择用车，确定某一辆车，订单详情
	//网点列表				/api/get_zhan_list.php
	//选择车辆        		/choose-car/index.html
	//使用某一辆车  		/choose-car/submit-order.html
	//当前行程信息    		/tour/index.html


	private $url;
	private $parms;
	private static $pubdata;

	public static function setUpBeforeClass(){
		self::$pubdata = array("title"=>"夜租车活动","act_icon"=>"http://imgtest.yiduyongche.com/wd_icon/tuijian/hongbao_yezu.png","des"=>"每日21点-次日9点，还车时里程符合条件，则可享受最低28元夜租价","h5_url"=>"http://test.yiduyongche.com/activity-night-rent-car/detail.html");
	}






	//判断夜租车的图标类型
	public function test_zhanlist(){
		$carinfo = [] ;
		$this->url = $this->get_url(zhan_list,1);
		$this->parms  = $this->get_parms();
		$this->parms['city'] = city;
		$this->parms['city_id'] = city_id;
		//print_r($this->parms) ;
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		$this->print_log('YzActivityCarTest.php','test_zhanlist',$this->rlt,0) ;
		//print_r($this->rlt) ;
		$wdnums = count($this->rlt['data']) ;
		for($i = 0 ;$i<$wdnums;$i++){
			$icon_id = $this->rlt['data'][$i]['icon_id'] ;
			//echo ":::::".$icon_id."::::::" ;
			if($icon_id == 7){
				$wd_id = $this->rlt['data'][$i]['id'] ;
				//echo "=====".$wd_id."========" ;
				array_push($carinfo,$wd_id);
				break ;
			}
		}
		$this->assertEquals(count($carinfo),1) ;
		return $carinfo ;
	}






	//查看用户车辆信息
	/**
	 * @depends test_zhanlist
	 */
	function  test_get_car_info($carinfo){
		//$carinfo = [] ;
		$this->url_choose = $this->get_url(choose_car);
		$this->parms_choose  = $this->get_parms();
		$this->parms_choose['uid'] = self::$uid;
		$this->parms_choose['key'] = self::$key;
		$this->parms_choose['wd_id'] = $carinfo[0];
		$this->parms_choose['ret_wd_id'] = $carinfo[0];
		$this->parms_choose['city'] = city;
		$this->parms_choose['city_id'] = city_id;
		$this->result=$this->post_reslut($this->url_choose,$this->parms_choose);
		$carnums = count($this->result['data']) ;   //获取车辆的数量
		for ($i=0; $i < $carnums; $i++) { 
			$this->result=$this->post_reslut($this->url_choose,$this->parms_choose);
			if($this->result['status']!=0){
				echo "YzActivityCarTest:::"."choose_car/index.html:::".$this->rlt['msg'] ;
				return False ;
			}
			//$dist_remain = $this->result['data'][$i]['dist_remain'] ;
			$carid = $this->result['data'][$i]['id'] ;
			$pricekm = $this->result['data'][$i]['price_km'] ;
			$pricemin = $this->result['data'][$i]['price_min'] ;
			//update_dist_remain(self::$conn,$dist_remain,$carid,1) ;	//修改里程
			$this->url = $this->get_url(choose_car_submit);
			$this->parms  = $this->get_parms();
			$this->parms['uid'] = self::$uid;
			$this->parms['key'] = self::$key;
			$this->parms['wd_id'] = $carinfo[0];
			$this->parms['ret_wd_id'] = $carinfo[0];
			$this->parms['bjmp_flag'] = bjmp_flag;
			$this->parms['city'] = city;
			$this->parms['city_id'] = city_id;
			$this->parms['car_id'] = $carid;
			$this->parms['price_km'] = $pricekm;
			$this->parms['price_min'] = $pricemin;
			$this->rlt=$this->post_reslut($this->url,$this->parms);
			if($this->rlt['status']==0){
				$car_orderid = $this->rlt['data']['order_id']; 
				array_push($carinfo,$carid,$pricekm,$pricemin,$car_orderid) ;
				break ;
			}else{
				if($i<$carnums){
					$n = $i + 1 ;
					echo "YzActivityCarTest:::"."第".$n."辆返回提示::".$this->rlt['msg'] ."。 " ;
					continue ;
				}else{
					return False ;
				}
			}
		}
		$this->assertEquals(count($carinfo),5) ;
		return $carinfo ;

	}


	// //选择用车,对夜租车进行验证,
	// /**
	//  * @depends test_zhanlist
	//  */
	// public function test_chooseyzactivitycar($carinfo){
	// 	//$carinfo = [] ;
	// 	//print_r($wdid) ;
	// 	$this->url = $this->get_url(choose_car);
	// 	$this->parms  = $this->get_parms();
	// 	$this->parms['uid'] = self::$uid;
	// 	$this->parms['key'] = self::$key;
	// 	$this->parms['wd_id'] = $carinfo[0];
	// 	$this->parms['ret_wd_id'] = $carinfo[0];
	// 	$this->parms['city'] = city;
	// 	$this->parms['city_id'] = city_id;
	// 	$this->rlt=$this->post_reslut($this->url,$this->parms);
	// 	$this->print_log('YzActivityCarTest.php','test_chooseyzactivitycar',$this->rlt,0) ;
	// 	$carnum = count($this->rlt['data']) ;
	// 	for($i = 0 ;$i<$carnum;$i++){
	// 		$dist_remain = $this->rlt['data'][$i]['dist_remain'] ;
	// 		//echo ":::::".$icon_id."::::::" ;
	// 		if($dist_remain >= 45 && $dist_remain <=90){
	// 			//$wd_id = $this->rlt['data'][$i]['id'] ;
	// 			$carid = $this->rlt['data'][$i]['id'] ;
	// 			$pricekm = $this->rlt['data'][$i]['price_km'] ;
	// 			$pricemin = $this->rlt['data'][$i]['price_min'] ;
	// 			array_push($carinfo, $carid,$pricekm,$pricemin) ;
	// 			$this->assertEquals($this->rlt['data'][$i]['activity']['title'],self::$pubdata['title']);
	// 			$this->assertEquals($this->rlt['data'][$i]['activity']['act_icon'],self::$pubdata['act_icon']);
	// 			$this->assertEquals($this->rlt['data'][$i]['activity']['des'],self::$pubdata['des']);
	// 			$this->assertEquals($this->rlt['data'][$i]['activity']['h5_url'],self::$pubdata['h5_url']);
	// 			break ;
	// 		}
	// 	}
	// 	return $carinfo ;	
	// }





	// //用例2：选择一辆车，并使用车该车
	// /**
	//  * @depends test_chooseyzactivitycar
	//  */
	// public function test_yzactivitysubmit($carinfo){
	// 	//print_r($carinfo);
	// 	$wdid = $carinfo[0] ;		//获取车辆网点id
	// 	$car_id = $carinfo[1] ;      //获取车辆id
	// 	$car_pricekm =$carinfo[2] ;    //获取车辆里程价格
	// 	$car_pricemin = $carinfo[3] ;   //获取车辆每分钟价格
	// 	$this->url = $this->get_url(choose_car_submit);
	// 	$this->parms  = $this->get_parms();
	// 	$this->parms['uid'] = self::$uid;
	// 	$this->parms['key'] = self::$key;
	// 	$this->parms['wd_id'] = $wdid;
	// 	$this->parms['ret_wd_id'] = $wdid;
	// 	$this->parms['bjmp_flag'] = bjmp_flag;
	// 	$this->parms['city'] = city;
	// 	$this->parms['city_id'] = city_id;
	// 	$this->parms['car_id'] = $car_id;
	// 	$this->parms['price_km'] = $car_pricekm;
	// 	$this->parms['price_min'] = $car_pricemin;
	// 	$this->rlt=$this->post_reslut($this->url,$this->parms);
	// 	$this->print_log('YzActivityCarTest.php','test_yzactivitysubmit',$this->rlt,0) ;
	// 	//$car_orderid = $this->rlt['data']['order_id']; 
	// 	//array_push($carinfo, $car_orderid) ;
	// 	$this->assertEquals($this->rlt['msg'],'提交订单成功');

	// 	//return $carinfo ;
	// }



	//用例3：滑动开车门页面，查看夜租显示
	/**
	 * @depends test_get_car_info
	 */
	public function test_yzactivitytourindex(){
		$this->url = $this->get_url(tour_index);
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['action'] = action;
		$this->parms['city'] = city;
		$this->parms['city_id'] = city_id;
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		$this->print_log('YzCarTest.php','test_yzactivitytourindex',$this->rlt,0) ;
		$this->assertEquals($this->rlt['data']['dd']['car_activity']['title'],self::$pubdata['title']);
		$this->assertEquals($this->rlt['data']['dd']['car_activity']['act_icon'],self::$pubdata['act_icon']);
		$this->assertEquals($this->rlt['data']['dd']['car_activity']['des'],self::$pubdata['des']);
		$this->assertEquals($this->rlt['data']['dd']['car_activity']['h5_url'],self::$pubdata['h5_url']);
	}




	public  static function tearDownAfterClass(){
		$uid = self::$uid;
		$sql =  "select id,status from 630_tour where customer_id = '{$uid}'  ORDER BY id  desc LIMIT 1;";
		$info = self::$conn->query($sql);
		echo "-----------------------";
		$data = $info->fetch_row() ;
		//print_r($info->fetch_row());
		$tourid = $data[0] ;
		$status = $data[1];
		$updata = "update 630_tour  set status = 2 where id = '{$tourid}'";
		if($status != 2){
			self::$conn->query($updata);
			//echo "更新成功！！！！！！！！！";
		}
	}




}