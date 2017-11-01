<?php


require_once dirname(__FILE__).'/BaseCase.php';
require_once dirname(__FILE__).'/../CommonClass/ApiName.php';
require_once dirname(__FILE__).'/../CommonClass/ApiParms.php';


class CarTest extends BaseCase{
	//包含：选择用车，当前行程信息，开车门，还车,行程历史
	//选择车辆        		/choose-car/index.html
	//使用某一辆车  		/choose-car/submit-order.html
	//当前行程信息    		/tour/index.html
	//开车门，还车    		/api/use_car.php    ----老接口
	//行程历史    			/tour/history-list.html
	//行程管理列表信息		/tour/detail.html      action = "tour_detail"

	private $url;
	private $parms;
	public static $carid;
	public static $pricekm;
	public static $pricemin;
	public static $orderid;
	private static $pub_data;
	private static $carinfo ;
	//private $stack ;
	// public static function setUpBeforeClass(){
		
	// }




	//进行用车并返回订单相关信息
	function  test_get_car_info(){
		$carinfo = [] ;
		$this->url_choose = $this->get_url(choose_car);
		$this->parms_choose  = $this->get_parms();
		$this->parms_choose['uid'] = self::$uid;
		$this->parms_choose['key'] = self::$key;
		$this->parms_choose['wd_id'] = wd_id;
		$this->parms_choose['ret_wd_id'] = ret_wd_id;
		$this->parms_choose['city'] = city;
		$this->parms_choose['city_id'] = city_id;
		//print_r($this->parms) ;
		$this->result=$this->post_reslut($this->url_choose,$this->parms_choose);
		$carnums = count($this->result['data']) ;
		for ($i=0; $i < $carnums; $i++) { 
			$this->result=$this->post_reslut($this->url_choose,$this->parms_choose);
			if($this->result['status']!=0){
				echo "CarTest:::"."choose_car/index.html:::".$this->rlt['msg'] ;
				return False ;
			}
			$dist_remain = $this->result['data'][$i]['dist_remain'] ;
			$carid = $this->result['data'][$i]['id'] ;
			$pricekm = $this->result['data'][$i]['price_km'] ;
			$pricemin = $this->result['data'][$i]['price_min'] ;
			//update_dist_remain(self::$conn,$dist_remain,$carid,1) ;	//修改里程到符合夜租车
			$this->url = $this->get_url(choose_car_submit);
			$this->parms  = $this->get_parms();
			$this->parms['uid'] = self::$uid;
			$this->parms['key'] = self::$key;
			$this->parms['wd_id'] = wd_id;
			$this->parms['ret_wd_id'] = ret_wd_id;
			$this->parms['bjmp_flag'] = bjmp_flag;
			$this->parms['city'] = city;
			$this->parms['city_id'] = city_id;
			$this->parms['car_id'] = $carid;
			$this->parms['price_km'] = $pricekm;
			$this->parms['price_min'] = $pricemin;
			$this->rlt=$this->post_reslut($this->url,$this->parms);
			if($this->rlt['status']==0){
				$car_orderid = $this->rlt['data']['order_id']; 
				array_push($carinfo, $carid,$pricekm,$pricemin,$car_orderid) ;
				break ;
			}else{
				if($i<$carnums){
					$n = $i + 1 ;
					echo "CarTest:::"."第".$n."辆返回提示::".$this->rlt['msg'] .'。 ' ;
					continue ;
				}else{
					return False ;
				}
			}
		}
		$this->assertEquals(count($carinfo),4) ;
		return $carinfo ;

	}






	//第一次查看行程详情
	/**
	 * @depends test_get_car_info
	 */
	public function test_tourdetail1($carinfo){
		$this->url = $this->get_url(tour_detail);
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['action'] = tour_action_detail;
		$this->parms['ddid'] = $carinfo[3];
		$this->parms['city'] = city;
		$this->parms['city_id'] = city_id;
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//print_r($this->rlt);
		$this->assertEquals($this->rlt['data']['dd_id'],$carinfo[3]);
		$this->assertEquals($this->rlt['data']['price_km'],$carinfo[1]) ;
		$this->assertEquals($this->rlt['data']['price_min'],$carinfo[2]) ;
		$this->assertEquals($this->rlt['data']['status'],0) ;                       
		
	}


	//用例3：查看当前用车的行程信息
	/**
	 * @depends test_get_car_info
	 */
	public function test_tourindex($carinfo){
		$this->url = $this->get_url(tour_index);
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['action'] = action;
		$this->parms['city'] = city;
		$this->parms['city_id'] = city_id;
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//print_r($this->rlt['data']['dd']['car_id']);
		$this->assertEquals($this->rlt['data']['dd']['car_id'],$carinfo[0]);
		$this->assertEquals($this->rlt['data']['dd']['wd_id'],wd_id);
	}




