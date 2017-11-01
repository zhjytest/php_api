<?php





//查询tour_id
function get_tour_id($conn,$uid){
	//select * from 630_tour where customer_id = 261934 ORDER BY id desc limit 1;
	$sql = "select id from 630_tour where customer_id = '{$uid}' ORDER BY id desc limit 1" ;
	$info = $conn->query($sql);
	$data = $info->fetch_row()[0];
	return $data ;

}


//查询用户个人优惠券
function get_user_code($conn,$uid){
	//select code from 630_customer where  id = 261873 ;
	$sql = "select code from 630_customer where  id = {$uid} " ;
	$info = $conn->query($sql);
	$data = $info->fetch_row()[0];
	return $data ;

}