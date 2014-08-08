<?php 
//*****************
//header('Content-type: text/html; charset=utf-8');
error_reporting(E_ALL & ~E_NOTICE); //所有錯誤中排除NOTICE提示
extract($_POST,EXTR_SKIP);extract($_GET,EXTR_SKIP);extract($_COOKIE,EXTR_SKIP);
$phpself=basename($_SERVER["SCRIPT_FILENAME"]);//被執行的文件檔名
date_default_timezone_set("Asia/Taipei");//時區設定
$time = time();//UNIX時間時區設定
$query_string=$_SERVER['QUERY_STRING'];
$GLOBALS['title'] = "js_M8FPBum70v9QRNg2";
////
//$handle=opendir("./../dbc/");
/*
$handle=opendir("./");
$cc=0; $dir_in=""; 
while(($file = readdir($handle))!==false) { 
	if(is_dir($file)){//只針對資料夾
		if($file=="."||$file == ".."){
			//什麼事都不做
		}else{
			if(preg_match('/^dbchat.+$/', $file)){
				$dir_in="./".$file."/";
				$cc=$cc+1;
			}else{
				if(preg_match('/^dbchat$/', $file)){
					die("资料夹未更名");
				}
			} //檢驗$query_string格式
		}
	}
} 
*/
////
//require './db_acpw2.php';//$time
//$tmp=$dir_in."db_ac.php";
$tmp="./db_ac.php";
require $tmp;
$mysql_host = $dbhost;
$mysql_user = $dbuser;
$mysql_pass = $dbpass;
$mysql_dbnm = $dbname;
//*****************
$chk=2;
if($query_string){//有query_string + 檔案存在
	if($query_string=="js"){
		header('Content-Type: application/javascript; charset=utf-8');
		echo "function tmp(){alert('test');}";
		$chk=1;
	}
	if($query_string=="css"){
		header("Content-type: text/css; charset=utf-8");
		echo "a {text-decoration:underline;}";
		$chk=1;
	}
	if($query_string=="png"){
		Header("Content-type: image/png");//指定文件類型為PNG
		$moji=date("ymd",$time);
		$moji=sprintf("%06d",$moji);
		$img = imageCreate(90,15);
		$black =imageColorAllocate($img, 0, 0, 255);
		$white = imageColorAllocate($img, 255, 255, 255);
		imageFill($img, 0, 0, $white);
		imagestring($img,5,0,0, $moji, $black);
		imagePng($img);
		imageDestroy($img);
		$chk=1;
	}
	if(preg_match("/^view[0-9]{6}/",$query_string)){
		header('Content-Type: application/javascript; charset=utf-8');
		//header("content-type: application/x-javascript; charset=utf-8"); 
		$ymd_set=substr($query_string,4,6);
		//echo $title_set;
		$x=view($mysql_host,$mysql_user,$mysql_pass,$mysql_dbnm); 
		echo $x[0];
		$chk=0;
	}
	if($query_string=="view"){
		header('Content-Type: application/javascript; charset=utf-8');
		//header("content-type: application/x-javascript; charset=utf-8"); 
		$x=view($mysql_host,$mysql_user,$mysql_pass,$mysql_dbnm); 
		echo $x[0];
		$chk=0;
	}
	if($chk == 1){
		$rec_x = rec($mysql_host,$mysql_user,$mysql_pass,$mysql_dbnm); //紀錄來源 //回傳紀錄檔行數
		$rec_x_0=$rec_x[0]; //輸入的字串
		$rec_x_1=$rec_x[1]; //計數器
		$rec_x_2=$rec_x[2]; //tbnm
		$rec_x = print_r($rec_x,true);
		$rec_x ="<pre>$rec_x</pre>";
		//echo $rec_x;//測試用
	}
}
if($chk == 2){
	header("content-Type: text/html; charset=utf-8; charset=utf-8"); //語言強制
	echo "測試";
	exit;
}
//**********
function newtable($t){//資料表格式
	$sql = "CREATE TABLE IF NOT EXISTS `$t`
	(
	`date` varchar(255),
	`user_ip` varchar(255) ,
	`ymd` varchar(255) ,
	`user_from` varchar(255),
	`arg1` varchar(255),
	`arg2` varchar(255),
	`arg3` varchar(255),
	`auto_time` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`auto_id` INT NOT NULL AUTO_INCREMENT,
	PRIMARY KEY ( auto_id )
	)ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
	return $sql;
}
//**********
function view($a,$b,$c,$d){
	$time=$GLOBALS['time'];
	$mysql_host=$a;
	$mysql_user=$b;
	$mysql_pass=$c;
	$mysql_dbnm=$d;
	
	//**********連結資料庫//
	$con = mysql_connect($mysql_host, $mysql_user, $mysql_pass);//連結資料庫
	if(mysql_error()){die(mysql_error());}//有錯誤就停止 //mysql_error()
	mysql_query("SET time_zone='+8:00';",$con);
	mysql_query("SET CHARACTER_SET_database='utf8'",$con);
	mysql_query("SET NAMES 'utf8'"); 
	// ^^加在mysql_select_db之前
	$tmp=mysql_select_db($mysql_dbnm, $con);//選擇資料庫
	if(mysql_error()){die(mysql_error());}//有錯誤就停止 //mysql_error()
	//**********連結資料庫//*
	if($GLOBALS['ymd_set']){
		$ymd = $GLOBALS['ymd_set'];
	}else{
		$ymd = date("ymd",$time);
	}
	$title=$GLOBALS['title'];
	$sql = "SELECT * FROM `$title` WHERE `ymd`='$ymd' ORDER BY `auto_time` DESC";//取得資料庫總筆數
	$result = mysql_query($sql); //mysql_list_tables($dbname)
	if(mysql_error()){die(mysql_error());}//有錯誤就停止 //mysql_error()
	$max = mysql_num_rows($result);//取得資料庫總筆數
	$cc=0;$str_tmp='';
	$str_tmp.=$title."\t".$max."\t".$ymd."\n";
	while($row = mysql_fetch_array($result)){//將範圍內的資料列出
		$str_tmp.= $row['date'];
		$str_tmp.= "\t";
		$str_tmp.= $row['ymd'];
		$str_tmp.= "\t";
		$str_tmp.= $cc;
		$str_tmp.= "\n";
		$str_tmp.= "\t";
		$str_tmp.= $row['user_ip'];
		$str_tmp.= "\n";
		$str_tmp.= "\t";
		$str_tmp.= $row['user_from'];
		$str_tmp.= "\n";
		$cc=$cc+1;
	}
	$x[0]=$str_tmp;
	return $x;
}
//**********
function rec($a,$b,$c,$d){
	$time=$GLOBALS['time'];
	$mysql_host=$a;
	$mysql_user=$b;
	$mysql_pass=$c;
	$mysql_dbnm=$d;
	
	//**********連結資料庫
	$con = mysql_connect($mysql_host, $mysql_user, $mysql_pass);//連結資料庫
	if(mysql_error()){die("");}else{}//讀取失敗則停止 //mysql_error()
	mysql_query("SET time_zone='+8:00';",$con);
	mysql_query("SET CHARACTER_SET_database='utf8'",$con);
	mysql_query("SET NAMES 'utf8'"); 
	// ^^加在mysql_select_db之前
	$tmp=mysql_select_db($mysql_dbnm, $con);//選擇資料庫
	if(mysql_error()){die("");}else{}//讀取失敗則停止 //mysql_error()
	//
	//$tmp=mysql_query("DROP TABLE IF EXISTS `$title`",$con);
	//if(mysql_error()){die(mysql_error());}//有錯誤就停止
	//**********連結資料庫
	$title=$GLOBALS['title'];
	$sql="SHOW TABLE STATUS";
	$result = mysql_query($sql); //mysql_list_tables($dbname)
	if(mysql_error()){die("");}else{}//讀取失敗則停止
	$cc=1;
	while($row = mysql_fetch_row($result)){
		if($row[0]==$title){$cc=0;};//有找到叫XXX的table
	}
	//isset($row[0]);
	if($cc){//建立預設的表格
		$sql=newtable($title); // 自訂函式
		$result=mysql_query($sql,$con);
		if(mysql_error()){die("");}else{}//讀取失敗則停止
	}
	//**********連結資料庫
	//舊版格式相容
	if(0){
		$sql = "ALTER TABLE `$title` CHANGE `user_ip2` `ymd` varchar(255)";// 
		$result = mysql_query($sql); 
	}
	$date=date("Y-m-d H:i:s",$time);
	$ymd=date("ymd",$time);
	$user_ip = ($HTTP_X_FORWARDED_FOR)?$_SERVER[HTTP_X_FORWARDED_FOR]:$_SERVER[REMOTE_ADDR];
	$user_ip = gethostbyaddr($user_ip);
	if(isset($_SERVER['HTTP_REFERER'])){
		$user_from=$_SERVER['HTTP_REFERER'];
	}else{
		$user_from="不明";
	}

	$sql="INSERT INTO `$title` ( date, user_ip, ymd, user_from)
	VALUES ('$date','$user_ip','$ymd','$user_from')";
	$result=mysql_query($sql,$con);
	if(mysql_error()){die("");}else{}//讀取失敗則停止
	//**********連結資料庫
	$sql = "SELECT * FROM `$title` ORDER BY `auto_time` DESC";//取得資料庫總筆數
	$result = mysql_query($sql,$con);
	if(mysql_error()){die("");}else{}//讀取失敗則停止
	////檢查page範圍
	$max = mysql_num_rows($result);//取得資料庫總筆數
	$x[0] = "$date,$user_ip,$user_ip2,$user_from";
	$x[1] = "$max";
	$x[2] = "$title";
	return $x;
}
?> 
