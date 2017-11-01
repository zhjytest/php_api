<?php

const API_URL = "http://123.57.217.108:6600/test.php?time=get_time" ;
const API_URL_UPDATE = "http://123.57.217.108:6600/test.php?uptime=" ;
const EXEC_TIME = "21:30:00" ;
const BEFORE_TIME = "20:00:00" ;
const AFTER_TIME = "10:00:00" ;
const CAR_MIN_KM = 45 ;
const CAR_MAX_KM = 90 ;
CONST BACK_MIN_KM = 110 ;
CONST BACK_MAX_KM = 130 ;
CONST MIN_PRICE = 28 ;
CONST MAX_PRICE = 48 ;
CONST TIP = "每日21点-次日9点，还车时里程符合条件，则可享受最低28元夜租价" ;
const SOC_LOW = 40 ;
CONST SOC_MIDDLE = 70 ;
CONST SOC_HIGH = 87 ;
CONST SOC_KM_LOW = 64 ;
CONST SOC_KM_MIDDLE = 112 ;
CONST SOC_KM_HIGH = 139 ;


//结算数据定义
const total_time = "" ;
const distance = 0 ;
const charge_distance = '' ;
const charge_min = "0.00" ;
const dyn_price = 0 ;
const activity_title = "夜租车活动" ;
const activity_rule = "还车里程大于等于130km" ;
const activity_rule1 = "还车里程大于等于110km 小于130km" ;
const activity_type = 2 ;
const couponid = 0 ;
const coupon_money = 0 ;
const coupon_name = "" ;
const charge_actual = "0.00" ;




//get方式的请求
function get_reslut($url){
	$ch = curl_init();
	//curl_setopt();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_HEADER,0);
	//curl_setopt($ch,CURLOPT_POST,1);
	//curl_setopt($ch,CURLOPT_POSTFIELDS,$post_params);
	$data = curl_exec($ch);
	curl_close($ch);
	$result=json_decode($data,true);
	//print_r($result);
	return $result;
}




function get_time(){
	$rlt = get_reslut(API_URL);
	$time = $rlt['cur_time'] ;
	return $time ;
}



function update_time($type,$curtime=null){
	if($type==0){	//修改为当前时间
		$urls = API_URL_UPDATE.$curtime;
		$rlt = get_reslut($urls) ;
	}elseif ($type==1) {	//修改为夜租符合的时间：21:30
		$urls = API_URL_UPDATE.EXEC_TIME;
		$rlt = get_reslut($urls) ;
	}elseif ($type==2) {	//修改为夜租前的时间20:00
		$urls = API_URL_UPDATE.BEFORE_TIME;
		$rlt = get_reslut($urls) ;
	}elseif ($type==3) {	//修改为次日夜租后的时间10:00
		$urls = API_URL_UPDATE.AFTER_TIME;
		$rlt = get_reslut($urls) ;
	}
	
}



function update_dist_remain($conn,$dist_remain,$carid,$flag=1){
	//flag = 1 为45~90 ，flag =2 为>110km , flag =3 为 >130km
	$soc_low = SOC_LOW ;
	$soc_km_low = SOC_KM_LOW ;
	$soc_middle = SOC_MIDDLE ;
	$soc_km_middle = SOC_KM_MIDDLE ;
	$soc_high = SOC_HIGH ;
	$soc_km_high = SOC_KM_HIGH ;
	if($flag==1){
		if($dist_remain<=CAR_MIN_KM || $dist_remain >=CAR_MAX_KM){
			$sql_vin = "select vin from 630_car where id = {$carid}" ;
			$rltdata = $conn->query($sql_vin);
			$vin = $rltdata->fetch_row()[0];
			$update_soc = "update 630_car_rt_test set soc = {$soc_low} where car_vin = {$vin}" ;
			$upbdate_dist = "update 630_car set dist_remain = {$soc_km_low} where id = {$carid}" ;
			$conn->query($update_soc) ;
			$conn->query($upbdate_dist) ;
		}
	}elseif ($flag==2) {	//>110km
		$sql_vin = "select vin from 630_car where id = {$carid}" ;
		$rltdata = $conn->query($sql_vin);
		$vin = $rltdata->fetch_row()[0];
		$update_soc = "update 630_car_rt_test set soc = {$soc_middle} where car_vin = {$vin}" ;
		$upbdate_dist = "update 630_car set dist_remain = {$soc_km_middle} where id = {$carid}" ;
		$conn->query($update_soc) ;
		$conn->query($upbdate_dist) ;
	}elseif($flag==3){	//>130Km
		$sql_vin = "select vin from 630_car where id = {$carid}" ;
		$rltdata = $conn->query($sql_vin);
		$vin = $rltdata->fetch_row()[0];
		$update_soc = "update 630_car_rt_test set soc = {$soc_high} where car_vin = {$vin}" ;
		$upbdate_dist = "update 630_car set dist_remain = {$soc_km_high} where id = {$carid}" ;
		$conn->query($update_soc) ;
		$conn->query($upbdate_dist) ;
	}

}
