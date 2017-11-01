<?php


require_once dirname(__FILE__).'/BaseCase.php';
require_once dirname(__FILE__).'/../CommonClass/ApiName.php';
require_once dirname(__FILE__).'/../CommonClass/ApiParms.php';
require_once dirname(__FILE__).'/../CommonClass/yzcar.php';


class YzACarTest extends BaseCase{
	//测试夜租28元套餐，包括我要还车页面，还车成功页面，还车详情页面

	private $url;
	private $parms;
	private static $times;


	/**
	 * @depends AALoginTest.test_login_success
	 */
	public static function setUpBeforeClass(){
		$totle_times = get_time(); 	//获取服务器(108)时间
		self::$times = explode(" ", $totle_times)[1] ;
		echo "time::".self::$times ;	//2017-10-26 16:35:40
		update_time(1) ;	//修改时间为21:30，在夜租时间内
	}


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
		//$this->result=$this->post_reslut($this->url,$this->parms);
		$this->result=$this->post_reslut($this->url_choose,$this->parms_choose);
		$carnums = count($this->result['data']) ;
		for ($i=0; $i < $carnums; $i++) { 
			$this->result=$this->post_reslut($this->url_choose,$this->parms_choose);
			if($this->result['status']!=0){
				echo "YzACarTest:::"."choose_car/index.html:::".$this->rlt['msg'] ;
				return False ;
			}
			$dist_remain = $this->result['data'][$i]['dist_remain'] ;
			$carid = $this->result['data'][$i]['id'] ;
			$pricekm = $this->result['data'][$i]['price_km'] ;
			$pricemin = $this->result['data'][$i]['price_min'] ;
			update_dist_remain(self::$conn,$dist_remain,$carid,1) ;	//修改里程到符合夜租车
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
					echo "YzACarTest:::"."第".$n."辆返回提示::".$this->rlt['msg'] .'。 ' ;
					continue ;
				}else{
					return False ;
				}
			}
		}
		$this->assertEquals(count($carinfo),4) ;
		return $carinfo ;

	}

	// //选择用车,对夜租车进行验证,
	// public function test_chooseyzcar(){
	// 	$carinfo = [] ;
	// 	$this->url = $this->get_url(choose_car);
	// 	$this->parms  = $this->get_parms();
	// 	$this->parms['uid'] = self::$uid;
	// 	$this->parms['key'] = self::$key;
	// 	$this->parms['wd_id'] = wd_id;
	// 	$this->parms['ret_wd_id'] = ret_wd_id;
	// 	$this->parms['city'] = city;
	// 	$this->parms['city_id'] = city_id;
	// 	//print_r($this->parms) ;
	// 	$this->rlt=$this->post_reslut($this->url,$this->parms);
	// 	$this->print_log('YzACarTest.php','test_chooseyzcar',$this->rlt,0) ;
	// 	$dist_remain = $this->rlt['data'][0]['dist_remain'] ;
	// 	$carid = $this->rlt['data'][0]['id'] ;
	// 	$pricekm = $this->rlt['data'][0]['price_km'] ;
	// 	$pricemin = $this->rlt['data'][0]['price_min'] ;
	// 	array_push($carinfo, $carid,$pricekm,$pricemin) ;
	// 	update_dist_remain(self::$conn,$dist_remain,$carid,1) ;
	// 	return $carinfo ;
	// }



	// //用例2：选择一辆车，并使用车该车
	// /**
	//  * @depends test_chooseyzcar
	//  */
	// public function test_yzchoosesubmit($carinfo){
	// 	//$arr = self::$carinfo ;
	// 	//$stack = [] ;
	// 	$this->url = $this->get_url(choose_car_submit);
	// 	$this->parms  = $this->get_parms();
	// 	$this->parms['uid'] = self::$uid;
	// 	$this->parms['key'] = self::$key;
	// 	$this->parms['wd_id'] = wd_id;
	// 	$this->parms['ret_wd_id'] = ret_wd_id;
	// 	$this->parms['bjmp_flag'] = bjmp_flag;
	// 	$this->parms['city'] = city;
	// 	$this->parms['city_id'] = city_id;
	// 	//foreach ($arr as &$value) {    //拿到所有车辆的id,里程价格，时间价格
	// 	$car_id = $carinfo[0] ;      //获取车辆id
	// 	$car_pricekm =$carinfo[1] ;    //获取车辆里程价格
	// 	$car_pricemin = $carinfo[2] ;   //获取车辆每分钟价格
	// 	$this->parms['car_id'] = $car_id;
	// 	$this->parms['price_km'] = $car_pricekm;
	// 	$this->parms['price_min'] = $car_pricemin;
	// 	$this->rlt=$this->post_reslut($this->url,$this->parms);
	// 	$this->print_log('YzACarTest.php','test_yzchoosesubmit',$this->rlt,0) ;
	// 	$car_orderid = $this->rlt['data']['order_id']; 
	// 	array_push($carinfo, $car_orderid) ;
	// 	$this->assertEquals($this->rlt['msg'],'提交订单成功');
	// 	return $carinfo ;
	// }





	//用例：进行开车门操作，这是一个老接口，迁移后需要写新的接口 。
	/**
	 * @depends test_get_car_info
	 */
	public function test_opencardoor($carinfo){
		//print_r($carinfo) ;
		$this->url = $this->get_url(use_car,1);
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['ddid'] = $carinfo[3];
		$this->parms['action'] = action_open ;
		$this->parms['car_id'] = $carinfo[0];
		$this->parms['city'] = city;
		$this->parms['city_id'] = city_id;
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		$this->print_log('YzACarTest.php','test_opencardoor',$this->rlt,0) ;
		$this->assertEquals($this->rlt['msg'],"开车门成功");
		update_dist_remain(self::$conn,0,$carinfo[0],3) ;	//修改里程>130km

	}





	//用例：我要还车页面的28元套餐 。
	/**
	 * @depends test_get_car_info
	 */
	public function test_usecarfee($carinfo){
		$this->url = $this->get_url(use_car_fee,1);
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['ddid'] = $carinfo[3];
		$this->parms['action'] = action_backcar ;
		$this->parms['car_id'] = $carinfo[0];
		$this->parms['city'] = city;
		$this->parms['city_id'] = city_id;
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		$this->print_log('YzACarTest.php','test_usecarfee',$this->rlt,0) ;
		$this->assertEquals($this->rlt['data']['total_time'],total_time);
		$this->assertEquals($this->rlt['data']['distance'],distance) ;
		$this->assertEquals($this->rlt['data']['charge_distance'],charge_distance) ;
		$this->assertEquals($this->rlt['data']['charge_min'],charge_min) ;
		$this->assertEquals($this->rlt['data']['dyn_price'],dyn_price) ;
		$this->assertEquals($this->rlt['data']['activity_title'],activity_title) ;
		$this->assertEquals($this->rlt['data']['activity_money'],MIN_PRICE) ;
		$this->assertEquals($this->rlt['data']['activity_rule'],activity_rule) ;
		$this->assertEquals($this->rlt['data']['activity_type'],activity_type) ;
		$this->assertEquals($this->rlt['data']['charge_total'],MIN_PRICE) ;
		$this->assertEquals($this->rlt['data']['coupon']['id'],couponid) ;	//优惠券id
		$this->assertEquals($this->rlt['data']['coupon']['money'],coupon_money) ;	//优惠券金额0
		$this->assertEquals($this->rlt['data']['coupon']['name'],coupon_name) ;	//优惠券name

		/*
		"distance": 0,
        "total_time": "",
		"charge_total": "28.00",
		"charge_distance": "",
        "charge_min": "0.00",
        "dyn_price": 0,
         "activity_rule": "还车里程大于等于130km",
            "activity_type": 2,
            "activity_money": "28",
            "activity_title": "夜租车活动",
            "coupon": {
                "id": 0,
                "money": 0,
                "name": ""
            },
       */
	}





	//用例5，进行还车，老接口，迁移后需要修改
	/**
	 * @depends test_get_car_info
	 */
	public function test_backcar($carinfo){
		//print_r($carinfo) ;
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
		$this->print_log('YzACarTest.php','test_backcar',$this->rlt,0) ;
		$this->assertEquals($this->rlt['status'],0);
	}



	//查看结单后的行程详情
	/**
	 * @depends test_get_car_info
	 */
	public function test_yzAtourdetail($carinfo){
		$this->url = $this->get_url(tour_detail);
		$this->parms  = $this->get_parms();
		$this->parms['uid'] = self::$uid;
		$this->parms['key'] = self::$key;
		$this->parms['action'] = tour_action_detail;
		$this->parms['ddid'] = $carinfo[3];
		$this->parms['city'] = city;
		$this->parms['city_id'] = city_id;
		$this->rlt=$this->post_reslut($this->url,$this->parms);
		$this->print_log('YzACarTest.php','test_yzAtourdetail',$this->rlt,0) ;
		$this->assertEquals($this->rlt['data']['charge_total'],MIN_PRICE) ;
		$this->assertEquals($this->rlt['data']['youhui_yuee'],MIN_PRICE) ;
		$this->assertEquals($this->rlt['data']['charge_actual'],charge_actual) ;
		$this->assertEquals($this->rlt['data']['charge_activity']['money'],MIN_PRICE) ;	//优惠券id
		$this->assertEquals($this->rlt['data']['charge_activity']['type'],activity_type) ;	//优惠券金额0
		$this->assertEquals($this->rlt['data']['charge_activity']['title'],activity_title) ;	//优惠券name

		/*
		"charge_total": 28,
		"youhui_yuee": "28.00",
		"charge_actual": "0.00",
		"charge_activity": {
                "money": "28",
                "type": "2",
                "title": "夜租车活动"
          	}
        */
	}





	public  static function tearDownAfterClass(){
		//echo "是否修改回原来的时间：".self::$times ;
		update_time(0,self::$times)	;	//修改为原来的时间

	}

}