	//用例4：进行开车门操作，这是一个老接口，迁移后需要写新的接口 。
	/**
	 * @depends test_get_car_info
	 */
	public function test_opencardoor($carinfo){
		$this->url = $this->get_url(use_car,1);
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['ddid'] = $carinfo[3];
		$this->parms['action'] = action_open;
		$this->parms['car_id'] = $carinfo[0];
		$this->parms['city'] = city_id;
		$this->parms['city_id'] = city_id;
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		if($this->rlt['status']==0){
			$this->assertEquals($this->rlt['msg'],"开车门成功");
		}
		else{
			echo $this->rlt['msg'];
		}
	}



	//第二次查看行程详情
	/**
	 * @depends test_get_car_info
	 */
	public function test_tourdetail2($carinfo){
		$this->url = $this->get_url(tour_detail);
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['action'] = tour_action_detail;
		$this->parms['ddid'] = $carinfo[3];
		$this->parms['city'] = city;
		$this->parms['city_id'] = city_id;
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//print_r($this->rlt);
		$this->assertEquals($this->rlt['data']['dd_id'],$carinfo[3]);
		$this->assertEquals($this->rlt['data']['price_km'],$carinfo[1]) ;
		$this->assertEquals($this->rlt['data']['price_min'],$carinfo[2]) ;
		$this->assertEquals($this->rlt['data']['status'],1) ;            //断言状态为订单已开始
	}


	// //用例5，进行还车，老接口，迁移后需要修改
	/**
	 * @depends test_get_car_info
	*/
	public function test_backcar($carinfo){
		$this->url = $this->get_url(use_car,1);
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['floor_num'] = floor_num;
		$this->parms['coupon_id'] = coupon_id;
		$this->parms['ret_wd_id'] = ret_wd_id;
		$this->parms['ddid'] = $carinfo[3];
		$this->parms['action'] = action_close;
		$this->parms['pop_flag'] = pop_flag;
		$this->parms['city'] = city;
		$this->parms['city_id'] = city_id;
		$this->parms['car_id'] = $carinfo[0];
		$this->parms['car_lat'] = car_lat;
		$this->parms['car_lng'] = car_lng;
		//print_r($this->parms);
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//echo "进行还车" ;
		//print_r($this->rlt);
		$this->assertEquals($this->rlt['status'],0);
	}



	//第三次还车成功的订单详情
	/**
	 * @depends test_get_car_info
	*/
	public function test_tourdetail3($carinfo){
		$this->url = $this->get_url(tour_detail);
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['action'] = tour_action_detail;
		$this->parms['ddid'] = $carinfo[3];
		$this->parms['city'] = city;
		$this->parms['city_id'] = city_id;
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//print_r($this->rlt);
		$this->assertEquals($this->rlt['data']['dd_id'],$carinfo[3]);
		$this->assertEquals($this->rlt['data']['price_km'],$carinfo[1]) ;
		$this->assertEquals($this->rlt['data']['price_min'],$carinfo[2]) ;
		$this->assertEquals($this->rlt['data']['status'],2) ;				//断言状态为订单已完成
	}


	//用例6：查看用车之后的历史行程
	/**
	 * @depends test_get_car_info
	*/
	public function test_tourhistory($carinfo){
		$this->url = $this->get_url(tour_history_list);
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['action'] = action_list;
		$this->parms['offset'] = offset;
		$this->parms['limit'] = limit;
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		//print_r($this->rlt);
		if($this->rlt['status']==0){
			$this->assertEquals($this->rlt['data']['dd_old'][0]['dd_id'],$carinfo[3]);
			$this->assertEquals($this->rlt['data']['dd_old'][0]['status'],"已完成");
			$this->assertEquals($this->rlt['data']['dd_old'][0]['wd_id'],wd_id);
		}else{
			echo  $this->rlt['status'].$this->rlt['msg'];
		}
		
	}


	// public  static function tearDownAfterClass(){
	// 	$id = self::$uid;
	// 	$sql =  "select id from 630_share where uid ='{$id}'";
	// 	$deldata = "delete from 630_share where uid = '{$id}'";
	// 	$info = self::$conn->query($sql);
	// 	echo "-----------------------";
	// 	print_r($info->fetch_row());
	// 	$data = $info->fetch_row();
	// 	if($data == null){
	// 		self::$conn->query($deldata);
	// 		//echo "删除成功！！！！！！！！！";
	// 	}
	// }




}