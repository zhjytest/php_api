<?php



function filepwd($filename,$tls='Source'){
	$bs = 'TestCases';
	$curpwd = getcwd() ;
	$dirpwd = explode($bs,$curpwd)[0];    //当前testcase的父目录
	//echo $dirpwd ;
	$dirname = $dirpwd."/".$tls ;
	$flname =  $dirname."/".$filename ;
	//echo $flname;
	return $flname ;
}




function generate_password($length = 6){ 
// 密码字符集，可任意添加你需要的字符 
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
	$password = ''; 
	for ( $i = 0; $i < $length; $i++ ){ 
		$password .= $chars[ mt_rand(0, strlen($chars) - 1) ]; 
	} 
	return $password; 
} 


